@extends('layouts.app')
@section('title', $partner->display_name)
@section('page-title', $partner->display_name)
@section('page-subtitle', $partner->partner_code . ' · ' . ucfirst($partner->status))

@section('header-actions')
<div class="flex items-center gap-2">
  @if($partner->status === 'pending')
  <form method="POST" action="{{ route('admin.partners.approve',$partner) }}">
    @csrf
    <button type="submit" class="btn-primary">Approve Partner</button>
  </form>
  @elseif($partner->status === 'active')
  <form method="POST" action="{{ route('admin.partners.suspend',$partner) }}" onsubmit="return confirm('Suspend?')">
    @csrf
    <button type="submit" class="btn-secondary">Suspend</button>
  </form>
  @endif
</div>
@endsection

@section('content')
<div class="py-4 space-y-6">
  <div class="grid lg:grid-cols-3 gap-6">
    {{-- Partner Info --}}
    <div class="space-y-4">
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Partner Details</h3>
        <dl class="space-y-2.5">
          @foreach([
            'Name' => $partner->display_name,
            'Code' => $partner->partner_code,
            'Type' => str_replace('_',' ',ucwords($partner->partner_type,'_')),
            'Email' => $partner->user->email,
            'Phone' => $partner->phone ?? '—',
            'Status' => ucfirst($partner->status),
            'CAC Number' => $partner->cac_number ?? '—',
            'Joined' => $partner->created_at->format('d M Y'),
            'Approved' => $partner->approved_at?->format('d M Y') ?? '—',
          ] as $label => $value)
          <div class="flex justify-between py-1.5 border-b border-slate-50">
            <dt class="text-xs text-slate-500">{{ $label }}</dt>
            <dd class="text-xs font-medium text-slate-800 text-right">{{ $value }}</dd>
          </div>
          @endforeach
        </dl>
      </div>

      {{-- Wallet --}}
      <div class="bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45] rounded-2xl p-5 text-white">
        <div class="text-slate-400 text-xs uppercase tracking-wider mb-1">Available Balance</div>
        <div class="text-2xl font-bold">₦{{ number_format($partner->wallet_balance,2) }}</div>
        <div class="mt-3 pt-3 border-t border-white/10 grid grid-cols-2 gap-2 text-xs">
          <div><div class="text-slate-400">Pending</div><div class="text-white font-medium">₦{{ number_format($partner->pending_balance,2) }}</div></div>
          <div><div class="text-slate-400">Lifetime</div><div class="text-white font-medium">₦{{ number_format($partner->lifetime_earnings,0) }}</div></div>
        </div>
      </div>

      {{-- Upload Earnings --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Upload Earnings</h3>
        <form method="POST" action="{{ route('admin.partners.upload-earnings',$partner) }}" class="space-y-3">
          @csrf
          <select name="motorcycle_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
            <option value="">-- Select Motorcycle (optional) --</option>
            @foreach($partner->motorcycles as $m)
            <option value="{{ $m->id }}">{{ $m->fleet_id }} – {{ $m->full_name }}</option>
            @endforeach
          </select>
          <select name="rider_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
            <option value="">-- Select Rider (optional) --</option>
            @foreach($partner->riders as $r)
            <option value="{{ $r->id }}">{{ $r->full_name }}</option>
            @endforeach
          </select>
          <div class="grid grid-cols-2 gap-2">
            <input type="number" name="amount" placeholder="Gross Amount" required min="0" step="0.01" class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
            <input type="number" name="platform_fee" placeholder="Platform Fee" required min="0" step="0.01" class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
          </div>
          <input type="date" name="earning_date" value="{{ today()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
          <div class="grid grid-cols-2 gap-2">
            <select name="period_type" class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
              <option value="daily">Daily</option>
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
            <select name="source" class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
              <option value="delivery">Delivery</option>
              <option value="rental">Rental</option>
              <option value="bonus">Bonus</option>
              <option value="adjustment">Adjustment</option>
            </select>
          </div>
          <button type="submit" class="btn-primary w-full justify-center text-xs py-2">Upload Earnings</button>
        </form>
      </div>
    </div>

    {{-- Right: Assets & Transactions --}}
    <div class="lg:col-span-2 space-y-5">
      {{-- Motorcycles --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Motorcycles ({{ $partner->motorcycles->count() }})</h3>
        @forelse($partner->motorcycles->take(5) as $m)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
          <div class="flex items-center gap-3">
            <span class="font-mono text-xs font-bold text-[#C41E3A] bg-[#C41E3A]/5 px-2 py-0.5 rounded">{{ $m->fleet_id }}</span>
            <span class="text-sm text-slate-700">{{ $m->full_name }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-emerald-700">₦{{ number_format($m->total_earnings,0) }}</span>
            <span class="badge-{{ ['active'=>'success','maintenance'=>'warning','suspended'=>'danger','retired'=>'gray'][$m->status] ?? 'gray' }}">{{ ucfirst($m->status) }}</span>
          </div>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-4">No motorcycles</p>
        @endforelse
      </div>

      {{-- Recent Transactions --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Recent Transactions</h3>
        @forelse($partner->walletTransactions as $txn)
        <div class="flex items-center gap-3 py-2.5 border-b border-slate-50 last:border-0">
          <div class="flex-1">
            <div class="text-sm font-medium text-slate-800">{{ $txn->description }}</div>
            <div class="text-xs text-slate-400">{{ $txn->created_at->format('d M Y H:i') }}</div>
          </div>
          <div class="text-sm font-bold {{ in_array($txn->type,['credit','bonus']) ? 'text-emerald-700' : 'text-red-600' }}">
            {{ in_array($txn->type,['credit','bonus']) ? '+' : '-' }}₦{{ number_format($txn->amount,2) }}
          </div>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-4">No transactions</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
