<?php

use App\Http\Controllers\Api\BookingRequestController;
use App\Http\Controllers\Api\CancelBookingRequestController;
use App\Http\Controllers\Api\ConfirmBookingRequestController;
use App\Http\Controllers\Api\RejectBookingRequestController;
use App\Http\Controllers\Api\WorkshopBookingRequestController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/workshops/{workshop}/booking-requests',
    [WorkshopBookingRequestController::class, 'index'],
);

Route::post(
    '/workshops/{workshop}/booking-requests',
    [BookingRequestController::class, 'store'],
);

Route::post(
    '/booking-requests/{bookingRequest}/confirm',
    ConfirmBookingRequestController::class,
);

Route::post(
    '/booking-requests/{bookingRequest}/cancel',
    CancelBookingRequestController::class,
);

Route::post(
    '/booking-requests/{bookingRequest}/reject',
    RejectBookingRequestController::class,
);
