<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\AddConsumerController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/add-consumer', [App\Http\Controllers\Api\AddConsumerController::class, 'store']);
Route::post('/consumer-answer', [App\Http\Controllers\Api\ConsumerAnswerController::class, 'store']);
Route::post('/store-answer', [App\Http\Controllers\Api\StoreAnswerController::class, 'store']);
Route::get('/worksheet', [App\Http\Controllers\Api\WorksheetController::class, 'index']);
Route::get('/worksheetStatus', [App\Http\Controllers\Api\StatusController::class, 'index']);
Route::get('/worksheetStore', [App\Http\Controllers\Api\WorksheetStoreController::class, 'index']);

