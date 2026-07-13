<?php

use App\Http\Controllers\Api\TriageController;
use Illuminate\Support\Facades\Route;

Route::post('/triage', TriageController::class)
    ->middleware('throttle:30,1')
    ->name('api.triage');
