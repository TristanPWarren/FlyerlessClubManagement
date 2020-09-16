<?php

use Illuminate\Support\Facades\Route;

Route::namespace('ParticipantApi')->group(function() {
    Route::apiResource('description', 'DescriptionController')->only(['index', 'store']);
});
