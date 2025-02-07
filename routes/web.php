<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/import', [FileController::class, 'import']); // Import file
Route::get('/download/{file}', [FileController::class, 'download']); // Download file
