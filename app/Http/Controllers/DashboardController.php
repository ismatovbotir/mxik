<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $service): View
    {
        $stats = $service->stats();

        return view('dashboard', [
            'stats'      => $stats,
            'yearLabels' => array_keys($stats['by_year']),
            'yearValues' => array_values($stats['by_year']),
        ]);
    }
}
