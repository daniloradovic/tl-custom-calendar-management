<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum', 'throttle:100,1')->group(function () {
    Route::get('events/locations', 'App\Http\Controllers\Api\EventsController@locations')->name('events.locations');
    Route::apiResource('events', 'App\Http\Controllers\Api\EventsController');
});

Route::post('register', 'App\Http\Controllers\Api\Auth\RegisterController@register')->name('register');
