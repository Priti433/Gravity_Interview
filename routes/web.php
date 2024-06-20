<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Api\DiwaliSaleController;

Route::post('/saleRule1', [DiwaliSaleController::class, 'saleRule1']);
Route::post('/saleRule2', [DiwaliSaleController::class, 'saleRule2']);
Route::post('/saleRule3', [DiwaliSaleController::class, 'saleRule3']);