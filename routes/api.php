<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
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


Route::group(['prefix' => 'authors'], function () {
    Route::get('/', [AuthorController::class, 'getAuthors']);
    Route::get('/{id}', [AuthorController::class, 'getAuthor']);
});

Route::group(['prefix' => 'books'], function () {
    Route::get('/', [BookController::class, 'getBooks']);
    Route::get('/{id}', [BookController::class, 'getBook']);
    Route::post('/', [BookController::class, 'addBook'])->middleware('auth:sanctum');
    Route::put('', [BookController::class, 'updateBook']);
    Route::delete('/{id}', [BookController::class, 'deleteBook']);
});

