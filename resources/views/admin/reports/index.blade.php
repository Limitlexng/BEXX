@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Platform Reports')
@section('page-subtitle', 'Financial and operational analytics')

@section('content')
<div class="py-4 space-y-5">
  <form method="GET" class="flex items-center gap-3">
    <div>
      <label class="block text-xs text-slate-500 mb-1">From</label>
      <input type="date" name="from" value="{{ $from }}" class="px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
    </div>
    <div>
      <label class="block text-xs text-slate-500 mb-1">To</label>
      <input type="date" name="to" value="{{ $to }}" class="px-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30">
    </div>
    <div class="flex items-end">
      <button type="submit" class="btn-primary py-2">Generate</button>
    </div>
  </form>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Earnings Distributed</div>
      <div class="text-2xl font-bold text-emerald-700">₦{{ number_format($summary['total_earnings'],2) }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Platform Revenue</div>
      <div class="text-2xl font-bold text-[#C41E3A]">₦{{ number_format($summary['total_fees'],2) }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Partners</div>
      <div class="text-2xl font-bold text-slate-900">{{ $summary['total_partners'] }}</div>
    </div>
  </div>
</div>
@endsection
