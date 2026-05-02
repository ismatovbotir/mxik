<?php

namespace App\Services;

use App\Models\ClassCode;
use App\Models\ClassGroup;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function stats(): array
    {
        return Cache::rememberForever('dashboard_stats', function () {
            $total    = ClassCode::count();
            $withGtin = ClassCode::whereNotNull('gtin')->count();

            $byYear = ClassCode::selectRaw("strftime('%Y', created_at) as year, count(*) as total")
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $byStatus = ClassCode::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->mapWithKeys(function ($count, $status) {
                    $label = match ($status) {
                        '1'     => 'Active',
                        '2'     => 'Changed',
                        default => 'Default',
                    };
                    return [$label => $count];
                })
                ->toArray();

            $byCountry = DB::table('class_codes as cc')
                ->selectRaw("
                    COALESCE(gp3.country, gp2.country, gp1.country, 'Unknown') as country,
                    COALESCE(gp3.flag, gp2.flag, gp1.flag) as flag,
                    COUNT(*) as total
                ")
                ->leftJoin('gtin_prefixes as gp3', fn ($j) => $j->whereRaw('SUBSTR(cc.gtin, 1, 3) = gp3.prefix'))
                ->leftJoin('gtin_prefixes as gp2', fn ($j) => $j->whereRaw('SUBSTR(cc.gtin, 1, 2) = gp2.prefix'))
                ->leftJoin('gtin_prefixes as gp1', fn ($j) => $j->whereRaw('SUBSTR(cc.gtin, 1, 1) = gp1.prefix'))
                ->whereNotNull('cc.gtin')
                ->groupByRaw("COALESCE(gp3.country, gp2.country, gp1.country, 'Unknown'), COALESCE(gp3.flag, gp2.flag, gp1.flag)")
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();

            return [
                'total'        => $total,
                'with_gtin'    => $withGtin,
                'without_gtin' => $total - $withGtin,
                'added_today'  => ClassCode::whereDate('created_at', today())->count(),
                'added_week'   => ClassCode::where('created_at', '>=', now()->startOfWeek())->count(),
                'by_year'      => $byYear,
                'by_status'    => $byStatus,
                'by_country'   => $byCountry,
                'total_groups'      => ClassGroup::count(),
                'last_sync'         => Setting::get('last_sync_at'),
                'last_created_item' => ClassCode::orderByDesc('created_at')->first(['id', 'name', 'status', 'gtin', 'created_at'])?->toArray(),
                'last_updated_item' => ClassCode::orderByDesc('updated_at')->first(['id', 'name', 'status', 'gtin', 'updated_at'])?->toArray(),
            ];
        });
    }
}
