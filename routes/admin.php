<?php
/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 30.09.25
 * Time: 21:55
 */

Route::get('admin', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');
