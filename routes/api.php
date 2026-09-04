<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmploymentTypeController;
use App\Http\Controllers\JobHiringController;
use App\Http\Controllers\LocationController;
use App\Models\FormTemplate;
use App\Models\FormVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\patch;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::middleware('jao.key')->group(function () {


    Route::prefix('/department')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::get('/{department}', [DepartmentController::class, 'show']);
        Route::patch('/{department}', [DepartmentController::class, 'update']);
        Route::delete('/{department}', [DepartmentController::class, 'destroy']);
    });

    Route::prefix('/employmentType')->group(function () {
        Route::get('/', [EmploymentTypeController::class, 'index']);
        Route::post('/', [EmploymentTypeController::class, 'store']);
        Route::get('/{employmentType}', [EmploymentTypeController::class, 'show']);
        Route::patch('/{employmentType}', [EmploymentTypeController::class, 'update']);
        Route::delete('/{employmentType}', [EmploymentTypeController::class, 'destroy']);
    });


    Route::prefix('/location')->group(function () {
        Route::get('/', [LocationController::class, 'index']);
        Route::post('/', [LocationController::class, 'store']);
        Route::get('/{location}', [LocationController::class, 'show']);
        Route::patch('/{location}', [LocationController::class, 'update']);
        Route::delete('/{location}', [LocationController::class, 'delete']);
    });

    Route::prefix('/jobhiring')->group(function () {
        Route::get('/', [JobHiringController::class, 'index']);
        Route::post('/', [JobHiringController::class, 'store']);
        Route::get('/{jobHiring}', [JobHiringController::class, 'show']);
        Route::patch('/{jobHiring}', [JobHiringController::class, 'update']);
        Route::delete('/{jobHiring}', [JobHiringController::class, 'destroy']);
    });



    Route::prefix('/formTemplate')->group(function () {
        Route::get('/', [FormTemplate::class, 'index']);
        Route::post('/', [FormTemplate::class, 'store']);
        Route::get('/{formTemplate}', [FormTemplate::class, 'show']);
        Route::patch('/{formTemplate}', [FormTemplate::class, 'update']);
        Route::delete('/{formTemplate}', [FormTemplate::class, 'destroy']);
    });

    Route::prefix('/formVersion')->group(function () {
        Route::get('/', [FormVersion::class, 'index']);
        Route::post('/', [FormVersion::class, 'store']);
        Route::get('/{formVersion}', [FormVersion::class, 'show']);
        Route::patch('/{formVersion}', [FormVersion::class, 'update']);
        Route::delete('/{formVersion}', [FormVersion::class, 'destroy']);
    });
});
