<?php

use logoIlluminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::namespace('ParticipantApi')->group(function() {
//    Route::apiResource('description', 'DescriptionController')->parameters(['description' => 'update_description']);
    Route::apiResource('description', 'DescriptionController');
    Route::get('club_logo', 'DescriptionController@club_logo');
});
