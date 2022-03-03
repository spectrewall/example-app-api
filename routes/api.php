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

    Route::controller(ProfileController::class)->prefix('/profile')->name('profile.')->group(function () {

        Route::get('/', 'show')->name('show');
        Route::put('/update', 'update')->name('update');
        Route::delete('/delete', 'destroy')->name('delete');
        Route::get('/companies', 'get_companies')->name('companies');
    });

    Route::middleware('only.admin')->group(function () {

        Route::controller(UserController::class)->name('user.')->group(function () {
            Route::get('/users', 'index')->name('index');

            Route::prefix('/user')->group(function () {

                Route::post('/create', 'create')->name('create');
                Route::prefix('/{id}')->group(function () {

                    Route::get('/', 'show')->name('show');
                    Route::put('/update', 'update')->name('update');
                    Route::delete('/delete', 'destroy')->name('update');
                    Route::get('/companies', 'get_companies')->name('show');
                });
            });
        });

        Route::controller(CompanyController::class)->name('company.')->group(function () {

            Route::get('/companies', 'index')->name('index');
            Route::post('/company/create', 'create')->name('create');

            Route::prefix('/company/{id}')->group(function () {

                Route::get('/', 'show')->name('show');
                Route::put('/update', 'update')->name('update');
                Route::delete('/delete', 'destroy')->name('update');

                Route::prefix('/users')->name('users.')->group(function () {

                    Route::get('/', 'get_users')->name('get');
                    Route::post('/add', 'add_users')->name('add');
                    Route::put('/remove', 'remove_users')->name('remove');
                });
            });
        });
    });
});
