<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;

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


Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::put('/profile/update', 'update')->name('update');
        Route::get('/profile', 'show')->name('show');
        Route::delete('/profile/delete', 'destroy')->name('delete');
    });

    Route::middleware('only.admin')->group(function () {

        Route::controller(UserController::class)->name('user.')->group(function () {
            Route::put('/user/{id}/update', 'update')->name('update');
            Route::get('/users', 'index')->name('index');
            Route::get('/user/{id}', 'show')->name('show');
            Route::post('/user/create', 'create')->name('create');
        });

        Route::controller(CompanyController::class)->name('company.')->group(function () {
        });
    });
});
