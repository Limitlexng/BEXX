@extends('layouts.app')
@section('title', 'Fleet Overview')
@section('page-title', 'Fleet Overview')
@section('page-subtitle', 'All registered motorcycles across all partners')

@section('content')
<div class="py-4 space-y-5">
  <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
    @foreach([
      ['Total',$stats['total'],'slate'],
      ['Active',$stats['active'],'emerald'],
      ['Maintenance',$stats['maintenance'],'amber'],
      ['Suspended',$stats['suspended'],'red'],
      ['Retired',$stats['retired'],'gray'],
    ] as [$label,$val,$color])
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 text-center">
      <div class="text-2xl font-bold text-{{ $color }}-700">{{ $val }}</div>
      <div class="text-xs text-slate-500 mt-1">{{ $label }}</div>
    </div>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Fleet ID</th>
            <th class="table-header">Motorcycle</th>
            <th class="table-header">Partner</th>
            <th class="table-header">Rider</th>
            <th class="table-header">Plate</th>
            <th class="table-header">Health</th>
            <th class="table-header">Earnings</th>
            <th class="table-header">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($motorcycles as $m)
          <tr class="hover:bg-slate-50/50">
            <td class="table-cell"><span class="font-mono text-xs font-bold text-[#C41E3A]">{{ $m->fleet_id }}</span></td>
            <td class="table-cell font-medium text-slate-800">{{ $m->full_name }}</td>
            <td class="table-cell text-slate-600 text-xs">{{ $m->partner->display_name }}</td>
            <td class="table-cell text-slate-600 text-xs">{{ $m->currentRider?->full_name ?? 'Unassigned' }}</td>
            <td class="table-cell font-mono text-xs">{{ $m->plate_number ?? '—' }}</td>
            <td class="table-cell">
              <span class="text-xs capitalize {{ ['excellent'=>'text-emerald-600','good'=>'text-blue-600','average'=>'text-amber-600','poor'=>'text-orange-600','critical'=>'text-red-600'][$m->health_rating] ?? 'text-slate-500' }}">{{ ucfirst($m->health_rating) }}</span>
            </td>
            <td class="table-cell font-semibold text-emerald-700">₦{{ number_format($m->total_earnings,0) }}</td>
            <td class="table-cell">
              <span class="badge-{{ ['active'=>'success','maintenance'=>'warning','suspended'=>'danger','retired'=>'gray'][$m->status] ?? 'gray' }}">{{ ucfirst($m->status) }}</span>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="table-cell text-center text-slate-400 py-10">No motorcycles registered</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $motorcycles->links() }}</div>
  </div>
</div>
@endsection
