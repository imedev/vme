<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'products'], function () {
    // import
    Route::get("/import",[\App\Http\Controllers\ProductController::class,"importProducts"]);
    Route::get("/batchInProgress",[\App\Http\Controllers\ProductController::class,"batchInProgress"]);

    // CRUD
    Route::get("",[\App\Http\Controllers\ProductController::class,"getProducts"]);
    Route::post("",[\App\Http\Controllers\ProductController::class,"saveProduct"]);
    Route::post("/{id}",[\App\Http\Controllers\ProductController::class,"updateProduct"]);
    Route::delete("/{id}",[\App\Http\Controllers\ProductController::class,"deleteProduct"]);

    // filters
    Route::get("/filterProducts",[\App\Http\Controllers\ProductController::class,"filterProducts"]);
    Route::get("/paginateProducts",[\App\Http\Controllers\ProductController::class,"paginateProducts"]);
    Route::get("/searchProducts",[\App\Http\Controllers\ProductController::class,"searchProducts"]);
    Route::get("/sortProducts",[\App\Http\Controllers\ProductController::class,"sortProducts"]);

    // Mail storeStaff
    Route::get("/mailProducts",[\App\Http\Controllers\ProductController::class,"mailProducts"]);
    // Notifications
    Route::get("/notifications",[\App\Http\Controllers\ProductController::class,"getNotifications"]);



});
