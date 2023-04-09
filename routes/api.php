<?php

use App\Http\Controllers\TaskController;
use Illuminate\Console\View\Components\Task;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get-available-coaches', [TaskController::class, 'getAvailableCoaches']);
Route::get('/available-slots', [TaskController::class, 'getAvailableSloats']);
Route::post('/book-slot', [TaskController::class, 'bookSlot']);
