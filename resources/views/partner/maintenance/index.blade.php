@extends('layouts.app')
@section('title', 'Maintenance')
@section('page-title', 'Maintenance Management')
@section('page-subtitle', 'Service history and upcoming maintenance schedules')

@section('header-actions')
<a href="{{ route('partner.maintenance.create') }}" class="btn-primary">
  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
  Log Service
</a>
@endsection

@section('content')
<div class="py-4 space-y-6">

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="stat-card">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Records</div>
      <div class="text-2xl font-bold text-slate-900">{{ $logs->total() }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Cost</div>
      <div class="text-2xl font-bold text-amber-700">₦{{ number_format($totalCost,0) }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Due (30 days)</div>
      <div class="text-2xl font-bold text-{{ $upcomingService->count() > 0 ? 'red' : 'slate' }}-700">{{ $upcomingService->count() }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Status</div>
      <div class="text-lg font-bold text-slate-900">{{ $upcomingService->count() > 0 ? 'Action Needed' : 'All Clear' }}</div>
    </div>
  </div>

  @if($upcomingService->count() > 0)
  <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
    <div class="flex items-center gap-2 mb-3">
      <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <h3 class="text-sm font-bold text-amber-800">Upcoming Services</h3>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
      @foreach($upcomingService as $log)
      <div class="bg-white rounded-xl p-3 flex items-center gap-3">
        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
          <div class="text-sm font-medium text-slate-800 truncate">{{ $log->motorcycle->fleet_id }}</div>
          <div class="text-xs text-amber-600">Due {{ $log->next_service_due->format('d M Y') }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Logs Table --}}
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Date</th>
            <th class="table-header">Motorcycle</th>
            <th class="table-header">Type</th>
            <th class="table-header">Description</th>
            <th class="table-header">Workshop</th>
            <th class="table-header">Cost</th>
            <th class="table-header">Downtime</th>
            <th class="table-header">Next Due</th>
            <th class="table-header">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($logs as $log)
          <tr class="hover:bg-slate-50/50">
            <td class="table-cell text-slate-700">{{ $log->service_date->format('d M Y') }}</td>
            <td class="table-cell">
              <a href="{{ route('partner.fleet.show', $log->motorcycle) }}" class="font-mono text-xs font-bold text-[#C41E3A] hover:underline">{{ $log->motorcycle->fleet_id }}</a>
            </td>
            <td class="table-cell capitalize text-slate-600">{{ str_replace('_',' ',$log->type) }}</td>
            <td class="table-cell max-w-xs truncate" title="{{ $log->description }}">{{ $log->description }}</td>
            <td class="table-cell text-slate-600">{{ $log->workshop_name ?? '—' }}</td>
            <td class="table-cell font-medium text-amber-700">₦{{ number_format($log->cost,0) }}</td>
            <td class="table-cell text-slate-600">{{ $log->downtime_hours }}h</td>
            <td class="table-cell text-sm">
              @if($log->next_service_due)
                @php $d = now()->diffInDays($log->next_service_due, false) @endphp
                <span class="{{ $d <= 0 ? 'text-red-600 font-bold' : ($d <= 7 ? 'text-amber-600 font-medium' : 'text-slate-500') }}">
                  {{ $log->next_service_due->format('d M Y') }}
                </span>
              @else —
              @endif
            </td>
            <td class="table-cell">
              <span class="badge-{{ ['completed'=>'success','in_progress'=>'warning','scheduled'=>'info','cancelled'=>'gray'][$log->status] ?? 'gray' }}">{{ ucfirst(str_replace('_',' ',$log->status)) }}</span>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="table-cell text-center text-slate-400 py-10">No maintenance records yet</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $logs->links() }}</div>
  </div>
</div>
@endsection
