@extends('layouts.app')
@section('title', $motorcycle->fleet_id . ' - ' . $motorcycle->full_name)
@section('page-title', $motorcycle->fleet_id)
@section('page-subtitle', $motorcycle->full_name . ' · ' . ($motorcycle->plate_number ?? 'No plate'))

@section('header-actions')
<a href="{{ route('partner.fleet.edit', $motorcycle) }}" class="btn-secondary">Edit Asset</a>
@endsection

@section('content')
<div class="py-4 space-y-6">

  {{-- Header Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="stat-card">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Status</div>
      @php $sc = ['active'=>'success','maintenance'=>'warning','suspended'=>'danger','retired'=>'gray','lost'=>'danger'][$motorcycle->status] ?? 'gray' @endphp
      <span class="badge-{{ $sc }} text-sm">{{ ucfirst($motorcycle->status) }}</span>
    </div>
    <div class="stat-card">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Health</div>
      @php $hc = ['excellent'=>'emerald','good'=>'blue','average'=>'amber','poor'=>'orange','critical'=>'red'][$motorcycle->health_rating] ?? 'slate' @endphp
      <div class="text-xl font-bold text-slate-900">{{ $motorcycle->health_score }}%</div>
      <div class="text-xs text-{{ $hc }}-600 font-medium capitalize">{{ ucfirst($motorcycle->health_rating) }}</div>
    </div>
    <div class="stat-card">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Total Earnings</div>
      <div class="text-xl font-bold text-emerald-700">₦{{ number_format($motorcycle->total_earnings, 0) }}</div>
      @if($motorcycle->purchase_cost)
      <div class="text-xs text-slate-500">{{ $motorcycle->roi }}% ROI</div>
      @endif
    </div>
    <div class="stat-card">
      <div class="text-xs text-slate-500 uppercase tracking-wider mb-1">Insurance</div>
      @php $days = $motorcycle->insurance_days_remaining @endphp
      @if($motorcycle->insurance_expiry)
        @if($days < 0)
        <span class="badge-danger">Expired {{ abs($days) }}d ago</span>
        @elseif($days <= 30)
        <span class="badge-warning">Expires in {{ $days }}d</span>
        @else
        <span class="badge-success">Valid · {{ $days }}d left</span>
        @endif
      @else
      <span class="badge-gray">Not set</span>
      @endif
    </div>
  </div>

  <div class="grid lg:grid-cols-3 gap-6">
    {{-- Left: Details --}}
    <div class="lg:col-span-1 space-y-4">

      {{-- Vehicle Details --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Vehicle Details</h3>
        <dl class="space-y-3">
          @foreach([
            'Fleet ID' => $motorcycle->fleet_id,
            'Brand' => $motorcycle->brand,
            'Model' => $motorcycle->model,
            'Year' => $motorcycle->year,
            'Color' => $motorcycle->color ?? '—',
            'Plate Number' => $motorcycle->plate_number ?? '—',
            'VIN Number' => $motorcycle->vin_number ?? '—',
            'Engine Number' => $motorcycle->engine_number ?? '—',
            'Vehicle Number' => $motorcycle->vehicle_number ?? '—',
          ] as $label => $value)
          <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
            <dt class="text-xs text-slate-500">{{ $label }}</dt>
            <dd class="text-sm font-medium text-slate-800 text-right">{{ $value }}</dd>
          </div>
          @endforeach
        </dl>
      </div>

      {{-- Purchase Info --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Investment</h3>
        <dl class="space-y-3">
          @foreach([
            'Purchase Date' => $motorcycle->purchase_date?->format('d M Y') ?? '—',
            'Purchase Cost' => $motorcycle->purchase_cost ? '₦'.number_format($motorcycle->purchase_cost,2) : '—',
            'Total Earnings' => '₦'.number_format($motorcycle->total_earnings,2),
            'Maintenance Cost' => '₦'.number_format($motorcycle->total_maintenance_cost,2),
            'Net Return' => '₦'.number_format($motorcycle->total_earnings - $motorcycle->total_maintenance_cost,2),
          ] as $label => $value)
          <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
            <dt class="text-xs text-slate-500">{{ $label }}</dt>
            <dd class="text-sm font-medium text-slate-800">{{ $value }}</dd>
          </div>
          @endforeach
        </dl>
      </div>

      {{-- Current Rider --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Assigned Rider</h3>
        @if($motorcycle->currentRider)
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold text-lg">
            {{ strtoupper(substr($motorcycle->currentRider->first_name,0,1)) }}
          </div>
          <div>
            <div class="font-semibold text-slate-900">{{ $motorcycle->currentRider->full_name }}</div>
            <div class="text-xs text-slate-500">{{ $motorcycle->currentRider->rider_id }}</div>
          </div>
        </div>
        <a href="{{ route('partner.riders.show', $motorcycle->currentRider) }}" class="btn-secondary w-full justify-center text-xs">
          View Rider Profile →
        </a>
        @else
        <div class="text-center py-4">
          <div class="text-slate-400 text-sm mb-3">No rider assigned</div>
          <a href="{{ route('partner.riders.create') }}" class="btn-primary text-xs">Assign Rider</a>
        </div>
        @endif
      </div>
    </div>

    {{-- Right: Activity --}}
    <div class="lg:col-span-2 space-y-5">

      {{-- Recent Earnings --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold text-slate-900">Recent Earnings</h3>
          <a href="{{ route('partner.earnings.index', ['motorcycle_id' => $motorcycle->id]) }}" class="text-[#C41E3A] text-xs hover:underline">View all</a>
        </div>
        @forelse($motorcycle->earnings as $earning)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
          <div>
            <div class="text-sm font-medium text-slate-800">{{ $earning->earning_date->format('d M Y') }}</div>
            <div class="text-xs text-slate-500 capitalize">{{ $earning->source }}</div>
          </div>
          <div class="text-right">
            <div class="text-sm font-bold text-emerald-700">₦{{ number_format($earning->net_amount,2) }}</div>
            <div class="text-xs text-slate-400">- ₦{{ number_format($earning->platform_fee,2) }} fee</div>
          </div>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-6">No earnings recorded yet</p>
        @endforelse
      </div>

      {{-- Maintenance Logs --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-bold text-slate-900">Maintenance History</h3>
          <a href="{{ route('partner.maintenance.create') }}" class="text-[#C41E3A] text-xs hover:underline">+ Log Service</a>
        </div>
        @forelse($motorcycle->maintenanceLogs as $log)
        <div class="flex items-start gap-3 py-3 border-b border-slate-50 last:border-0">
          <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35z"/></svg>
          </div>
          <div class="flex-1">
            <div class="text-sm font-medium text-slate-800">{{ $log->description }}</div>
            <div class="text-xs text-slate-500">{{ $log->service_date->format('d M Y') }} · {{ $log->workshop_name ?? 'Workshop' }} · ₦{{ number_format($log->cost,0) }}</div>
            @if($log->next_service_due)
            <div class="text-xs text-amber-600 mt-0.5">Next service: {{ $log->next_service_due->format('d M Y') }}</div>
            @endif
          </div>
          <span class="badge-{{ ['completed'=>'success','in_progress'=>'warning','scheduled'=>'info','cancelled'=>'gray'][$log->status] ?? 'gray' }} flex-shrink-0">{{ ucfirst(str_replace('_',' ',$log->status)) }}</span>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-6">No maintenance records</p>
        @endforelse
      </div>

      {{-- Assignment History --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Assignment History</h3>
        @forelse($motorcycle->assignments as $assignment)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
          <div class="flex items-center gap-3">
            <div class="w-7 h-7 bg-slate-100 rounded-full flex items-center justify-center text-xs font-bold text-slate-600">
              {{ strtoupper(substr($assignment->rider->first_name ?? 'R', 0, 1)) }}
            </div>
            <div>
              <div class="text-sm font-medium text-slate-800">{{ $assignment->rider->full_name ?? 'Unknown' }}</div>
              <div class="text-xs text-slate-500">{{ $assignment->assigned_date->format('d M Y') }} – {{ $assignment->unassigned_date?->format('d M Y') ?? 'Present' }}</div>
            </div>
          </div>
          <span class="badge-{{ ['active'=>'success','completed'=>'info','terminated'=>'danger'][$assignment->status] ?? 'gray' }}">{{ ucfirst($assignment->status) }}</span>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-4">No assignments yet</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
