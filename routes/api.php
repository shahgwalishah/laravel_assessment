<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
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

/* admin send invitation link to user route */
Route::post('admin/invite/user', [AdminController::class , 'sendInvitationEmailToUser']);


/* user login and register api routes */
Route::post('login', [UserController::class , 'login'])->name('login');
Route::post('register', [UserController::class , 'register'])->name('register');
Route::post('verify/email', [UserController::class , 'verifyEmail'])->name('verifyEmail');


Route::middleware('auth:api')->group(function () {
    Route::post('update/profile', [UserController::class , 'updateProfile'])->name('updateProfile');
});
