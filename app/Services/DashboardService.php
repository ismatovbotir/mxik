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

            $byYear = ClassCode::selectRaw("YEAR(created_at) as year, count(*) as total")
                ->groupBy('year')
                ->orderBy('year')
                ->pluck('total', 'year')
                ->toArray();

            $byMonth = ClassCode::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
                ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $byStatus = ClassCode::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->mapWithKeys(function ($count, $status) {
                    $label = match ($status) {
                        '1'     => 'Faol',
                        '2'     => "O'zgartirilgan",
                        default => 'Standart',
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

            $topGroups = DB::table('class_codes as cc')
                ->leftJoin('class_groups as cg', 'cc.class_group_id', '=', 'cg.id')
                ->selectRaw("
                    COALESCE(cg.name_uz, 'Guruhsiz') as name,
                    COUNT(*) as total,
                    SUM(CASE WHEN cc.gtin IS NOT NULL THEN 1 ELSE 0 END) as with_gtin
                ")
                ->groupBy('cg.id', 'cg.name_uz')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();

            $byStatusRaw = ClassCode::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            return [
                'total'             => $total,
                'with_gtin'         => $withGtin,
                'without_gtin'      => $total - $withGtin,
                'added_today'       => ClassCode::whereDate('created_at', today())->count(),
                'added_week'        => ClassCode::where('created_at', '>=', now()->startOfWeek())->count(),
                'added_month'       => ClassCode::where('created_at', '>=', now()->startOfMonth())->count(),
                'active_count'      => (int) ($byStatusRaw['1'] ?? 0),
                'changed_count'     => (int) ($byStatusRaw['2'] ?? 0),
                'by_year'           => $byYear,
                'by_month'          => $byMonth,
                'by_status'         => $byStatus,
                'by_country'        => $byCountry,
                'top_groups'        => $topGroups,
                'flags'             => [
                    'label'           => ClassCode::where('label', true)->count(),
                    'label_for_check' => ClassCode::where('labelForCheck', true)->count(),
                    'use_package'     => ClassCode::where('usePackage', true)->count(),
                    'cash_sale'       => ClassCode::where('cashSale', true)->count(),
                ],
                'total_groups'      => ClassGroup::count(),
                'last_sync'         => Setting::get('last_sync_at'),
                'last_created_item' => ClassCode::orderByDesc('created_at')->first(['id', 'name', 'status', 'gtin', 'label', 'labelForCheck', 'usePackage', 'cashSale', 'created_at'])?->toArray(),
                'last_updated_item' => ClassCode::orderByDesc('updated_at')->first(['id', 'name', 'status', 'gtin', 'label', 'labelForCheck', 'usePackage', 'cashSale', 'updated_at'])?->toArray(),
            ];
        });
    }
}
