<?php

use App\Http\Controllers\LectureController;
use App\Http\Controllers\PresencesController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/lecture/create', [LectureController::class, 'create']);
    Route::post('/lecture/start', [LectureController::class, 'start']);
    Route::post('/lecture/stop', [LectureController::class, 'stop']);
    Route::get('/lecture/students', [LectureController::class, 'getStudents']);
    Route::get('/lectures', [LectureController::class, 'getLectures']);

    Route::post('/presences', [PresencesController::class, 'add']);
    Route::get('/presences', [PresencesController::class, 'getPresences']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
