<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::namespace('AdminApi')->group(function() {
    Route::apiResource('description', 'AdminDescriptionController');
    Route::get('description_all', 'AdminDescriptionController@description_all');
});