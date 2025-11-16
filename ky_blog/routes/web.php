<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\DairyController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// --- LoginController ---
Route::get('/', [LoginController::class, 'loginGet']);
Route::post('/', [LoginController::class, 'loginPost']);

Route::get('/add', [LoginController::class, 'add']);
Route::post('/add', [LoginController::class, 'mailSend']);

Route::get('/code', [LoginController::class, 'inputCode']);
Route::post('/code', [LoginController::class, 'judgeCode']);

Route::get('/create', [LoginController::class, 'inputAccount']);
Route::post('/create', [LoginController::class, 'create']);

Route::get('/kiyaku', [LoginController::class, 'kiyaku']);
Route::post('/kiyaku', [LoginController::class, 'kiyakuPost']);

// --- MainController ---
Route::get('/main', [MainController::class, 'main']);
Route::post('/main', [MainController::class, 'mainPost']);

// --- DairyController ---
Route::get('/dairyCreate', [DairyController::class, 'dairyCreate']);
Route::post('/dairyCreate', [DairyController::class, 'dairyCreatePost']);
Route::get('/dairyLog', [DairyController::class, 'dairyLog']);
Route::post('/dairyLog', [DairyController::class, 'dairyLogPost']);
Route::get('/publicDiaries', [DairyController::class, 'publicDiaryLog']); // みんなの投稿