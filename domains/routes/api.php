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

use App\Http\Controllers\TeamMemberController;

Route::get('/team-members', [TeamMemberController::class, 'index']);
Route::get('/team-members/{id}', [TeamMemberController::class, 'show']);
Route::post('/team-members', [TeamMemberController::class, 'store']);
Route::put('/team-members/{id}', [TeamMemberController::class, 'update']);
Route::delete('/team-members/{id}', [TeamMemberController::class, 'destroy']);
