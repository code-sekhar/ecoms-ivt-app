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
    Route::post('/addbrand', [BrandController::class, 'addBrand']);
});
