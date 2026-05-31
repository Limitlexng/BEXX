@extends('layouts.app')
@section('title', 'Fleet Management')
@section('page-title', 'Fleet Management')
@section('page-subtitle', 'Manage and monitor your motorcycle assets')

@section('header-actions')
<a href="{{ route('partner.fleet.create') }}" class="btn-primary">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Register Motorcycle
</a>
@endsection

@section('content')
<div class="py-4 space-y-6">

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @php
            $statuses = ['all' => 'All Assets', 'active' => 'Active', 'maintenance' => 'Maintenance', 'suspended' => 'Suspended', 'retired' => 'Retired'];
            $colors = ['all' => 'slate', 'active' => 'emerald', 'maintenance' => 'amber', 'suspended' => 'red', 'retired' => 'gray'];
        @endphp
        @foreach($statuses as $key => $label)
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 text-center">
            <div class="text-2xl font-bold text-slate-900">
                {{ $key === 'all' ? $motorcycles->total() : $partner->motorcycles()->where('status', $key)->count() }}
            </div>
            <div class="text-xs text-slate-500 mt-1">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- Fleet Grid --}}
    @if($motorcycles->isEmpty())
    <div class="bg-white rounded-2xl p-16 shadow-sm border border-slate-100 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2">No motorcycles yet</h3>
        <p class="text-slate-500 text-sm mb-6">Register your first motorcycle to start tracking your fleet assets.</p>
        <a href="{{ route('partner.fleet.create') }}" class="btn-primary inline-flex">Register First Motorcycle</a>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="table-header">Fleet ID</th>
                        <th class="table-header">Motorcycle</th>
                        <th class="table-header">Plate</th>
                        <th class="table-header">Assigned Rider</th>
                        <th class="table-header">Insurance</th>
                        <th class="table-header">Health</th>
                        <th class="table-header">Total Earnings</th>
                        <th class="table-header">Status</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($motorcycles as $moto)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="table-cell">
                            <span class="font-mono text-xs font-bold text-[#C41E3A] bg-[#C41E3A]/5 px-2 py-1 rounded-lg">{{ $moto->fleet_id }}</span>
                        </td>
                        <td class="table-cell">
                            <div class="font-medium text-slate-900">{{ $moto->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $moto->color }}</div>
                        </td>
                        <td class="table-cell">
                            <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded">{{ $moto->plate_number ?? 'N/A' }}</span>
                        </td>
                        <td class="table-cell">
                            @if($moto->currentRider)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <span class="text-emerald-700 text-xs font-bold">{{ substr($moto->currentRider->first_name, 0, 1) }}</span>
                                </div>
                                <span class="text-sm">{{ $moto->currentRider->full_name }}</span>
                            </div>
                            @else
                            <span class="text-slate-400 text-xs">Unassigned</span>
                            @endif
                        </td>
                        <td class="table-cell">
                            @php $days = $moto->insurance_days_remaining @endphp
                            @if($moto->insurance_expiry)
                                @if($days < 0)
                                <span class="badge-danger">Expired</span>
                                @elseif($days <= 30)
                                <span class="badge-warning">{{ $days }}d left</span>
                                @else
                                <span class="badge-success">Valid</span>
                                @endif
                            @else
                            <span class="badge-gray">No data</span>
                            @endif
                        </td>
                        <td class="table-cell">
                            @php
                                $healthColors = ['excellent' => 'emerald', 'good' => 'blue', 'average' => 'amber', 'poor' => 'orange', 'critical' => 'red'];
                                $hc = $healthColors[$moto->health_rating] ?? 'slate';
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-slate-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full bg-{{ $hc }}-500" style="width: {{ $moto->health_score }}%"></div>
                                </div>
                                <span class="text-xs capitalize text-slate-600">{{ ucfirst($moto->health_rating) }}</span>
                            </div>
                        </td>
                        <td class="table-cell">
                            <span class="font-semibold text-emerald-700">₦{{ number_format($moto->total_earnings, 0) }}</span>
                        </td>
                        <td class="table-cell">
                            @php
                                $statusColors = ['active' => 'success', 'maintenance' => 'warning', 'suspended' => 'danger', 'retired' => 'gray', 'lost' => 'danger'];
                                $sc = $statusColors[$moto->status] ?? 'gray';
                            @endphp
                            <span class="badge-{{ $sc }}">{{ ucfirst($moto->status) }}</span>
                        </td>
                        <td class="table-cell">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('partner.fleet.show', $moto) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                                <a href="{{ route('partner.fleet.edit', $moto) }}" class="text-slate-500 hover:text-slate-700 text-xs font-medium">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $motorcycles->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
