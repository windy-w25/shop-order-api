<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class,'show']);
Route::patch('/orders/{order}/cancel', [OrderController::class,'cancel']);
Route::get('/reports/top-products', [ReportController::class,'topProducts']);