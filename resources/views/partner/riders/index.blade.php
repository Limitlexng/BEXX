@extends('layouts.app')
@section('title', 'Riders')
@section('page-title', 'Rider Management')
@section('page-subtitle', 'Manage all your assigned delivery riders')

@section('header-actions')
<a href="{{ route('partner.riders.create') }}" class="btn-primary">
  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
  Add Rider
</a>
@endsection

@section('content')
<div class="py-4 space-y-5">

  {{-- Status Filter --}}
  <div class="flex items-center gap-2 flex-wrap">
    @foreach([''=>'All','active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended','terminated'=>'Terminated'] as $s=>$l)
    <a href="{{ route('partner.riders.index', $s ? ['status'=>$s] : []) }}"
       class="px-4 py-1.5 rounded-xl text-sm font-medium transition-colors {{ ($status ?? '') === $s ? 'bg-[#C41E3A] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-[#C41E3A]/40' }}">
      {{ $l }}
    </a>
    @endforeach
  </div>

  @if($riders->isEmpty())
  <div class="bg-white rounded-2xl p-16 shadow-sm border border-slate-100 text-center">
    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
    </div>
    <h3 class="text-lg font-semibold text-slate-700 mb-2">No riders yet</h3>
    <p class="text-slate-500 text-sm mb-6">Register your first rider to start tracking performance.</p>
    <a href="{{ route('partner.riders.create') }}" class="btn-primary inline-flex">Add First Rider</a>
  </div>
  @else
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Rider</th>
            <th class="table-header">Rider ID</th>
            <th class="table-header">Motorcycle</th>
            <th class="table-header">License</th>
            <th class="table-header">Compliance</th>
            <th class="table-header">Earnings</th>
            <th class="table-header">Deliveries</th>
            <th class="table-header">Status</th>
            <th class="table-header">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($riders as $rider)
          <tr class="hover:bg-slate-50/50 transition-colors">
            <td class="table-cell">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-{{ $rider->status === 'active' ? 'emerald' : 'slate' }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <span class="text-{{ $rider->status === 'active' ? 'emerald' : 'slate' }}-700 text-xs font-bold">{{ strtoupper(substr($rider->first_name,0,1)) }}</span>
                </div>
                <div>
                  <div class="text-sm font-medium text-slate-900">{{ $rider->full_name }}</div>
                  <div class="text-xs text-slate-500">{{ $rider->phone }}</div>
                </div>
              </div>
            </td>
            <td class="table-cell">
              <span class="font-mono text-xs font-bold text-[#C41E3A] bg-[#C41E3A]/5 px-2 py-1 rounded-lg">{{ $rider->rider_id }}</span>
            </td>
            <td class="table-cell">
              @if($rider->currentMotorcycle)
              <a href="{{ route('partner.fleet.show', $rider->currentMotorcycle) }}" class="text-blue-600 hover:underline text-xs font-medium">{{ $rider->currentMotorcycle->fleet_id }}</a>
              @else
              <span class="text-slate-400 text-xs">Unassigned</span>
              @endif
            </td>
            <td class="table-cell">
              @if($rider->license_expiry)
                @php $ld = now()->diffInDays($rider->license_expiry, false) @endphp
                @if($ld < 0) <span class="badge-danger">Expired</span>
                @elseif($ld <= 30) <span class="badge-warning">{{ $ld }}d left</span>
                @else <span class="badge-success">Valid</span>
                @endif
              @else
              <span class="badge-gray">N/A</span>
              @endif
            </td>
            <td class="table-cell">
              <div class="flex items-center gap-1.5">
                <div class="w-12 bg-slate-100 rounded-full h-1.5">
                  <div class="h-1.5 rounded-full {{ $rider->compliance_score >= 80 ? 'bg-emerald-500' : ($rider->compliance_score >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                       style="width: {{ $rider->compliance_score }}%"></div>
                </div>
                <span class="text-xs text-slate-600">{{ $rider->compliance_score }}%</span>
              </div>
            </td>
            <td class="table-cell font-semibold text-emerald-700">₦{{ number_format($rider->total_earnings, 0) }}</td>
            <td class="table-cell text-slate-600">{{ number_format($rider->total_deliveries) }}</td>
            <td class="table-cell">
              <span class="badge-{{ ['active'=>'success','inactive'=>'gray','suspended'=>'warning','terminated'=>'danger'][$rider->status] ?? 'gray' }}">{{ ucfirst($rider->status) }}</span>
            </td>
            <td class="table-cell">
              <div class="flex items-center gap-2">
                <a href="{{ route('partner.riders.show', $rider) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                @if($rider->status === 'active')
                <form method="POST" action="{{ route('partner.riders.suspend', $rider) }}" onsubmit="return confirm('Suspend this rider?')">
                  @csrf
                  <button type="submit" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Suspend</button>
                </form>
                @elseif($rider->status === 'suspended')
                <form method="POST" action="{{ route('partner.riders.activate', $rider) }}">
                  @csrf
                  <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">Activate</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $riders->links() }}</div>
  </div>
  @endif
</div>
@endsection
