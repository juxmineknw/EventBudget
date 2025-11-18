<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::get('/events', [EventController::class, 'index']);  
Route::get('/events/{id}', [EventController::class, 'show']);
