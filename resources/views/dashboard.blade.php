<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MXIK Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eef2ff', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

{{-- Top nav --}}
<header class="bg-white border-b border-gray-200 sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-brand-600 font-bold text-lg tracking-tight">MXIK</span>
            <span class="text-gray-300">|</span>
            <span class="text-gray-500 text-sm">Dashboard</span>
        </div>
        <div class="flex items-center gap-4 text-sm text-gray-500">
            @if($stats['last_sync'])
                <span>
                    Last sync:
                    <span class="font-medium text-gray-700">
                        {{ \Carbon\Carbon::createFromTimestamp($stats['last_sync'])->diffForHumans() }}
                    </span>
                </span>
            @else
                <span class="text-amber-500 font-medium">Never synced</span>
            @endif
            <a href="/api/class-codes"
               class="px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-md text-xs font-medium transition-colors">
                API
            </a>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-8 space-y-8">

    {{-- ── Stat cards ── --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @php
            $cards = [
                ['label' => 'Total Codes',    'value' => $stats['total'],        'color' => 'bg-indigo-600',  'icon' => '🗂️'],
                ['label' => 'With GTIN',      'value' => $stats['with_gtin'],    'color' => 'bg-emerald-600', 'icon' => '✅'],
                ['label' => 'Without GTIN',   'value' => $stats['without_gtin'], 'color' => 'bg-amber-500',   'icon' => '⚠️'],
                ['label' => 'Added Today',    'value' => $stats['added_today'],  'color' => 'bg-sky-600',     'icon' => '📅'],
                ['label' => 'Added This Week','value' => $stats['added_week'],   'color' => 'bg-violet-600',  'icon' => '📆'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</span>
                <span class="text-lg">{{ $card['icon'] }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900">
                {{ number_format($card['value']) }}
            </div>
            @if($stats['total'] > 0 && in_array($card['label'], ['With GTIN', 'Without GTIN']))
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="{{ $card['color'] }} h-full rounded-full"
                         style="width: {{ round($card['value'] / $stats['total'] * 100, 1) }}%"></div>
                </div>
                <span class="text-xs text-gray-400">{{ round($card['value'] / $stats['total'] * 100, 1) }}% of total</span>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ── Second row: Year chart + Country table ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Year chart (3/5 width) --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Items Added by Year</h2>
            @if(!empty($yearLabels))
                <div class="relative h-56">
                    <canvas id="yearChart"></canvas>
                </div>
            @else
                <div class="h-56 flex items-center justify-center text-gray-400 text-sm">No data yet</div>
            @endif
        </div>

        {{-- Country table (2/5 width) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Top Countries by GTIN</h2>
            @if(!empty($stats['by_country']))
                @php $maxCountry = $stats['by_country'][0]['total'] ?? 1; @endphp
                <div class="space-y-2 overflow-y-auto max-h-56 pr-1">
                    @foreach($stats['by_country'] as $row)
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-base w-6 text-center shrink-0">{{ $row['flag'] ?: '🌐' }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="truncate text-gray-700 text-xs font-medium">{{ $row['country'] }}</span>
                                <span class="text-gray-500 text-xs ml-2 shrink-0">{{ number_format($row['total']) }}</span>
                            </div>
                            <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-400 rounded-full"
                                     style="width: {{ round($row['total'] / $maxCountry * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="h-56 flex items-center justify-center text-gray-400 text-sm">No GTIN data yet</div>
            @endif
        </div>
    </div>

    {{-- ── Third row: Status + Groups ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Status distribution --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Status Distribution</h2>
            @if(!empty($stats['by_status']))
                @php
                    $statusColors = ['Active' => 'bg-emerald-500', 'Changed' => 'bg-amber-500', 'Default' => 'bg-indigo-400'];
                    $statusTotal  = array_sum($stats['by_status']);
                @endphp
                <div class="space-y-3">
                    @foreach($stats['by_status'] as $status => $count)
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-white {{ $statusColors[$status] ?? 'bg-gray-400' }} w-20 justify-center shrink-0">
                            {{ $status }}
                        </span>
                        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="{{ $statusColors[$status] ?? 'bg-gray-400' }} h-full rounded-full"
                                 style="width: {{ $statusTotal > 0 ? round($count / $statusTotal * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 w-20 text-right shrink-0">
                            {{ number_format($count) }}
                        </span>
                        <span class="text-xs text-gray-400 w-10 shrink-0">
                            {{ $statusTotal > 0 ? round($count / $statusTotal * 100) : 0 }}%
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-gray-400 text-sm">No data yet</div>
            @endif
        </div>

        {{-- Summary info --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Summary</h2>
            <dl class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <dt class="text-sm text-gray-500">Total MXIK Codes</dt>
                    <dd class="text-sm font-bold text-gray-900">{{ number_format($stats['total']) }}</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <dt class="text-sm text-gray-500">Class Groups</dt>
                    <dd class="text-sm font-bold text-gray-900">{{ number_format($stats['total_groups']) }}</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <dt class="text-sm text-gray-500">GTIN Coverage</dt>
                    <dd class="text-sm font-bold text-gray-900">
                        @if($stats['total'] > 0)
                            {{ round($stats['with_gtin'] / $stats['total'] * 100, 1) }}%
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <dt class="text-sm text-gray-500">Countries detected</dt>
                    <dd class="text-sm font-bold text-gray-900">{{ count($stats['by_country']) }}</dd>
                </div>
                <div class="flex justify-between items-center py-2">
                    <dt class="text-sm text-gray-500">Last sync</dt>
                    <dd class="text-sm font-bold text-gray-900">
                        @if($stats['last_sync'])
                            {{ \Carbon\Carbon::createFromTimestamp($stats['last_sync'])->format('d M Y, H:i') }}
                        @else
                            <span class="text-amber-500">Never</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- ── Fourth row: Last added / Last updated item cards ── --}}
    @php
        $itemCards = [
            ['label' => 'Last Added Code',   'item' => $stats['last_created_item'], 'dateKey' => 'created_at', 'accent' => 'bg-sky-600'],
            ['label' => 'Last Updated Code',  'item' => $stats['last_updated_item'], 'dateKey' => 'updated_at', 'accent' => 'bg-violet-600'],
        ];
        $statusColors = ['1' => 'bg-emerald-500', '2' => 'bg-amber-500', '3' => 'bg-indigo-400'];
        $statusLabels = ['1' => 'Active', '2' => 'Changed', '3' => 'Default'];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($itemCards as $card)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 rounded-full {{ $card['accent'] }}"></span>
                <h2 class="text-sm font-semibold text-gray-700">{{ $card['label'] }}</h2>
            </div>
            @if($card['item'])
                @php $item = $card['item']; @endphp
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ $item['id'] }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-white {{ $statusColors[$item['status']] ?? 'bg-gray-400' }} shrink-0">
                            {{ $statusLabels[$item['status']] ?? $item['status'] }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-800 font-medium leading-snug">{{ $item['name'] }}</p>
                    @if($item['gtin'])
                        <p class="text-xs text-gray-400">GTIN: <span class="font-mono text-gray-600">{{ $item['gtin'] }}</span></p>
                    @endif
                    <p class="text-xs text-gray-400 pt-1 border-t border-gray-50">
                        {{ \Carbon\Carbon::parse($item[$card['dateKey']])->format('d M Y, H:i') }}
                        <span class="ml-1 text-gray-300">({{ \Carbon\Carbon::parse($item[$card['dateKey']])->diffForHumans() }})</span>
                    </p>
                </div>
            @else
                <div class="text-gray-400 text-sm">No data yet</div>
            @endif
        </div>
        @endforeach
    </div>

</main>

@if(!empty($yearLabels))
<script>
    const ctx = document.getElementById('yearChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($yearLabels),
            datasets: [{
                label: 'Items added',
                data: @json($yearValues),
                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString() + ' items'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        callback: v => v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v,
                        font: { size: 11 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
</script>
@endif

</body>
</html>
