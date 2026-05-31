@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Fleet investment overview for ' . $partner->display_name)

@section('content')
<div class="py-4 space-y-6">

    {{-- KPI Stats Row 1 --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Fleet --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h9a1 1 0 001-1z"/>
                    </svg>
                </div>
                <span class="badge-success">Fleet</span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $stats['total_motorcycles'] }}</div>
            <div class="text-sm text-slate-500 mt-1">Total Motorcycles</div>
            <div class="flex items-center gap-3 mt-3 text-xs">
                <span class="text-emerald-600 font-medium">{{ $stats['active_motorcycles'] }} active</span>
                <span class="text-amber-600">{{ $stats['maintenance_motorcycles'] }} maintenance</span>
            </div>
        </div>

        {{-- Active Riders --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="badge-success">Riders</span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $stats['active_riders'] }}</div>
            <div class="text-sm text-slate-500 mt-1">Active Riders</div>
            <div class="flex items-center gap-3 mt-3 text-xs">
                <span class="text-slate-500">{{ $stats['total_riders'] }} total</span>
                <span class="text-red-600">{{ $stats['inactive_riders'] }} inactive</span>
            </div>
        </div>

        {{-- Fleet Utilization --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="badge-info">Utilization</span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $stats['fleet_utilization'] }}%</div>
            <div class="text-sm text-slate-500 mt-1">Fleet Utilization Rate</div>
            <div class="mt-3">
                <div class="w-full bg-slate-100 rounded-full h-1.5">
                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $stats['fleet_utilization'] }}%"></div>
                </div>
            </div>
        </div>

        {{-- Compliance Score --}}
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-[#C41E3A]/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#C41E3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="{{ $compliance['score'] >= 90 ? 'badge-success' : ($compliance['score'] >= 70 ? 'badge-warning' : 'badge-danger') }}">
                    {{ $compliance['score'] >= 90 ? 'Excellent' : ($compliance['score'] >= 70 ? 'Good' : 'Poor') }}
                </span>
            </div>
            <div class="text-3xl font-bold text-slate-900">{{ $compliance['score'] }}%</div>
            <div class="text-sm text-slate-500 mt-1">Compliance Score</div>
            <div class="flex items-center gap-3 mt-3 text-xs">
                @if($compliance['violations'] > 0)
                <span class="text-red-600 font-medium">{{ $compliance['violations'] }} violations</span>
                @else
                <span class="text-emerald-600 font-medium">No violations</span>
                @endif
                @if($compliance['expiring_soon'] > 0)
                <span class="text-amber-600">{{ $compliance['expiring_soon'] }} expiring</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Earnings Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach([
            ['label' => "Today's Earnings", 'value' => $earnings['today'], 'color' => 'emerald'],
            ['label' => 'This Week', 'value' => $earnings['this_week'], 'color' => 'blue'],
            ['label' => 'This Month', 'value' => $earnings['this_month'], 'color' => 'purple'],
            ['label' => 'This Year', 'value' => $earnings['this_year'], 'color' => 'indigo'],
            ['label' => 'Lifetime Total', 'value' => $earnings['this_year'], 'color' => 'amber'],
        ] as $e)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">{{ $e['label'] }}</div>
            <div class="text-2xl font-bold text-slate-900">₦{{ number_format($e['value'], 0) }}</div>
        </div>
        @endforeach
    </div>

    {{-- Main content grid --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Earnings Trend Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Earnings Trend</h3>
                    <p class="text-sm text-slate-500">Last 30 days</p>
                </div>
                <a href="{{ route('partner.earnings.index') }}" class="text-[#C41E3A] text-sm font-medium hover:underline">View all →</a>
            </div>
            <div style="height: 220px; position: relative;">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>

        {{-- Wallet Card --}}
        <div class="space-y-4">
            <div class="bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45] rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-slate-400 text-xs uppercase tracking-wider">Available Balance</div>
                        <div class="text-3xl font-bold mt-1">₦{{ number_format($stats['wallet_balance'], 2) }}</div>
                    </div>
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
                <div class="border-t border-white/10 pt-4 grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-slate-400 text-xs">Pending</div>
                        <div class="text-white font-semibold">₦{{ number_format($stats['pending_balance'], 2) }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-xs">Lifetime</div>
                        <div class="text-white font-semibold">₦{{ number_format($stats['lifetime_earnings'], 0) }}</div>
                    </div>
                </div>
                <a href="{{ route('partner.wallet.index') }}"
                   class="mt-4 block w-full text-center py-2.5 bg-[#C41E3A] hover:bg-[#A01830] text-white text-sm font-semibold rounded-xl transition-colors">
                    Withdraw Funds
                </a>
            </div>

            {{-- Top Motorcycles --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Top Earning Assets</h3>
                    <a href="{{ route('partner.fleet.index') }}" class="text-[#C41E3A] text-xs hover:underline">View all</a>
                </div>
                <div class="space-y-3">
                    @forelse($topMotorcycles as $moto)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate">{{ $moto->fleet_id }}</div>
                            <div class="text-xs text-slate-500">{{ $moto->full_name }}</div>
                        </div>
                        <div class="text-sm font-bold text-emerald-600">₦{{ number_format($moto->total_earnings, 0) }}</div>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 text-center py-4">No motorcycles yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Alerts --}}
    @if($recentAlerts->count() > 0)
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900">Recent Alerts</h3>
            <a href="{{ route('partner.alerts.index') }}" class="text-[#C41E3A] text-sm font-medium hover:underline">View all →</a>
        </div>
        <div class="space-y-3">
            @foreach($recentAlerts as $alert)
            <div class="flex items-start gap-4 p-3 rounded-xl {{ $alert->read ? 'bg-slate-50' : 'bg-amber-50 border border-amber-100' }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                    {{ match($alert->severity) { 'danger' => 'bg-red-100', 'warning' => 'bg-amber-100', 'success' => 'bg-emerald-100', default => 'bg-blue-100' } }}">
                    @if($alert->severity === 'danger')
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @elseif($alert->severity === 'warning')
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    @else
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-slate-800">{{ $alert->title }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $alert->message }}</div>
                    <div class="text-xs text-slate-400 mt-1">{{ $alert->created_at->diffForHumans() }}</div>
                </div>
                @if(!$alert->read)
                <span class="w-2 h-2 bg-[#C41E3A] rounded-full flex-shrink-0 mt-1"></span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('partner.fleet.create') }}" class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-[#C41E3A]/30 transition-all group">
            <div class="w-10 h-10 bg-[#C41E3A]/10 group-hover:bg-[#C41E3A] rounded-xl flex items-center justify-center transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-[#C41E3A] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-800">Add Motorcycle</div>
                <div class="text-xs text-slate-500">Register asset</div>
            </div>
        </a>
        <a href="{{ route('partner.riders.create') }}" class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-[#C41E3A]/30 transition-all group">
            <div class="w-10 h-10 bg-emerald-50 group-hover:bg-emerald-500 rounded-xl flex items-center justify-center transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-800">Add Rider</div>
                <div class="text-xs text-slate-500">Register rider</div>
            </div>
        </a>
        <a href="{{ route('partner.maintenance.create') }}" class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group">
            <div class="w-10 h-10 bg-blue-50 group-hover:bg-blue-500 rounded-xl flex items-center justify-center transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-800">Log Maintenance</div>
                <div class="text-xs text-slate-500">Record service</div>
            </div>
        </a>
        <a href="{{ route('partner.wallet.index') }}" class="flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-purple-300 transition-all group">
            <div class="w-10 h-10 bg-purple-50 group-hover:bg-purple-500 rounded-xl flex items-center justify-center transition-colors flex-shrink-0">
                <svg class="w-5 h-5 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-800">Withdraw Funds</div>
                <div class="text-xs text-slate-500">Request payout</div>
            </div>
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('earningsChart');
if (ctx) {
    const labels = @json($earningsTrend->pluck('date'));
    const data = @json($earningsTrend->pluck('total'));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Net Earnings (₦)',
                data: data,
                borderColor: '#C41E3A',
                backgroundColor: 'rgba(196, 30, 58, 0.08)',
                borderWidth: 2.5,
                pointRadius: 3,
                pointBackgroundColor: '#C41E3A',
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { size: 11 },
                        color: '#94a3b8',
                        callback: v => '₦' + (v/1000).toFixed(0) + 'k'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#94a3b8', maxTicksLimit: 7 }
                }
            }
        }
    });
}
</script>
@endpush
