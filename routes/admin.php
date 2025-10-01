<?php
/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 21:55
 */

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Middleware\AdminAreaMiddleware;

Route::middleware([AdminAreaMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('projects', ProjectController::class);
        Route::resource('tasks', TaskController::class);
    });

