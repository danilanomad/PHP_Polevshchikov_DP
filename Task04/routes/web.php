<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', [GameController::class, 'index']);
Route::get('/games', [GameController::class, 'history']);
Route::post('/games', [GameController::class, 'startGame']);
Route::post('/step/{id}', [GameController::class, 'step']);
