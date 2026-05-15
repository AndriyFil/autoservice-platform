<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class PublicBookingPageController extends Controller
{
    public function __invoke(string $workshop): View
    {
        return view('public.booking-request', [
            'workshop' => $workshop,
        ]);
    }
}
