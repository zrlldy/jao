<?php

use App\Http\Controllers\ApplicationInvitationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmploymentTypeController;
use App\Http\Controllers\FormSectionController;
use App\Http\Controllers\FormTemplateController;
use App\Http\Controllers\FormVersionController;
use App\Http\Controllers\JobHiringController;
use App\Http\Controllers\LocationController;
use App\Models\FormSection;
use App\Models\FormTemplate;
use App\Models\FormVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\patch;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::middleware('jao.key')->group(function () {

    // Department API
    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::get('/{department}', [DepartmentController::class, 'show']);
        Route::patch('/{department}', [DepartmentController::class, 'update']);
        Route::delete('/{department}', [DepartmentController::class, 'destroy']);
    });


    // Employment Type API
    Route::prefix('employment-types')->group(function () {
        Route::get('/', [EmploymentTypeController::class, 'index']);
        Route::post('/', [EmploymentTypeController::class, 'store']);
        Route::get('/{employmentType}', [EmploymentTypeController::class, 'show']);
        Route::patch('/{employmentType}', [EmploymentTypeController::class, 'update']);
        Route::delete('/{employmentType}', [EmploymentTypeController::class, 'destroy']);
    });


    // Location API
    Route::prefix('locations')->group(function () {
        Route::get('/', [LocationController::class, 'index']);
        Route::post('/', [LocationController::class, 'store']);
        Route::get('/{location}', [LocationController::class, 'show']);
        Route::patch('/{location}', [LocationController::class, 'update']);
        Route::delete('/{location}', [LocationController::class, 'destroy']);
    });


    // Job Hiring API
    Route::prefix('job-hirings')->group(function () {
        Route::get('/', [JobHiringController::class, 'index']);
        Route::post('/', [JobHiringController::class, 'store']);

        Route::patch(
            '/{jobHiring}/form-versions',
            [JobHiringController::class, 'attachFormVersion']
        );

        Route::get('/{jobHiring}', [JobHiringController::class, 'show']);
        Route::patch('/{jobHiring}', [JobHiringController::class, 'update']);
        Route::delete('/{jobHiring}', [JobHiringController::class, 'destroy']);
    });


    // Form Template API
    Route::prefix('form-templates')->group(function () {
        Route::get('/', [FormTemplateController::class, 'index']);
        Route::post('/', [FormTemplateController::class, 'store']);
        Route::get('/{formTemplate}', [FormTemplateController::class, 'show']);
        Route::patch('/{formTemplate}', [FormTemplateController::class, 'update']);
        Route::delete('/{formTemplate}', [FormTemplateController::class, 'destroy']);
    });

    // Nested API

    Route::scopeBindings()->group(function () {

        // Form Version API
        Route::prefix('form-templates/{formTemplate}/form-versions')->group(function () {
            Route::get('/', [FormVersionController::class, 'index']);
            Route::post('/', [FormVersionController::class, 'store']);
        });

        Route::prefix('form-versions')->group(function () {
            Route::get('/{formVersion}', [FormVersionController::class, 'show']);
            Route::patch('/{formVersion}', [FormVersionController::class, 'update']);
            Route::delete('/{formVersion}', [FormVersionController::class, 'destroy']);
        });

        Route::prefix('form-versions/{formVersion}/form-sections')->group(function () {
            Route::get('/', [FormSectionController::class, 'index']);
            Route::post('/', [FormSectionController::class, 'store']);
        });

        Route::prefix('form-sections')->group(function () {
            Route::get('/{formSection}', [FormSectionController::class, 'show']);
            Route::patch('/{formSection}', [FormSectionController::class, 'update']);
            Route::delete('/{formSection}', [FormSectionController::class, 'destroy']);
        });
    });


    // Application Invitation API
    Route::prefix('invitations')->group(function () {
        Route::post('/', [
            ApplicationInvitationController::class,
            'store'
        ]);
    });
});
