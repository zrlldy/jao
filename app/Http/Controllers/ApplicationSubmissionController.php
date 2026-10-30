<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApplicationAnswerRequest;
use App\Models\Application;
use App\Models\ApplicationAnswer;
use App\Models\ApplicationInvitation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ApplicationSubmissionController extends Controller
{
    public function store(CreateApplicationAnswerRequest $request, string $token): JsonResponse
    {
        $data = $request->validated();
        $hashedToken = hash('sha256', $token);
        $storedFiles = [];

        try {
            DB::transaction(function () use ($data, $hashedToken, &$storedFiles): void {
                $applicationInvitation = ApplicationInvitation::query()
                    ->where('token', $hashedToken)
                    ->where('expired_at', '>=', today()->toDateString())
                    ->lockForUpdate()
                    ->whereNull('used_at')
                    ->whereHas('jobHiring', fn (Builder $query) => $query->publiclyVisible())
                    ->firstOrFail();

                $formVersion = $applicationInvitation->jobHiring->formVersions()
                    ->where('form_versions.id', $data['form_version_id'])
                    ->where('form_versions.id', $applicationInvitation->form_version_id)
                    ->where('form_versions.status', 'published')
                    ->first();

                if ($formVersion === null) {
                    throw ValidationException::withMessages([
                        'form_version_id' => 'The selected form is not available for this invitation.',
                    ]);
                }

                $fields = $formVersion->formSections()
                    ->with('formFields.formFieldOptions')
                    ->get()
                    ->flatMap(fn ($section) => $section->formFields)
                    ->keyBy('id');
                $answers = collect($data['answers']);

                $totalFileSize = 0;
                foreach ($answers as $index => $answer) {
                    $field = $fields->get($answer['form_field_id']);

                    if ($field === null) {
                        throw ValidationException::withMessages([
                            "answers.$index.form_field_id" => 'The selected field is not part of this form.',
                        ]);
                    }

                    if ($field->type === 'file') {
                        if (array_key_exists('value', $answer) || ! isset($answer['file'])) {
                            throw ValidationException::withMessages([
                                "answers.$index.file" => 'This question needs an uploaded file.',
                            ]);
                        }

                        $totalFileSize += $answer['file']->getSize();
                        if ($totalFileSize > 50 * 1024 * 1024) {
                            throw ValidationException::withMessages([
                                'answers' => 'The total upload size cannot exceed 50 MB.',
                            ]);
                        }

                        continue;
                    }

                    if (array_key_exists('file', $answer) || ! array_key_exists('value', $answer)) {
                        throw ValidationException::withMessages([
                            "answers.$index.value" => 'This question needs a text value.',
                        ]);
                    }

                    if ($field->is_required && blank($answer['value'])) {
                        throw ValidationException::withMessages([
                            "answers.$index.value" => 'This field is required.',
                        ]);
                    }

                    if ($answer['value'] !== null && in_array($field->type, ['select', 'radio', 'checkbox'], true)
                        && ! $field->formFieldOptions->contains('value', $answer['value'])) {
                        throw ValidationException::withMessages([
                            "answers.$index.value" => 'The selected option is invalid.',
                        ]);
                    }

                    if ($answer['value'] !== null && $field->type === 'date'
                        && Validator::make($answer, ['value' => 'date_format:Y-m-d'])->fails()) {
                        throw ValidationException::withMessages([
                            "answers.$index.value" => 'The date must use YYYY-MM-DD format.',
                        ]);
                    }
                }

                if ($fields->contains(fn ($field) => $field->is_required
                    && ! $answers->contains('form_field_id', $field->id))) {
                    throw ValidationException::withMessages([
                        'answers' => 'All required fields must have an answer.',
                    ]);
                }

                $submittedAt = now();
                $application = Application::create([
                    'application_invitation_id' => $applicationInvitation->id,
                    'job_hiring_id' => $applicationInvitation->job_hiring_id,
                    'form_version_id' => $formVersion->id,
                    'status' => 'on_review',
                    'submitted_at' => $submittedAt,
                ]);

                $answerRows = [];
                foreach ($answers as $answer) {
                    $answerId = (string) Str::uuid();
                    $row = [
                        'id' => $answerId,
                        'application_id' => $application->id,
                        'form_field_id' => $answer['form_field_id'],
                        'value' => $answer['value'] ?? null,
                        'file_path' => null,
                        'file_name' => null,
                        'file_mime' => null,
                        'file_size' => null,
                        'created_at' => $submittedAt,
                        'updated_at' => $submittedAt,
                    ];

                    if (isset($answer['file'])) {
                        $file = $answer['file'];
                        $extension = match ($file->getMimeType()) {
                            'application/pdf' => 'pdf',
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            default => throw new RuntimeException('Unsupported uploaded document type.'),
                        };
                        $name = pathinfo(basename(str_replace('\\', '/', $file->getClientOriginalName())), PATHINFO_FILENAME);
                        $name = trim(substr(preg_replace('/[^A-Za-z0-9_-]/', '_', $name), 0, 180), '_-') ?: 'document';
                        $path = $file->storeAs(
                            'application-answers/'.$application->id,
                            $answerId.'.'.$extension,
                            'local'
                        );
                        if ($path === false) {
                            throw new RuntimeException('Unable to store uploaded document.');
                        }

                        $storedFiles[] = $path;
                        $row['file_path'] = $path;
                        $row['file_name'] = $name.'.'.$extension;
                        $row['file_mime'] = $file->getMimeType();
                        $row['file_size'] = $file->getSize();
                    }

                    $answerRows[] = $row;
                }

                ApplicationAnswer::insert($answerRows);
                $applicationInvitation->used_at = $submittedAt;
                $applicationInvitation->save();
            });
        } catch (Throwable $exception) {
            if ($storedFiles !== [] && ! Storage::disk('local')->delete($storedFiles)) {
                Log::error('Could not remove files from a failed application submission.', ['paths' => $storedFiles]);
            }

            throw $exception;
        }

        return response()->json(['message' => 'Application submitted successfully']);
    }
}
