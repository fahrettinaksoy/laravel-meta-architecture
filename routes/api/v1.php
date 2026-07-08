<?php

use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('validate.module')->group(function () {
    Route::get('{path}/{id}', [CommonController::class, 'show'])->where('id', '[0-9]+')->where('path', '.*');
    Route::get('{path}', [CommonController::class, 'index'])->where('path', '.*');

    Route::middleware('throttle:api-write')->group(function () {
        Route::put('{path}/{id}', [CommonController::class, 'update'])->where('id', '[0-9]+')->where('path', '.*');
        Route::patch('{path}/{id}', [CommonController::class, 'patch'])->where('id', '[0-9]+')->where('path', '.*');
        Route::delete('{path}/{id}', [CommonController::class, 'destroy'])->where('id', '[0-9]+')->where('path', '.*');
        Route::post('{path}', [CommonController::class, 'store'])->where('path', '.*');
    });
});
