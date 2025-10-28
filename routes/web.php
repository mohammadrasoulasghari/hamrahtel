<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComparisonController;

Route::get('/', function () {
    return view('index');
});

Route::post('/comparison/upload', [ComparisonController::class, 'upload'])->name('comparison.upload');
Route::get('/comparison/result/{id}', [ComparisonController::class, 'result'])->name('comparison.result');
