<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

// Route::get('/test', function () {
//     return response()->json(['message' => 'API working']);
// });

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class,'show']);
Route::patch('/orders/{order}/cancel', [OrderController::class,'cancel']);