<?php

use App\Http\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:sanctum'])
    ->name('api.')
    ->group(function () {
        Route::patch('tasks/{task}/completed', TaskController::class)
            ->name('tasks.completed');
    });
