@extends('layouts.app')
@section('title', 'Fleet Report')
@section('page-title', 'Fleet Performance Report')
@section('page-subtitle', 'Complete overview of all fleet assets')

@section('header-actions')
<button onclick="window.print()" class="btn-secondary">
  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
  Print Report
</button>
@endsection

@section('content')
<div class="py-4 space-y-5">
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-lg font-bold text-slate-900">Fleet Report – {{ $partner->display_name }}</h2>
        <p class="text-sm text-slate-500">Generated {{ now()->format('d M Y, H:i') }}</p>
      </div>
      <div class="text-right">
        <div class="text-2xl font-bold text-slate-900">{{ $motorcycles->count() }}</div>
        <div class="text-xs text-slate-500">Total Assets</div>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Fleet ID</th>
            <th class="table-header">Motorcycle</th>
            <th class="table-header">Plate</th>
            <th class="table-header">Rider</th>
            <th class="table-header">Status</th>
            <th class="table-header">Health</th>
            <th class="table-header">Earnings</th>
            <th class="table-header">Maintenance Cost</th>
            <th class="table-header">ROI</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($motorcycles as $moto)
          <tr>
            <td class="table-cell font-mono text-xs font-bold text-[#C41E3A]">{{ $moto->fleet_id }}</td>
            <td class="table-cell font-medium">{{ $moto->full_name }}</td>
            <td class="table-cell font-mono text-xs">{{ $moto->plate_number ?? '—' }}</td>
            <td class="table-cell">{{ $moto->currentRider?->full_name ?? 'Unassigned' }}</td>
            <td class="table-cell capitalize">{{ $moto->status }}</td>
            <td class="table-cell capitalize">{{ $moto->health_rating }}</td>
            <td class="table-cell font-bold text-emerald-700">₦{{ number_format($moto->total_earnings,0) }}</td>
            <td class="table-cell text-amber-700">₦{{ number_format($moto->total_maintenance_cost,0) }}</td>
            <td class="table-cell font-medium">{{ $moto->roi }}%</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot class="bg-slate-50 border-t border-slate-200">
          <tr>
            <td colspan="6" class="table-cell font-bold">Totals</td>
            <td class="table-cell font-bold text-emerald-700">₦{{ number_format($motorcycles->sum('total_earnings'),0) }}</td>
            <td class="table-cell font-bold text-amber-700">₦{{ number_format($motorcycles->sum('total_maintenance_cost'),0) }}</td>
            <td class="table-cell"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
