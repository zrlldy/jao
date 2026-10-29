<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApplicationAnswerRequest;
use App\Models\Application;
use App\Models\ApplicationAnswer;
use App\Models\ApplicationInvitation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApplicationSubmissionController extends Controller
{
    public function store(CreateApplicationAnswerRequest $request, string $token): JsonResponse
    {
        $data = $request->validated();
        $hashedToken = hash('sha256', $token);

        DB::transaction(function () use ($data, $hashedToken): void {
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

            foreach ($answers as $index => $answer) {
                $field = $fields->get($answer['form_field_id']);

                if ($field === null) {
                    throw ValidationException::withMessages([
                        "answers.$index.form_field_id" => 'The selected field is not part of this form.',
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

            $answerRows = $answers->map(fn (array $answer): array => [
                'id' => (string) Str::uuid(),
                'application_id' => $application->id,
                'form_field_id' => $answer['form_field_id'],
                'value' => $answer['value'],
                'created_at' => $submittedAt,
                'updated_at' => $submittedAt,
            ])->all();

            ApplicationAnswer::insert($answerRows);
            $applicationInvitation->used_at = $submittedAt;
            $applicationInvitation->save();
        });

        return response()->json(['message' => 'Application submitted successfully']);
    }
}
