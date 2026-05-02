<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MXIK Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        *, body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
        .kpi-bar { height: 3px; border-radius: 99px; }
        .tag { display:inline-flex; align-items:center; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; letter-spacing: .02em; }
        /* search result card */
        #search-result { display:none; }
        #search-result.visible { display:block; }
        /* circular gauge */
        .gauge-ring { transform: rotate(-90deg); transform-origin: 50% 50%; }
        /* smooth number animation */
        @keyframes fadeInUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeInUp .4s ease both; }
        .fade-up-2 { animation: fadeInUp .4s .08s ease both; }
        .fade-up-3 { animation: fadeInUp .4s .16s ease both; }
    </style>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                brand: { 50:'#eef2ff', 100:'#e0e7ff', 500:'#6366f1', 600:'#4f46e5', 700:'#4338ca', 900:'#1e1b4b' }
            }}}
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen antialiased">

{{-- ═══════════════════ HEADER ═══════════════════ --}}
<header class="bg-gradient-to-r from-brand-900 via-brand-700 to-indigo-600 sticky top-0 z-30 shadow-lg">
    <div class="max-w-7xl mx-auto px-6 h-14 flex items-center gap-4">
        <div class="flex items-center gap-2 shrink-0">
            <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center">
                <span class="text-white text-xs font-black">M</span>
            </div>
            <span class="text-white font-bold text-base tracking-tight">MXIK</span>
            <span class="text-indigo-300 text-sm hidden sm:block">Analytics</span>
        </div>

        {{-- GTIN / Code Search --}}
        <div class="flex-1 max-w-md relative">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-indigo-300 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input id="gtin-input" type="text" placeholder="Search by GTIN, MXIK code or name…"
                    class="w-full bg-white/10 text-white placeholder-indigo-300 text-sm pl-9 pr-4 py-1.5 rounded-lg border border-white/20 focus:outline-none focus:border-white/60 focus:bg-white/20 transition-all"
                    autocomplete="off">
                <div id="search-spinner" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                    <svg class="w-4 h-4 text-indigo-300 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </div>
            </div>
            {{-- Dropdown results --}}
            <div id="search-dropdown" class="hidden absolute top-full left-0 right-0 mt-1.5 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50 max-h-80 overflow-y-auto">
                <div id="search-content"></div>
            </div>
        </div>

        <div class="flex items-center gap-3 ml-auto shrink-0">
            @if($stats['last_sync'])
                <div class="hidden md:flex items-center gap-1.5 text-xs text-indigo-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ \Carbon\Carbon::createFromTimestamp($stats['last_sync'])->diffForHumans() }}
                </div>
            @else
                <span class="hidden md:block text-xs text-amber-300 font-medium">Never synced</span>
            @endif
            <a href="/api/class-codes" target="_blank"
               class="text-xs font-semibold text-white/80 hover:text-white border border-white/25 hover:border-white/60 px-3 py-1.5 rounded-lg transition-colors">
                API ↗
            </a>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-7 space-y-6">

    @php
        $total      = $stats['total'];
        $withGtin   = $stats['with_gtin'];
        $gtinPct    = $total > 0 ? round($withGtin / $total * 100, 1) : 0;
        $activePct  = $total > 0 ? round($stats['active_count'] / $total * 100, 1) : 0;
        $changedPct = $total > 0 ? round($stats['changed_count'] / $total * 100, 1) : 0;
        $countryCnt = count($stats['by_country']);
        // Gauge circumference for r=15.9: 2π×15.9 ≈ 100
        $gtinDash   = $gtinPct;
        $activeDash = $activePct;
    @endphp

    {{-- ═══════════════════ KPI CARDS ═══════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 fade-up">

        {{-- Total --}}
        <div class="card p-5 col-span-1">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Total Codes</p>
            <p class="text-2xl font-extrabold text-gray-900 tabular-nums">{{ number_format($total) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ number_format($stats['total_groups']) }} groups</p>
            <div class="kpi-bar bg-indigo-500 mt-3 w-full"></div>
        </div>

        {{-- GTIN Coverage --}}
        <div class="card p-5 flex items-center gap-3">
            <svg viewBox="0 0 36 36" class="w-14 h-14 shrink-0">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3.2"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#10b981" stroke-width="3.2"
                    stroke-dasharray="{{ $gtinDash }} 100"
                    stroke-linecap="round" class="gauge-ring"/>
                <text x="18" y="22" text-anchor="middle" font-size="8" font-weight="800" fill="#111827">{{ $gtinPct }}%</text>
            </svg>
            <div class="min-w-0">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">GTIN Coverage</p>
                <p class="text-base font-bold text-gray-900 mt-0.5">{{ number_format($withGtin) }}</p>
                <p class="text-xs text-gray-400">have barcodes</p>
            </div>
        </div>

        {{-- Active --}}
        <div class="card p-5">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Active Codes</p>
            <p class="text-2xl font-extrabold text-emerald-600 tabular-nums">{{ number_format($stats['active_count']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $activePct }}% of catalog</p>
            <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width:{{ $activePct }}%"></div>
            </div>
        </div>

        {{-- Changed --}}
        <div class="card p-5">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Changed</p>
            <p class="text-2xl font-extrabold text-amber-500 tabular-nums">{{ number_format($stats['changed_count']) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $changedPct }}% superseded</p>
            <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-amber-400 rounded-full" style="width:{{ $changedPct }}%"></div>
            </div>
        </div>

        {{-- Added This Month --}}
        <div class="card p-5">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">This Month</p>
            <p class="text-2xl font-extrabold text-sky-600 tabular-nums">{{ number_format($stats['added_month']) }}</p>
            <div class="flex items-center gap-3 mt-1">
                <p class="text-xs text-gray-400">week: <span class="font-semibold text-gray-600">{{ number_format($stats['added_week']) }}</span></p>
                <p class="text-xs text-gray-400">today: <span class="font-semibold text-gray-600">{{ number_format($stats['added_today']) }}</span></p>
            </div>
            <div class="kpi-bar bg-sky-400 mt-3 w-full"></div>
        </div>

        {{-- Countries --}}
        <div class="card p-5">
            <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Countries</p>
            <p class="text-2xl font-extrabold text-violet-600 tabular-nums">{{ $countryCnt }}</p>
            <p class="text-xs text-gray-400 mt-1">from GTIN prefixes</p>
            <div class="kpi-bar bg-violet-400 mt-3 w-full"></div>
        </div>
    </div>

    {{-- ═══════════════════ ROW 2: Monthly Trend + Status Donut ═══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 fade-up-2">

        {{-- Monthly Growth Trend --}}
        <div class="card p-6 lg:col-span-3">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Growth Trend</h2>
                    <p class="text-xs text-gray-400 mt-0.5">New codes added — last 12 months</p>
                </div>
                @php $monthTotal = array_sum($monthValues); @endphp
                <div class="text-right">
                    <p class="text-lg font-extrabold text-brand-600 tabular-nums">{{ number_format($monthTotal) }}</p>
                    <p class="text-xs text-gray-400">in period</p>
                </div>
            </div>
            @if(!empty($monthValues))
                <div class="relative h-52">
                    <canvas id="monthlyChart"></canvas>
                </div>
            @else
                <div class="h-52 flex items-center justify-center text-gray-400 text-sm">No data yet</div>
            @endif
        </div>

        {{-- Status Distribution --}}
        <div class="card p-6 lg:col-span-2">
            <div class="mb-4">
                <h2 class="text-sm font-bold text-gray-800">Status Distribution</h2>
                <p class="text-xs text-gray-400 mt-0.5">Catalog health by code status</p>
            </div>
            @if(!empty($stats['by_status']))
                <div class="relative h-44 flex items-center justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
                @php
                    $statusMeta = ['Active' => ['bg-emerald-500','text-emerald-700','bg-emerald-50'], 'Changed' => ['bg-amber-500','text-amber-700','bg-amber-50'], 'Default' => ['bg-slate-400','text-slate-600','bg-slate-50']];
                    $statusTotal = array_sum($stats['by_status']);
                @endphp
                <div class="mt-3 space-y-1.5">
                    @foreach($stats['by_status'] as $label => $cnt)
                    @php $m = $statusMeta[$label] ?? ['bg-gray-400','text-gray-600','bg-gray-50']; @endphp
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-2 h-2 rounded-full {{ $m[0] }} shrink-0"></span>
                        <span class="text-gray-600 w-14 shrink-0">{{ $label }}</span>
                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="{{ $m[0] }} h-full rounded-full" style="width: {{ $statusTotal > 0 ? round($cnt/$statusTotal*100) : 0 }}%"></div>
                        </div>
                        <span class="text-gray-500 tabular-nums w-12 text-right shrink-0">{{ number_format($cnt) }}</span>
                        <span class="text-gray-400 w-7 text-right shrink-0">{{ $statusTotal > 0 ? round($cnt/$statusTotal*100) : 0 }}%</span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="h-52 flex items-center justify-center text-gray-400 text-sm">No data yet</div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════ ROW 3: Top Groups + Compliance ═══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 fade-up-3">

        {{-- Top Product Groups --}}
        <div class="card p-6 lg:col-span-3">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Top Product Groups</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Codes per group — GTIN coverage overlay</p>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-indigo-200 border border-indigo-400 inline-block"></span>Total</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500 inline-block"></span>With GTIN</span>
                </div>
            </div>
            @if(!empty($stats['top_groups']))
                <div class="relative h-64">
                    <canvas id="groupsChart"></canvas>
                </div>
            @else
                <div class="h-64 flex items-center justify-center text-gray-400 text-sm">No group data yet</div>
            @endif
        </div>

        {{-- Compliance Flags --}}
        <div class="card p-6 lg:col-span-2">
            <div class="mb-5">
                <h2 class="text-sm font-bold text-gray-800">Regulatory Compliance</h2>
                <p class="text-xs text-gray-400 mt-0.5">Boolean flag breakdown — % of total</p>
            </div>
            @if($total > 0)
                @php
                    $flags = [
                        ['label' => 'Requires Labeling',   'key' => 'label',           'color' => 'bg-rose-500',   'light' => 'text-rose-700',  'icon' => '🏷️'],
                        ['label' => 'Label for Check',      'key' => 'label_for_check', 'color' => 'bg-orange-500', 'light' => 'text-orange-700','icon' => '🔍'],
                        ['label' => 'Package Required',     'key' => 'use_package',     'color' => 'bg-sky-500',    'light' => 'text-sky-700',   'icon' => '📦'],
                        ['label' => 'Cash Sale Allowed',    'key' => 'cash_sale',       'color' => 'bg-emerald-500','light' => 'text-emerald-700','icon' => '💵'],
                    ];
                @endphp
                <div class="space-y-4">
                    @foreach($flags as $f)
                    @php
                        $cnt = $stats['flags'][$f['key']] ?? 0;
                        $pct = round($cnt / $total * 100, 1);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="flex items-center gap-1.5 text-xs font-medium text-gray-700">
                                <span>{{ $f['icon'] }}</span>{{ $f['label'] }}
                            </span>
                            <span class="text-xs font-bold {{ $f['light'] }}">{{ $pct }}%</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="{{ $f['color'] }} h-full rounded-full transition-all" style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 tabular-nums w-16 text-right shrink-0">{{ number_format($cnt) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Summary insight --}}
                <div class="mt-5 p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs text-gray-500 leading-relaxed">
                        <span class="font-semibold text-gray-700">{{ round(($stats['flags']['label'] ?? 0) / max(1, $total) * 100, 1) }}%</span>
                        of catalog requires physical labeling. Cash sales permitted for
                        <span class="font-semibold text-gray-700">{{ round(($stats['flags']['cash_sale'] ?? 0) / max(1, $total) * 100, 1) }}%</span>
                        of codes.
                    </p>
                </div>
            @else
                <div class="text-gray-400 text-sm">No data yet</div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════ ROW 4: Countries + Year History ═══════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Country breakdown --}}
        <div class="card p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Origin Countries</h2>
                    <p class="text-xs text-gray-400 mt-0.5">By GTIN prefix — top {{ count($stats['by_country']) }}</p>
                </div>
                <span class="tag bg-violet-100 text-violet-700">{{ $countryCnt }} total</span>
            </div>
            @if(!empty($stats['by_country']))
                @php $maxC = $stats['by_country'][0]['total'] ?? 1; $gtinCountTotal = array_sum(array_column($stats['by_country'], 'total')); @endphp
                <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                    @foreach($stats['by_country'] as $i => $row)
                    <div class="flex items-center gap-2.5 text-sm group">
                        <span class="text-lg w-7 text-center shrink-0 leading-none">{{ $row['flag'] ?: '🌐' }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium text-gray-700 truncate">{{ $row['country'] }}</span>
                                <div class="flex items-center gap-2 shrink-0 ml-2">
                                    <span class="text-xs text-gray-400 tabular-nums">{{ round($row['total'] / max(1, $gtinCountTotal) * 100, 1) }}%</span>
                                    <span class="text-xs font-semibold text-gray-600 tabular-nums">{{ number_format($row['total']) }}</span>
                                </div>
                            </div>
                            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all"
                                     style="width:{{ round($row['total'] / $maxC * 100) }}%; background: hsl({{ 220 + $i * 15 }}, 65%, 55%)"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="h-40 flex items-center justify-center text-gray-400 text-sm">No GTIN data yet</div>
            @endif
        </div>

        {{-- Year-over-Year History --}}
        <div class="card p-6 lg:col-span-3">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Year-over-Year History</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Cumulative additions per calendar year</p>
                </div>
                @if(!empty($yearLabels))
                    @php $peakYear = $yearLabels[array_search(max($yearValues), $yearValues)] ?? '—'; @endphp
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Peak year</p>
                        <p class="text-sm font-bold text-brand-600">{{ $peakYear }}</p>
                    </div>
                @endif
            </div>
            @if(!empty($yearLabels))
                <div class="relative h-52">
                    <canvas id="yearChart"></canvas>
                </div>
            @else
                <div class="h-52 flex items-center justify-center text-gray-400 text-sm">No data yet</div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════ ROW 5: Last Items ═══════════════════ --}}
    @php
        $itemCards = [
            ['label' => 'Last Added',   'item' => $stats['last_created_item'], 'dateKey' => 'created_at', 'accent' => 'bg-sky-500',    'tag' => 'bg-sky-50 text-sky-700'],
            ['label' => 'Last Updated', 'item' => $stats['last_updated_item'], 'dateKey' => 'updated_at', 'accent' => 'bg-violet-500', 'tag' => 'bg-violet-50 text-violet-700'],
        ];
        $sColors = ['1' => ['bg-emerald-500','bg-emerald-50 text-emerald-700'], '2' => ['bg-amber-500','bg-amber-50 text-amber-700'], '3' => ['bg-slate-400','bg-slate-50 text-slate-600']];
        $sLabels = ['1' => 'Active', '2' => 'Changed', '3' => 'Default'];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($itemCards as $card)
        <div class="card p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full {{ $card['accent'] }}"></span>
                <h2 class="text-sm font-bold text-gray-800">{{ $card['label'] }}</h2>
            </div>
            @if($card['item'])
                @php
                    $item   = $card['item'];
                    $sc     = $sColors[$item['status']] ?? ['bg-gray-400','bg-gray-50 text-gray-600'];
                    $imgUrl = 'https://tasnif.soliq.uz/api/cls-api/integration-mxik/references/get/file/' . $item['id'] . '_1.png';
                @endphp
                <div class="flex gap-4">
                    {{-- Product image --}}
                    <div class="shrink-0">
                        <div class="w-20 h-20 rounded-xl border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center">
                            <img src="{{ $imgUrl }}"
                                 alt="{{ $item['name'] }}"
                                 class="w-full h-full object-contain p-1"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full items-center justify-center text-gray-300">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM16 3H8l-1 4h10l-1-4z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <code class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-mono tracking-tight truncate">{{ $item['id'] }}</code>
                            <span class="tag {{ $sc[1] }} shrink-0">{{ $sLabels[$item['status']] ?? $item['status'] }}</span>
                        </div>
                        <p class="text-sm text-gray-800 font-medium leading-snug line-clamp-2">{{ $item['name'] }}</p>
                        @if($item['gtin'])
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-gray-400">GTIN:</span>
                                <code class="text-xs font-mono text-gray-600 bg-gray-50 px-1.5 py-0.5 rounded">{{ $item['gtin'] }}</code>
                            </div>
                        @endif
                        <p class="text-xs text-gray-400 pt-1.5 border-t border-gray-50">
                            {{ \Carbon\Carbon::parse($item[$card['dateKey']])->format('d M Y, H:i') }}
                            <span class="ml-1.5 text-gray-300">({{ \Carbon\Carbon::parse($item[$card['dateKey']])->diffForHumans() }})</span>
                        </p>
                    </div>
                </div>
            @else
                <div class="text-gray-400 text-sm">No data yet</div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Footer meta --}}
    <div class="text-center text-xs text-gray-400 pb-2">
        MXIK Classifier · tasnif.soliq.uz ·
        @if($stats['last_sync'])
            Last sync {{ \Carbon\Carbon::createFromTimestamp($stats['last_sync'])->format('d M Y, H:i') }}
        @else
            Not yet synced
        @endif
    </div>

</main>

{{-- ═══════════════════ CHARTS ═══════════════════ --}}
<script>
const fmt = n => n >= 1000000 ? (n/1000000).toFixed(1)+'M' : n >= 1000 ? (n/1000).toFixed(1)+'k' : n;

// ── Monthly Trend
@if(!empty($monthValues))
(function() {
    const canvas = document.getElementById('monthlyChart');
    const ctx = canvas.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 200);
    grad.addColorStop(0, 'rgba(99,102,241,0.25)');
    grad.addColorStop(1, 'rgba(99,102,241,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthLabels),
            datasets: [{
                data: @json($monthValues),
                borderColor: '#6366f1',
                backgroundColor: grad,
                fill: true,
                tension: 0.45,
                pointRadius: 3,
                pointBackgroundColor: '#6366f1',
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#6366f1',
                pointHoverBorderWidth: 2,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleColor: '#a5b4fc',
                    bodyColor: '#e0e7ff',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: c => '  ' + c.parsed.y.toLocaleString() + ' codes' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { callback: fmt, font: { size: 10 }, color: '#94a3b8' },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#94a3b8', maxRotation: 0 },
                    border: { display: false }
                }
            }
        }
    });
})();
@endif

// ── Status Doughnut
@if(!empty($statusValues))
(function() {
    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: @json($statusLabels),
            datasets: [{
                data: @json($statusValues),
                backgroundColor: ['#10b981','#f59e0b','#94a3b8'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleColor: '#a5b4fc',
                    bodyColor: '#e0e7ff',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: c => '  ' + c.label + ': ' + c.parsed.toLocaleString() }
                }
            },
            cutout: '68%',
        }
    });
})();
@endif

// ── Top Groups Horizontal Bar
@if(!empty($topGroupTotals))
(function() {
    new Chart(document.getElementById('groupsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($topGroupLabels),
            datasets: [
                {
                    label: 'Total',
                    data: @json($topGroupTotals),
                    backgroundColor: 'rgba(99,102,241,0.15)',
                    borderColor: 'rgba(99,102,241,0.5)',
                    borderWidth: 1,
                    borderRadius: 3,
                    borderSkipped: false,
                },
                {
                    label: 'With GTIN',
                    data: @json($topGroupGtins),
                    backgroundColor: 'rgba(16,185,129,0.7)',
                    borderColor: 'rgba(5,150,105,1)',
                    borderWidth: 1,
                    borderRadius: 3,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleColor: '#a5b4fc',
                    bodyColor: '#e0e7ff',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: c => '  ' + c.dataset.label + ': ' + c.parsed.x.toLocaleString() }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { callback: fmt, font: { size: 10 }, color: '#94a3b8' },
                    border: { display: false }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#374151' },
                    border: { display: false }
                }
            }
        }
    });
})();
@endif

// ── Year History Bar
@if(!empty($yearValues))
(function() {
    new Chart(document.getElementById('yearChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($yearLabels),
            datasets: [{
                data: @json($yearValues),
                backgroundColor: function(c) {
                    const max = Math.max(...@json($yearValues));
                    const a = 0.35 + 0.65 * (c.parsed.y / max);
                    return `rgba(99,102,241,${a.toFixed(2)})`;
                },
                borderColor: 'rgba(79,70,229,0.7)',
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleColor: '#a5b4fc',
                    bodyColor: '#e0e7ff',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: c => '  ' + c.parsed.y.toLocaleString() + ' codes' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { callback: fmt, font: { size: 10 }, color: '#94a3b8' },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: '500' }, color: '#374151' },
                    border: { display: false }
                }
            }
        }
    });
})();
@endif

// ═══════════════════ GTIN / Code Search ═══════════════════
(function() {
    const input    = document.getElementById('gtin-input');
    const dropdown = document.getElementById('search-dropdown');
    const content  = document.getElementById('search-content');
    const spinner  = document.getElementById('search-spinner');
    let timer;

    const statusLabel = { '1': 'Active', '2': 'Changed', '3': 'Default' };
    const statusClass = {
        '1': 'bg-emerald-50 text-emerald-700',
        '2': 'bg-amber-50 text-amber-700',
        '3': 'bg-slate-50 text-slate-600'
    };

    function renderResults(data) {
        if (!data.data || data.data.length === 0) {
            content.innerHTML = `<div class="px-4 py-6 text-sm text-gray-400 text-center">No results found</div>`;
            return;
        }
        const items = data.data.map(item => {
            const sc = statusClass[item.status] || 'bg-gray-50 text-gray-600';
            const sl = statusLabel[item.status] || item.status;
            const gtin = item.gtin ? `<span class="font-mono text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">${item.gtin}</span>` : '';
            return `
            <a href="/api/class-codes/${item.id}" target="_blank"
               class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors border-b border-gray-100 last:border-0 group">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                        <code class="text-xs font-mono text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">${item.id}</code>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold ${sc}">${sl}</span>
                        ${gtin}
                    </div>
                    <p class="text-sm text-gray-800 leading-snug truncate group-hover:text-brand-600">${item.name || '—'}</p>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-500 shrink-0 mt-0.5 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>`;
        }).join('');

        const meta = data.meta || {};
        const total = meta.total ?? data.data.length;
        const header = total > data.data.length
            ? `<div class="px-4 py-2 text-[10px] font-semibold text-gray-400 uppercase tracking-widest border-b border-gray-100 bg-gray-50">
                  Showing ${data.data.length} of ${total.toLocaleString()} results
               </div>`
            : '';
        content.innerHTML = header + items;
    }

    function doSearch(q) {
        if (!q) { dropdown.classList.add('hidden'); return; }
        spinner.classList.remove('hidden');
        dropdown.classList.remove('hidden');
        content.innerHTML = `<div class="px-4 py-5 text-sm text-gray-400 text-center">Searching…</div>`;

        fetch(`/api/class-codes?search=${encodeURIComponent(q)}&per_page=8`)
            .then(r => r.json())
            .then(data => { spinner.classList.add('hidden'); renderResults(data); })
            .catch(() => {
                spinner.classList.add('hidden');
                content.innerHTML = `<div class="px-4 py-5 text-sm text-red-400 text-center">Search failed — check API</div>`;
            });
    }

    input.addEventListener('input', () => {
        clearTimeout(timer);
        const q = input.value.trim();
        if (q.length < 2) { dropdown.classList.add('hidden'); return; }
        timer = setTimeout(() => doSearch(q), 300);
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Escape') { dropdown.classList.add('hidden'); input.blur(); }
        if (e.key === 'Enter') { clearTimeout(timer); doSearch(input.value.trim()); }
    });

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
})();
</script>

</body>
</html>
