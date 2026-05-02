<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $service): View
    {
        $stats = $service->stats();

        $monthData = [];
        for ($i = 11; $i >= 0; $i--) {
            $key   = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->format("M 'y");
            $monthData[$label] = $stats['by_month'][$key] ?? 0;
        }

        $topGroupLabels = array_map(
            fn($g) => mb_strlen($g['name']) > 30 ? mb_substr($g['name'], 0, 27).'…' : $g['name'],
            $stats['top_groups']
        );

        return view('dashboard', [
            'stats'          => $stats,
            'yearLabels'     => array_keys($stats['by_year']),
            'yearValues'     => array_values($stats['by_year']),
            'monthLabels'    => array_keys($monthData),
            'monthValues'    => array_values($monthData),
            'statusLabels'   => array_keys($stats['by_status']),
            'statusValues'   => array_values($stats['by_status']),
            'topGroupLabels' => array_values($topGroupLabels),
            'topGroupTotals' => array_column($stats['top_groups'], 'total'),
            'topGroupGtins'  => array_column($stats['top_groups'], 'with_gtin'),
        ]);
    }
}
