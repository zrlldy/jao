<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApplicationAnswerResources;
use App\Models\Application;
use App\Models\ApplicationAnswer;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Application $application)
    {
        $applicationAnswers = $application
            ->applicationAnswers()
            ->with(['application', 'formField'])
            ->select([
                'id',
                'application_id',
                'form_field_id',
                'value',
                'file_path',
                'file_name',
                'file_mime',
                'file_size',
            ])
            ->paginate(10);

        return ApplicationAnswerResources::collection($applicationAnswers);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application, ApplicationAnswer $applicationAnswer): ApplicationAnswerResources
    {
        return new ApplicationAnswerResources($applicationAnswer->load('application', 'formField'));
    }

    public function download(Application $application, ApplicationAnswer $applicationAnswer): StreamedResponse
    {
        abort_unless($applicationAnswer->application_id === $application->id, 404);
        abort_unless($applicationAnswer->file_path !== null
            && str_starts_with($applicationAnswer->file_path, 'application-answers/')
            && Storage::disk('local')->exists($applicationAnswer->file_path), 404);

        return Storage::disk('local')->download(
            $applicationAnswer->file_path,
            basename($applicationAnswer->file_name ?: 'document'),
            [
                'Content-Type' => $applicationAnswer->file_mime,
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}
