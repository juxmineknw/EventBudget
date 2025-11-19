<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::get('/events', [EventController::class, 'index']);  
Route::get('/events/{id}', [EventController::class, 'show']);
Route::apiResource('events', EventController::class);

use App\Http\Controllers\CategoryController;

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::apiResource('categories', CategoryController::class);