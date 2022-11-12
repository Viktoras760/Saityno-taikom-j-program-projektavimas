<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AuthController;


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
Route::post('users', [UserController::class, 'addUser']);
Route::patch('users/{id}', [UserController::class, 'declineRegistrationRequest']);
Route::get('users', [UserController::class, 'getAllUsers']);
Route::delete('users/{id}', [UserController::class, 'deleteUser']);
Route::put('users/{id}', [UserController::class, 'updateUser']);

//School routes
Route::post('schools', [SchoolController::class, 'addSchool']);
Route::put('schools/{id}', [SchoolController::class, 'updateSchool']);
Route::get('schools/{id}', [SchoolController::class, 'getSchool']);
Route::get('schools', [SchoolController::class, 'getAllSchools']);
Route::delete('schools/{id}', [SchoolController::class, 'deleteSchool']);

//Floor routes
Route::post('schools/{id}/floors', [FloorController::class, 'addFloor']);
Route::put('schools/{idSchool}/floors/{idFloor}', [FloorController::class, 'updateFloor']);
Route::get('schools/{idSchool}/floors/{idFloor}', [FloorController::class, 'getFloor']);
Route::delete('schools/{idSchool}/floors/{idFloor}', [FloorController::class, 'deleteFloor']);
Route::get('schools/{idSchool}/floors', [FloorController::class, 'getFloorBySchool']);

//Classroom routes
Route::post('schools/{idSchool}/floors/{idFloor}/classrooms', [ClassroomController::class, 'addClassroom']);
Route::put('schools/{idSchool}/floors/{idFloor}/classrooms/{idClassroom}', [ClassroomController::class, 'updateClassroom']);
Route::get('schools/{idSchool}/floors/{idFloor}/classrooms/{idClassroom}', [ClassroomController::class, 'getClassroom']);
Route::delete('schools/{idSchool}/floors/{idFloor}/classrooms/{idClassroom}', [ClassroomController::class, 'deleteClassroom']);
Route::get('schools/{idSchool}/floors/{idFloor}/classrooms', [ClassroomController::class, 'getClassroomByFloor']);

//Lesson routes
Route::get('schools/{idSchool}/floors/{idFloor}/classrooms/{idClassroom}/lessons/{id}', [LessonController::class, 'getLesson']);
Route::post('schools/{idSchool}/floors/{idFloor}/classrooms/{idClassroom}/lessons', [LessonController::class, 'addLesson']);
Route::post('schools/{idSchool}/floors/{idFloor}/classrooms/{idClassroom}/lessons/{id}', [LessonController::class, 'registerToLesson']);
Route::delete('schools/{idSchool}/floors/{idFloor}/classrooms/{idClassroom}/lessons/{id}', [LessonController::class, 'deleteLesson']);
Route::delete('user_lessons/{id}', [LessonController::class, 'unregisterFromLesson']);
Route::put('schools/{idSchool}/floors/{idFloor}/classrooms/{idClassroom}/lessons/{id}', [LessonController::class, 'updateLesson']);
Route::get('user_lessons/{id}', [LessonController::class, 'getUserLessons']);

//Auth routes
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login'])/*->name('login')*/;
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout'])/*->name('logout')*/;
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
//Route::get('token/{id}', [AuthController::class, 'getToken']);