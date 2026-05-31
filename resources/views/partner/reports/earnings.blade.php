@extends('layouts.app')
@section('title', 'Earnings Report')
@section('page-title', 'Earnings Report')
@section('page-subtitle', 'Revenue breakdown for the selected period')

@section('header-actions')
<button onclick="window.print()" class="btn-secondary">Print / Export</button>
@endsection

@section('content')
<div class="py-4 space-y-5">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
    <form method="GET" class="flex items-center gap-3 mb-6">
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
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-base font-bold text-slate-900">Earnings: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</h2>
        <p class="text-sm text-slate-500">{{ $earnings->count() }} transactions · Total: <strong class="text-emerald-700">₦{{ number_format($total,2) }}</strong></p>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Date</th>
            <th class="table-header">Motorcycle</th>
            <th class="table-header">Rider</th>
            <th class="table-header">Gross</th>
            <th class="table-header">Fee</th>
            <th class="table-header">Net</th>
            <th class="table-header">Source</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($earnings as $e)
          <tr>
            <td class="table-cell">{{ $e->earning_date->format('d M Y') }}</td>
            <td class="table-cell font-mono text-xs">{{ $e->motorcycle?->fleet_id ?? '—' }}</td>
            <td class="table-cell">{{ $e->rider ? $e->rider->first_name.' '.$e->rider->last_name : '—' }}</td>
            <td class="table-cell">₦{{ number_format($e->amount,2) }}</td>
            <td class="table-cell text-red-500">-₦{{ number_format($e->platform_fee,2) }}</td>
            <td class="table-cell font-bold text-emerald-700">₦{{ number_format($e->net_amount,2) }}</td>
            <td class="table-cell capitalize">{{ $e->source }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot class="bg-slate-50 border-t border-slate-200">
          <tr>
            <td colspan="3" class="table-cell font-bold">Total</td>
            <td class="table-cell font-bold">₦{{ number_format($earnings->sum('amount'),2) }}</td>
            <td class="table-cell font-bold text-red-500">-₦{{ number_format($earnings->sum('platform_fee'),2) }}</td>
            <td class="table-cell font-bold text-emerald-700">₦{{ number_format($total,2) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
