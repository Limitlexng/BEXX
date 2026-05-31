@extends('layouts.app')
@section('title', 'Earnings')
@section('page-title', 'Earnings & Revenue')
@section('page-subtitle', 'Track all income across your fleet')

@section('content')
<div class="py-4 space-y-6">

  {{-- Summary Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
    @foreach([
      ['Today','today','emerald'],
      ['This Week','this_week','blue'],
      ['This Month','this_month','purple'],
      ['This Year','this_year','indigo'],
      ['Lifetime','lifetime','amber'],
    ] as [$label,$key,$color])
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1.5">{{ $label }}</div>
      <div class="text-xl font-bold text-slate-900">₦{{ number_format($summary[$key],0) }}</div>
    </div>
    @endforeach
  </div>

  {{-- Investment Metrics --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1.5">Avg / Motorcycle (Month)</div>
      <div class="text-xl font-bold text-slate-900">₦{{ number_format($avgPerMotorcycle,0) }}</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1.5">Avg / Rider (Month)</div>
      <div class="text-xl font-bold text-slate-900">₦{{ number_format($avgPerRider,0) }}</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1.5">Fleet ROI (Lifetime)</div>
      <div class="text-xl font-bold text-emerald-700">{{ $roi }}%</div>
    </div>
  </div>

  <div class="grid lg:grid-cols-2 gap-6">
    {{-- Trend Chart --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <h3 class="text-sm font-bold text-slate-900 mb-4">Earnings Trend (Last 30 Days)</h3>
      <div style="height:200px;position:relative"><canvas id="trendChart"></canvas></div>
    </div>

    {{-- By Motorcycle --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <h3 class="text-sm font-bold text-slate-900 mb-4">Earnings by Motorcycle</h3>
      <div class="space-y-3">
        @forelse($byMotorcycle->take(6) as $moto)
        @php $max = $byMotorcycle->max('total_earnings') ?: 1 @endphp
        <div>
          <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-medium text-slate-700">{{ $moto->fleet_id }} – {{ $moto->full_name }}</span>
            <span class="text-xs font-bold text-emerald-700">₦{{ number_format($moto->total_earnings,0) }}</span>
          </div>
          <div class="w-full bg-slate-100 rounded-full h-1.5">
            <div class="h-1.5 rounded-full bg-[#C41E3A]" style="width: {{ $moto->total_earnings / $max * 100 }}%"></div>
          </div>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-4">No data</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- By Rider --}}
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
    <h3 class="text-sm font-bold text-slate-900 mb-4">Top Earning Riders</h3>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Rider</th>
            <th class="table-header">Rider ID</th>
            <th class="table-header">Total Earnings</th>
            <th class="table-header">Deliveries</th>
            <th class="table-header">Avg / Delivery</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($byRider as $rider)
          <tr class="hover:bg-slate-50/50">
            <td class="table-cell font-medium">{{ $rider->first_name }} {{ $rider->last_name }}</td>
            <td class="table-cell"><span class="font-mono text-xs bg-[#C41E3A]/5 text-[#C41E3A] px-2 py-0.5 rounded">{{ $rider->rider_id }}</span></td>
            <td class="table-cell font-bold text-emerald-700">₦{{ number_format($rider->total_earnings,0) }}</td>
            <td class="table-cell text-slate-600">{{ number_format($rider->total_deliveries) }}</td>
            <td class="table-cell text-slate-600">{{ $rider->total_deliveries > 0 ? '₦'.number_format($rider->total_earnings / $rider->total_deliveries, 0) : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="table-cell text-center text-slate-400">No rider data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Transaction Table --}}
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900">All Transactions</h3>
      <form method="GET" class="flex items-center gap-3">
        <select name="motorcycle_id" class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
          <option value="">All Motorcycles</option>
          @foreach($motorcycles as $m)
          <option value="{{ $m->id }}" {{ request('motorcycle_id') == $m->id ? 'selected' : '' }}>{{ $m->fleet_id }}</option>
          @endforeach
        </select>
        <select name="rider_id" class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
          <option value="">All Riders</option>
          @foreach($riders as $r)
          <option value="{{ $r->id }}" {{ request('rider_id') == $r->id ? 'selected' : '' }}>{{ $r->first_name }} {{ $r->last_name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn-primary text-xs py-2 px-3">Filter</button>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Date</th>
            <th class="table-header">Motorcycle</th>
            <th class="table-header">Rider</th>
            <th class="table-header">Gross Amount</th>
            <th class="table-header">Platform Fee</th>
            <th class="table-header">Net Amount</th>
            <th class="table-header">Source</th>
            <th class="table-header">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($earnings as $earning)
          <tr class="hover:bg-slate-50/50">
            <td class="table-cell">{{ $earning->earning_date->format('d M Y') }}</td>
            <td class="table-cell">{{ $earning->motorcycle?->fleet_id ?? '—' }}</td>
            <td class="table-cell">{{ $earning->rider ? $earning->rider->first_name.' '.$earning->rider->last_name : '—' }}</td>
            <td class="table-cell text-slate-600">₦{{ number_format($earning->amount,2) }}</td>
            <td class="table-cell text-red-500">-₦{{ number_format($earning->platform_fee,2) }}</td>
            <td class="table-cell font-bold text-emerald-700">₦{{ number_format($earning->net_amount,2) }}</td>
            <td class="table-cell capitalize text-slate-600">{{ $earning->source }}</td>
            <td class="table-cell">
              <span class="badge-{{ ['confirmed'=>'success','pending'=>'warning','paid'=>'info'][$earning->status] ?? 'gray' }}">{{ ucfirst($earning->status) }}</span>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="table-cell text-center text-slate-400 py-8">No transactions found</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $earnings->links() }}</div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('trendChart');
if (ctx) {
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: @json($trend->pluck('date')),
      datasets: [{ label: 'Net Earnings (₦)', data: @json($trend->pluck('total')), backgroundColor: 'rgba(196,30,58,0.15)', borderColor: '#C41E3A', borderWidth: 1.5, borderRadius: 4 }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 }, color: '#94a3b8', callback: v => '₦'+(v/1000).toFixed(0)+'k' } },
        x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8', maxTicksLimit: 7 } }
      }
    }
  });
}
</script>
@endpush
