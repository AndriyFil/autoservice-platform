<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class PublicWorkshopIndexController extends Controller
{
    public function __invoke(): View
    {
        if (! Schema::hasTable('workshops')) {
            return view('public.workshops-index', [
                'workshops' => new Collection,
            ]);
        }

        $workshops = Workshop::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['name', 'slug', 'phone', 'email']);

        return view('public.workshops-index', [
            'workshops' => $workshops,
        ]);
    }
}
