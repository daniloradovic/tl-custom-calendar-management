<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->namespace('api')->group(function () {
    Route::apiResource('events', 'App\Http\Controllers\Api\EventsController');
    Route::get('locations', 'App\Http\Controllers\Api\EventsController@locations')->name('locations');
});