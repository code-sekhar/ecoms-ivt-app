<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\BrandController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
//Get User Profile
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'getProfile']);
    Route::post('/addprofile', [UserProfileController::class, 'addProfile']);
    Route::post('/updateprofile', [UserProfileController::class, 'updateProfile']);
    Route::post('/logout', [UserController::class, 'logout']);
});

//Only admin can access this route
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/admin/addbrand', [BrandController::class, 'addBrand']);
    Route::post('/admin/updatebrand/{id}', [BrandController::class, 'updateBrand']);
    Route::delete('/admin/deletebrand/{id}', [BrandController::class, 'deleteBrand']);
});

//all users can access this route
Route::get('/brands', [BrandController::class, 'getAllBrands']);
Route::get('/brand/{id}', [BrandController::class, 'getBrand']);
