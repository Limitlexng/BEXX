@extends('layouts.app')
@section('title', $rider->full_name)
@section('page-title', $rider->full_name)
@section('page-subtitle', $rider->rider_id . ' · ' . ucfirst($rider->status))

@section('header-actions')
<form method="POST" action="{{ $rider->status === 'active' ? route('partner.riders.suspend', $rider) : route('partner.riders.activate', $rider) }}"
      onsubmit="return confirm('{{ $rider->status === 'active' ? 'Suspend' : 'Activate' }} this rider?')">
  @csrf
  <button type="submit" class="{{ $rider->status === 'active' ? 'btn-secondary' : 'btn-primary' }}">
    {{ $rider->status === 'active' ? 'Suspend Rider' : 'Activate Rider' }}
  </button>
</form>
@endsection

@section('content')
<div class="py-4 space-y-6">

  {{-- Rider Profile Card --}}
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
    <div class="flex items-start gap-6">
      <div class="w-20 h-20 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-700 font-bold text-3xl flex-shrink-0">
        {{ strtoupper(substr($rider->first_name,0,1)) }}
      </div>
      <div class="flex-1">
        <div class="flex items-center gap-3 mb-1">
          <h2 class="text-xl font-bold text-slate-900">{{ $rider->full_name }}</h2>
          <span class="badge-{{ ['active'=>'success','inactive'=>'gray','suspended'=>'warning','terminated'=>'danger'][$rider->status] ?? 'gray' }}">{{ ucfirst($rider->status) }}</span>
        </div>
        <div class="text-slate-500 text-sm mb-3">{{ $rider->rider_id }} · {{ $rider->phone }} @if($rider->email) · {{ $rider->email }} @endif</div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div class="bg-slate-50 rounded-xl p-3 text-center">
            <div class="text-lg font-bold text-emerald-700">₦{{ number_format($rider->total_earnings,0) }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Total Earnings</div>
          </div>
          <div class="bg-slate-50 rounded-xl p-3 text-center">
            <div class="text-lg font-bold text-slate-900">{{ number_format($rider->total_deliveries) }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Deliveries</div>
          </div>
          <div class="bg-slate-50 rounded-xl p-3 text-center">
            <div class="text-lg font-bold text-{{ $rider->compliance_score >= 80 ? 'emerald' : 'amber' }}-700">{{ $rider->compliance_score }}%</div>
            <div class="text-xs text-slate-500 mt-0.5">Compliance</div>
          </div>
          <div class="bg-slate-50 rounded-xl p-3 text-center">
            <div class="text-lg font-bold text-blue-700">{{ $rider->performance_score }}%</div>
            <div class="text-xs text-slate-500 mt-0.5">Performance</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid lg:grid-cols-3 gap-6">
    <div class="space-y-4">
      {{-- ID Card --}}
      @if($rider->activeIdCard)
      @php $card = $rider->activeIdCard @endphp
      <div class="bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45] rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-slate-400 text-xs uppercase tracking-wider">Cartlex Rider ID</div>
            <div class="text-white font-mono font-bold text-sm mt-1">{{ $card->card_number }}</div>
          </div>
          <div class="w-10 h-10 bg-[#C41E3A] rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
        </div>
        <div class="flex items-center gap-3 mb-4">
          <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center text-2xl font-bold text-white">{{ strtoupper(substr($rider->first_name,0,1)) }}</div>
          <div>
            <div class="text-white font-semibold">{{ $rider->full_name }}</div>
            <div class="text-slate-400 text-xs">{{ $rider->partner->display_name }}</div>
            @if($rider->currentMotorcycle)
            <div class="text-slate-400 text-xs">{{ $rider->currentMotorcycle->fleet_id }}</div>
            @endif
          </div>
        </div>
        <div class="border-t border-white/10 pt-3 grid grid-cols-2 gap-2 text-xs">
          <div><div class="text-slate-400">Issued</div><div class="text-white font-medium">{{ $card->issue_date->format('d M Y') }}</div></div>
          <div><div class="text-slate-400">Expires</div><div class="text-white font-medium">{{ $card->expiry_date->format('d M Y') }}</div></div>
        </div>
        @if($card->qr_code_path && file_exists(storage_path('app/public/'.$card->qr_code_path)))
        <div class="mt-3 flex justify-center bg-white p-2 rounded-lg">
          <img src="{{ asset('storage/'.$card->qr_code_path) }}" alt="QR Code" class="w-24 h-24">
        </div>
        @endif
        <a href="{{ $card->verification_url }}" target="_blank" class="mt-3 block text-center text-xs text-slate-400 hover:text-white underline">Verify online</a>
      </div>
      <form method="POST" action="{{ route('partner.riders.generate-id-card', $rider) }}">
        @csrf
        <button type="submit" class="btn-secondary w-full justify-center text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          Regenerate ID Card
        </button>
      </form>
      @else
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 text-center">
        <div class="text-slate-400 text-sm mb-3">No active ID card</div>
        <form method="POST" action="{{ route('partner.riders.generate-id-card', $rider) }}">
          @csrf
          <button type="submit" class="btn-primary w-full justify-center text-sm">Generate ID Card</button>
        </form>
      </div>
      @endif

      {{-- Personal Details --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Personal Details</h3>
        <dl class="space-y-2.5">
          @foreach([
            'Phone' => $rider->phone,
            'Email' => $rider->email ?? '—',
            'DOB' => $rider->date_of_birth?->format('d M Y') ?? '—',
            'NIN' => $rider->nin ?? '—',
            'License No.' => $rider->license_number ?? '—',
            'License Expiry' => $rider->license_expiry?->format('d M Y') ?? '—',
            'Emergency Contact' => $rider->emergency_contact_name ?? '—',
            'Emergency Phone' => $rider->emergency_contact_phone ?? '—',
          ] as $label => $value)
          <div class="flex justify-between py-1.5 border-b border-slate-50">
            <dt class="text-xs text-slate-500">{{ $label }}</dt>
            <dd class="text-xs font-medium text-slate-800 text-right">{{ $value }}</dd>
          </div>
          @endforeach
        </dl>
      </div>
    </div>

    <div class="lg:col-span-2 space-y-5">
      {{-- Recent Earnings --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Recent Earnings</h3>
        @forelse($rider->earnings as $earning)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
          <div>
            <div class="text-sm font-medium text-slate-800">{{ $earning->earning_date->format('d M Y') }}</div>
            <div class="text-xs text-slate-500 capitalize">{{ $earning->source }}</div>
          </div>
          <div class="text-sm font-bold text-emerald-700">₦{{ number_format($earning->net_amount,2) }}</div>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-6">No earnings recorded yet</p>
        @endforelse
      </div>

      {{-- Assignment History --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Assignment History</h3>
        @forelse($rider->assignments as $assignment)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
          <div>
            <div class="text-sm font-medium text-slate-800">{{ $assignment->motorcycle->fleet_id ?? '—' }} – {{ $assignment->motorcycle->full_name ?? '' }}</div>
            <div class="text-xs text-slate-500">{{ $assignment->assigned_date->format('d M Y') }} – {{ $assignment->unassigned_date?->format('d M Y') ?? 'Present' }}</div>
          </div>
          <span class="badge-{{ ['active'=>'success','completed'=>'info','terminated'=>'danger'][$assignment->status] ?? 'gray' }}">{{ ucfirst($assignment->status) }}</span>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-4">No assignments</p>
        @endforelse
      </div>

      {{-- Compliance Records --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Compliance Records</h3>
        @forelse($rider->complianceRecords as $record)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
          <div>
            <div class="text-sm font-medium text-slate-800">{{ $record->title }}</div>
            <div class="text-xs text-slate-500 capitalize">{{ str_replace('_',' ',$record->type) }} @if($record->expiry_date) · Expires {{ $record->expiry_date->format('d M Y') }} @endif</div>
          </div>
          <span class="badge-{{ ['valid'=>'success','expiring_soon'=>'warning','expired'=>'danger','violation'=>'danger'][$record->status] ?? 'gray' }}">{{ ucfirst(str_replace('_',' ',$record->status)) }}</span>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-4">No compliance records</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
