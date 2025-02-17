<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ExcelController;


Route::get('/upload', function () {
    return view('upload', ['data' => []]);
});

Route::get('/', [ExcelController::class, 'showData'])->name('upload.page');

Route::post('/import', [FileController::class, 'import']); // Import file
Route::get('/data', [ExcelController::class, 'showData'])->name('data.show');
Route::get('/download/{file}', [FileController::class, 'download']); // Download file
// Route::post('/upload', [ExcelController::class, 'upload'])->name('upload.excel');
Route::get('/upload', [ExcelController::class, 'showData']);
Route::post('/upload', [ExcelController::class, 'upload'])->name('upload.excel');
Route::get('/download-excel', [ExcelController::class, 'downloadExcel'])->name('download.excel');
Route::get('/download-pdf', [ExcelController::class, 'downloadPDF'])->name('download.pdf');