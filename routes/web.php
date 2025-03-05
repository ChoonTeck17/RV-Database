<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ExcelController;

Route::get('/', [ExcelController::class, 'showNPSUpload'])->name('upload.page');

// Route::post('/import', [FileController::class, 'import']); // Import file
// Route::get('/data', [ExcelController::class, 'showData'])->name('data.show');
// Route::get('/download/{file}', [FileController::class, 'download']); // Download file

// Route::get('/upload', [ExcelController::class, 'showData']); // Ensure no duplicate
Route::get('/rfm-upload', [ExcelController::class, 'showRFMUpload'])->name('rfm.upload.excel');
Route::post('/rfm-upload', [ExcelController::class, 'uploadRFM'])->name('rfm.upload.process');

Route::get('/raw-upload', [ExcelController::class, 'showRAWUpload'])->name('raw.upload.excel');
Route::post('/raw-upload', [ExcelController::class, 'uploadRAW'])->name('raw.upload.process');

Route::get('/nps-upload', [ExcelController::class, 'showNPSUpload'])->name('nps.upload.show');
Route::post('/nps-upload', [ExcelController::class, 'uploadNPS'])->name('nps.upload.process');

Route::get('/download-excel', [ExcelController::class, 'downloadExcel'])->name('download.excel');
Route::get('/download-pdf', [ExcelController::class, 'downloadPDF'])->name('download.pdf');
