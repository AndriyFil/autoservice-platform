<?php

use App\Http\Controllers\PublicBookingPageController;
use App\Http\Controllers\PublicWorkshopIndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicWorkshopIndexController::class)
    ->name('public.workshops.index');

Route::get('/w/{workshop}/booking', PublicBookingPageController::class)
    ->name('public.booking');
