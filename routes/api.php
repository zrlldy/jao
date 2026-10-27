<?php

use App\Http\Controllers\ApplicationAnswerController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\ApplicationInvitationController;
use App\Http\Controllers\ApplicationSubmisionController;
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
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

Route::middleware(['jao.key', 'web'])->group(function () {
    Route::apiResources([
        'departments' => DepartmentController::class,
        'employment-types' => EmploymentTypeController::class,
        'locations' => LocationController::class,
        'job-hirings' => JobHiringController::class,
        'form-templates' => FormTemplateController::class,
    ]);

    Route::apiResource('job-hirings.form-versions', JobFormVersionAssignmentController::class)
        ->only(['index', 'store', 'destroy']);

    Route::apiResource('job-hirings.invitations', ApplicationInvitationController::class)
        ->only(['store']);

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

    Route::prefix('application-invitations/{token}')->name('application-invitations.')->group(function () {
        Route::get('form', [ApplicationFormController::class, 'index'])->name('form.show');
        Route::post('submissions', [ApplicationSubmisionController::class, 'store'])->name('submissions.store');
    });

    Route::apiResource('applications', ApplicationController::class)->only(['index', 'show']);
})->withoutMiddleware(PreventRequestForgery::class);
