<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\OrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/v1/orders', function () {
    return "Procurement Micro Service V 1.0 ".app()->version();
});

Route::prefix('v1/orders')->controller(OrderController::class)->middleware(['configure_tenant_db','locale'])->group(function () {
    // return "Procurement Micro Service V 1.0";
    Route::get('/orders', 'index');
    Route::post('/orders', 'store');
    Route::get('/orders/{id}', 'show');
    Route::put('/orders/{id}', 'update');

    Route::put('/orders/orders-update/{id}', 'updateField');

    Route::patch('/orders/{id}', 'update');
    Route::delete('/orders/{id}', 'destroy');

    Route::post('/orders/bypo', 'bypo');
    Route::post('/orders/send', 'sendOrders');
});
