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
    Route::post('/login', 'login')->name('login')->middleware('login.throttle');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('/profile', 'show')->name('show');
        Route::put('/profile', 'update')->name('update');
        Route::delete('/profile', 'destroy')->name('delete');
        Route::get('/profile/companies', 'getCompanies')->name('companies');
    });

    Route::middleware('only.admin')->group(function () {

        Route::controller(UserController::class)->name('user.')->group(function () {
            Route::get('/users', 'index')->name('index');
            Route::post('/user', 'create')->name('create');
            Route::get('/user/{id}', 'show')->name('show');
            Route::put('/user/{id}', 'update')->name('update');
            Route::delete('/user/{id}', 'destroy')->name('update');
            Route::get('/user/{id}/companies', 'getCompanies')->name('companies');
        });

        Route::controller(CompanyController::class)->name('company.')->group(function () {
            Route::get('/companies', 'index')->name('index');
            Route::post('/company', 'create')->name('create');
            Route::get('/company/{id}', 'show')->name('show');
            Route::put('/company/{id}', 'update')->name('update');
            Route::delete('/company/{id}', 'destroy')->name('delete');

            // Company's users routes
            Route::get('/company/{id}/users', 'getUsers')->name('users');
            Route::post('/company/{id}/users', 'addUsers')->name('users.add');
            Route::delete('/company/{id}/users', 'removeUsers')->name('users.remove');
        });
    });
});
