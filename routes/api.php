<?php

use App\Http\Controllers\ApplicationAnswerController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\ApplicationInvitationController;
use App\Http\Controllers\ApplicationSubmissionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmploymentTypeController;
use App\Http\Controllers\FormFieldController;
use App\Http\Controllers\FormFieldOptionController;
use App\Http\Controllers\FormSectionController;
use App\Http\Controllers\FormTemplateController;
use App\Http\Controllers\FormVersionController;
use App\Http\Controllers\JobFormVersionAssignmentController;
use App\Http\Controllers\JobHiringController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('public/job-hirings')->name('public.job-hirings.')->middleware('throttle:public-job-hirings')->group(function () {
    Route::get('/', [JobHiringController::class, 'publicIndex'])->name('index');
    Route::get('{job_hiring}', [JobHiringController::class, 'publicShow'])->name('show');
});

Route::prefix('application-invitations/{token}')->name('application-invitations.')->group(function () {
    Route::get('form', [ApplicationFormController::class, 'index'])
        ->middleware('throttle:public-application-form')
        ->name('form.show');

    Route::post('submissions', [ApplicationSubmissionController::class, 'store'])
        ->middleware('throttle:public-application-submission')
        ->name('submissions.store');
});

Route::middleware(['jao.key', 'jao.hmac', 'web'])->group(function () {
    Route::apiResources([
        'departments' => DepartmentController::class,
        'employment-types' => EmploymentTypeController::class,
        'locations' => LocationController::class,
        'job-hirings' => JobHiringController::class,
        'form-templates' => FormTemplateController::class,
    ]);

    Route::apiResource('job-hirings.form-versions', JobFormVersionAssignmentController::class)
        ->only(['index', 'store', 'destroy']);

    Route::post('job-hirings/{job_hiring}/invitations', [ApplicationInvitationController::class, 'store'])
        ->middleware('throttle:private-invitation-create')
        ->name('job-hirings.invitations.store');

    Route::scopeBindings()->group(function () {
        Route::apiResource('form-templates.form-versions', FormVersionController::class)->shallow();

        Route::apiResource('form-versions.form-sections', FormSectionController::class)->shallow();

        Route::apiResource('form-sections.form-fields', FormFieldController::class)->shallow();

        Route::apiResource('form-fields.options', FormFieldOptionController::class)
            ->only(['index', 'store']);

        Route::apiResource('form-field-options', FormFieldOptionController::class)
            ->only(['show', 'update', 'destroy']);

        Route::apiResource('applications.application-answers', ApplicationAnswerController::class)
            ->only(['index', 'show']);
    });

    Route::patch('form-sections/{form_section}/position', [FormSectionController::class, 'sectionReorder'])
        ->name('form-sections.position.update');

    Route::patch('form-fields/{form_field}/position', [FormFieldController::class, 'formFieldReorder'])
        ->name('form-fields.position.update');

    Route::patch('form-field-options/{form_field_option}/position', [FormFieldOptionController::class, 'formFieldOptionsReorder'])
        ->name('form-field-options.position.update');

    Route::apiResource('applications', ApplicationController::class)->only(['index', 'show']);
});
