<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JobController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::get('/openapi.yaml', fn () => Response::file(base_path('docs/api/openapi.yaml'), [
    'Content-Type' => 'application/yaml',
]));

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', DashboardController::class);

    Route::get('/candidates/export', [CandidateController::class, 'export']);
    Route::post('/candidates/import', [CandidateController::class, 'import']);
    Route::apiResource('candidates', CandidateController::class);
    Route::apiResource('jobs', JobController::class);
    Route::apiResource('applications', ApplicationController::class)->except(['destroy']);
    Route::get('/application', [ApplicationController::class, 'index']);
});
