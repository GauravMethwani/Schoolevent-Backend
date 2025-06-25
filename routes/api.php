<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

// API route for Event CRUD
Route::apiResource('events', EventController::class);
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});