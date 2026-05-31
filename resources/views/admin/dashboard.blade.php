@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Cartlex Operations Dashboard')
@section('page-subtitle', 'Fleet-wide overview · ' . now()->format('d F Y'))

@section('content')
<div class="py-4 space-y-6">

  {{-- KPI Row --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
      ['Total Partners', $stats['total_partners'], 'Pending: '.$stats['pending_partners'], 'blue', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
      ['Total Motorcycles', $stats['total_motorcycles'], 'Active: '.$stats['active_motorcycles'], 'purple', 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z'],
      ['Total Riders', $stats['total_riders'], 'Active: '.$stats['active_riders'], 'emerald', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
      ['Pending Withdrawals', $stats['pending_withdrawals'], 'Awaiting approval', 'amber', 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
    ] as [$label,$val,$sub,$color,$icon])
    <div class="stat-card">
      <div class="flex items-center justify-between mb-3">
        <div class="w-10 h-10 bg-{{ $color }}-100 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
        </div>
      </div>
      <div class="text-3xl font-bold text-slate-900">{{ number_format($val) }}</div>
      <div class="text-sm text-slate-500 mt-1">{{ $label }}</div>
      <div class="text-xs text-slate-400 mt-1">{{ $sub }}</div>
    </div>
    @endforeach
  </div>

  {{-- Revenue Row --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45] rounded-2xl p-6 text-white">
      <div class="text-slate-400 text-xs uppercase tracking-wider mb-1">Today's Revenue</div>
      <div class="text-2xl font-bold">₦{{ number_format($stats['total_earnings_today'],2) }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="text-slate-500 text-xs uppercase tracking-wider mb-1">This Month</div>
      <div class="text-2xl font-bold text-slate-900">₦{{ number_format($stats['total_earnings_month'],0) }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="text-slate-500 text-xs uppercase tracking-wider mb-1">Platform Fees (Month)</div>
      <div class="text-2xl font-bold text-[#C41E3A]">₦{{ number_format($stats['platform_fees_month'],0) }}</div>
    </div>
  </div>

  {{-- Earnings Chart --}}
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
    <h3 class="text-sm font-bold text-slate-900 mb-4">Platform Revenue Trend (Last 30 Days)</h3>
    <div style="height:220px;position:relative"><canvas id="revChart"></canvas></div>
  </div>

  <div class="grid lg:grid-cols-2 gap-6">
    {{-- Pending Partners --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-slate-900">Recent Partners</h3>
        <a href="{{ route('admin.partners.index') }}" class="text-[#C41E3A] text-xs hover:underline">View all</a>
      </div>
      @forelse($recentPartners as $p)
      <div class="flex items-center gap-3 py-3 border-b border-slate-50 last:border-0">
        <div class="w-9 h-9 bg-[#C41E3A]/10 rounded-full flex items-center justify-center text-[#C41E3A] font-bold text-sm flex-shrink-0">{{ strtoupper(substr($p->display_name,0,1)) }}</div>
        <div class="flex-1">
          <div class="text-sm font-medium text-slate-800">{{ $p->display_name }}</div>
          <div class="text-xs text-slate-500">{{ $p->partner_code }} · {{ str_replace('_',' ',ucwords($p->partner_type,'_')) }}</div>
        </div>
        <div class="flex items-center gap-2">
          <span class="badge-{{ ['active'=>'success','pending'=>'warning','suspended'=>'danger'][$p->status] ?? 'gray' }}">{{ ucfirst($p->status) }}</span>
          @if($p->status === 'pending')
          <form method="POST" action="{{ route('admin.partners.approve',$p) }}">
            @csrf
            <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold">Approve</button>
          </form>
          @endif
        </div>
      </div>
      @empty
      <p class="text-sm text-slate-400 text-center py-4">No partners yet</p>
      @endforelse
    </div>

    {{-- Pending Withdrawals --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-slate-900">Pending Withdrawals</h3>
        <a href="{{ route('admin.withdrawals.index') }}" class="text-[#C41E3A] text-xs hover:underline">View all</a>
      </div>
      @forelse($pendingWithdrawals->take(5) as $wd)
      <div class="flex items-center gap-3 py-3 border-b border-slate-50 last:border-0">
        <div class="flex-1">
          <div class="text-sm font-medium text-slate-800">{{ $wd->partner->display_name }}</div>
          <div class="text-xs text-slate-500">{{ $wd->bank_name }} · {{ $wd->account_number }}</div>
        </div>
        <div class="text-right">
          <div class="text-sm font-bold text-slate-900">₦{{ number_format($wd->amount,0) }}</div>
          <div class="text-xs text-slate-400">{{ $wd->reference }}</div>
        </div>
        <div class="flex items-center gap-1.5">
          <form method="POST" action="{{ route('admin.withdrawals.approve',$wd) }}">
            @csrf <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold">Approve</button>
          </form>
        </div>
      </div>
      @empty
      <p class="text-sm text-slate-400 text-center py-4">No pending withdrawals</p>
      @endforelse
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('revChart');
if (ctx) {
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: @json($earningsTrend->pluck('date')),
      datasets: [
        { label: 'Revenue', data: @json($earningsTrend->pluck('total')), borderColor: '#C41E3A', backgroundColor: 'rgba(196,30,58,0.08)', fill: true, tension: 0.4, borderWidth: 2.5 },
        { label: 'Platform Fees', data: @json($earningsTrend->pluck('fees')), borderColor: '#6366f1', backgroundColor: 'transparent', tension: 0.4, borderWidth: 2, borderDash: [4,3] }
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'top', labels: { font: { size: 12 }, usePointStyle: true } } },
      scales: {
        y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 11 }, color: '#94a3b8', callback: v => '₦'+(v/1000).toFixed(0)+'k' } },
        x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8', maxTicksLimit: 7 } }
      }
    }
  });
}
</script>
@endpush
