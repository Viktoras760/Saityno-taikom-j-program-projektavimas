<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ReservationController;


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

//User routes
Route::post('user/', [UserController::class, 'register']);
Route::get('user/sorted', [UserController::class, 'getAllUncomfirmed']);
Route::put('user/{id}/apis?', [UserController::class, 'confirmRegistrationRequest']);
Route::patch('user/{id}', [UserController::class, 'declineRegistrationRequest']);
Route::get('user', [UserController::class, 'getAllUsers']);
Route::delete('user/{id}', [UserController::class, 'deleteUser']);
Route::put('user/{id}', [UserController::class, 'updateUser']);

//School routes
Route::post('school/add', [SchoolController::class, 'addSchool']);
Route::put('school/update/{id}', [SchoolController::class, 'updateSchool']);
Route::get('school/get/{id}', [SchoolController::class, 'getSchool']);
Route::get('school/getAll', [SchoolController::class, 'getAllSchools']);
Route::delete('school/delete/{id}', [SchoolController::class, 'deleteSchool']);

//Floor routes
Route::post('school/{id}/floor/add', [FloorController::class, 'addFloor']);
Route::put('school/{idSchool}/floor/update/{idFloor}', [FloorController::class, 'updateFloor']);
Route::get('school/{idSchool}/floor/get/{idFloor}', [FloorController::class, 'getFloor']);
Route::delete('school/{idSchool}/floor/delete/{idFloor}', [FloorController::class, 'deleteFloor']);
Route::get('school/{idSchool}/floor/getBySchool', [FloorController::class, 'getFloorBySchool']);

//Classes routes
Route::post('school/{idSchool}/floor/{idFloor}/classroom/add', [ClassroomController::class, 'addClassroom']);
Route::put('school/{idSchool}/floor/{idFloor}/classroom/update/{idClassroom}', [ClassroomController::class, 'updateClassroom']);
Route::get('school/{idSchool}/floor/{idFloor}/classroom/get/{idClassroom}', [ClassroomController::class, 'getClassroom']);
Route::delete('school/{idSchool}/floor/{idFloor}/classroom/delete/{idClassroom}', [ClassroomController::class, 'deleteClassroom']);
Route::get('school/{idSchool}/floor/{idFloor}/classroom/getByFloor', [ClassroomController::class, 'getClassroomByFloor']);

//Reservation routes
Route::get('school/{idSchool}/floor/{idFloor}/classroom/{idClassroom}/reservation/get/{id}', [ReservationController::class, 'getReservation']);
Route::post('school/{idSchool}/floor/{idFloor}/classroom/{idClassroom}/reservation/add', [ReservationController::class, 'addReservation']);