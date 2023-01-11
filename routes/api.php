<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MeaningController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WordController;
use App\Models\Bookmark;

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

// Public Route
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::get('/search', [WordController::class,'search']);
Route::resource('categories', CategoryController::class);
Route::resource('tags', TagController::class);
Route::resource('words', WordController::class);
Route::resource('requests', RequestController::class);
Route::get('requests/accept/{id}', [RequestController::class, 'accept']);

// Protected route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('test',  function () {
        echo "Oke";
    });
    Route::get('/histories',[UserController::class,'getHistories']);
    Route::delete('histories/{id}', [UserController::class,'destroyHistory']);
    Route::resource('bookmarks', BookmarkController::class);
});

Route::get('view', function () {
    return("OKE");
});