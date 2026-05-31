@extends('layouts.app')
@section('title', 'Compliance')
@section('page-title', 'Compliance Management')
@section('page-subtitle', 'Monitor licenses, insurance, and regulatory requirements')

@section('content')
<div class="py-4 space-y-6">

  {{-- Score Card --}}
  <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
    <div class="lg:col-span-1 bg-white rounded-2xl p-5 shadow-sm border border-slate-100 text-center">
      <div class="text-3xl font-bold {{ $score >= 90 ? 'text-emerald-600' : ($score >= 70 ? 'text-amber-600' : 'text-red-600') }}">{{ $score }}%</div>
      <div class="text-xs text-slate-500 mt-1">Compliance Score</div>
    </div>
    @foreach([
      ['Valid', $stats['valid'], 'emerald'],
      ['Expiring Soon', $stats['expiring_soon'], 'amber'],
      ['Expired', $stats['expired'], 'red'],
      ['Violations', $stats['violations'], 'red'],
    ] as [$label,$val,$color])
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
      <div class="text-2xl font-bold text-{{ $color }}-600">{{ $val }}</div>
      <div class="text-xs text-slate-500 mt-1">{{ $label }}</div>
    </div>
    @endforeach
  </div>

  {{-- Records Table --}}
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
      <h3 class="text-sm font-bold text-slate-900">Compliance Records</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Type</th>
            <th class="table-header">Title</th>
            <th class="table-header">Related To</th>
            <th class="table-header">Expiry Date</th>
            <th class="table-header">Status</th>
            <th class="table-header">Fine</th>
            <th class="table-header">Resolved</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($records as $record)
          <tr class="hover:bg-slate-50/50">
            <td class="table-cell capitalize text-slate-600">{{ str_replace('_',' ',$record->type) }}</td>
            <td class="table-cell font-medium text-slate-800">{{ $record->title }}</td>
            <td class="table-cell text-slate-600">
              @if($record->rider) <a href="{{ route('partner.riders.show',$record->rider) }}" class="text-blue-600 hover:underline text-xs">{{ $record->rider->full_name }}</a>
              @elseif($record->motorcycle) <a href="{{ route('partner.fleet.show',$record->motorcycle) }}" class="text-blue-600 hover:underline text-xs">{{ $record->motorcycle->fleet_id }}</a>
              @else —
              @endif
            </td>
            <td class="table-cell">
              @if($record->expiry_date)
                @php $d = now()->diffInDays($record->expiry_date, false) @endphp
                <span class="{{ $d < 0 ? 'text-red-600 font-bold' : ($d <= 30 ? 'text-amber-600 font-medium' : 'text-slate-600') }}">
                  {{ $record->expiry_date->format('d M Y') }}
                </span>
              @else —
              @endif
            </td>
            <td class="table-cell">
              <span class="badge-{{ ['valid'=>'success','expiring_soon'=>'warning','expired'=>'danger','violation'=>'danger'][$record->status] ?? 'gray' }}">
                {{ ucfirst(str_replace('_',' ',$record->status)) }}
              </span>
            </td>
            <td class="table-cell text-red-600">{{ $record->fine_amount > 0 ? '₦'.number_format($record->fine_amount,0) : '—' }}</td>
            <td class="table-cell">
              @if($record->resolved)
              <span class="badge-success">Yes</span>
              @else
              <span class="badge-gray">No</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="table-cell text-center text-slate-400 py-10">No compliance records yet</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $records->links() }}</div>
  </div>
</div>
@endsection
