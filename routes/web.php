<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComparisonController;

Route::get('/', function () {
    return view('index');
});

// Legacy upload (backward compatibility)
Route::post('/comparison/upload', [ComparisonController::class, 'upload'])->name('comparison.upload');

// New smart matching workflow (NO API - regular routes)
Route::post('/preview-columns', [ComparisonController::class, 'previewColumns'])->name('preview.columns');
Route::post('/validate-matching', [ComparisonController::class, 'validateMatching'])->name('validate.matching');
Route::post('/start-comparison', [ComparisonController::class, 'startComparison'])->name('start.comparison');
Route::get('/check-status/{id}', [ComparisonController::class, 'checkStatus'])->name('check.status');

// Results
Route::get('/comparison/result/{id}', [ComparisonController::class, 'result'])->name('comparison.result');
