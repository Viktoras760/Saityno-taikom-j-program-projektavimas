<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('users/register', [UserController::class, 'register']);
Route::get('users/uncomfirmed', [UserController::class, 'getAllUncomfirmed']);
Route::put('users/confirm/{id}', [UserController::class, 'confirmRegistrationRequest']);
Route::put('users/decline/{id}', [UserController::class, 'declineRegistrationRequest']);
Route::get('users/get', [UserController::class, 'getAllUsers']);
Route::delete('users/delete/{id}', [UserController::class, 'deleteUser']);