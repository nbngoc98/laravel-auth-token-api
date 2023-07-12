<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('signup', [AuthController::class, 'signup'])->name('signup');
Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('email.verify');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::get('me', [AuthController::class, 'getAuthenticatedUser'])->middleware('auth:sanctum')->name('user');
Route::post('password/email', [AuthController::class, 'sendPasswordResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
Route::post('password/verify/{token}', [AuthController::class, 'verifyTokenUser'])->name('password.email.check');
Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::middleware(['auth:sanctum', 'verified.api'])->prefix('users')->controller(UserController::class)->name('users.')->group(function () {
    Route::get('/', function () {
        return response()->json(User::all());
    })->name('index');
});
