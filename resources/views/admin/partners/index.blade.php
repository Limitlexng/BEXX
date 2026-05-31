@extends('layouts.app')
@section('title', 'Partners')
@section('page-title', 'Fleet Partners')
@section('page-subtitle', 'Manage investor and fleet company accounts')

@section('content')
<div class="py-4 space-y-5">
  {{-- Filters --}}
  <div class="flex items-center gap-2 flex-wrap">
    @foreach(['all'=>'All','pending'=>'Pending','active'=>'Active','suspended'=>'Suspended'] as $s=>$l)
    <a href="{{ route('admin.partners.index', ['status'=>$s]) }}"
       class="px-4 py-1.5 rounded-xl text-sm font-medium transition-colors {{ $status === $s ? 'bg-[#C41E3A] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-[#C41E3A]/40' }}">
      {{ $l }}
    </a>
    @endforeach
    @foreach(['independent_investor'=>'Investors','logistics_company'=>'Logistics','corporate_fleet_partner'=>'Corporate'] as $t=>$l)
    <a href="{{ route('admin.partners.index', ['status'=>$status,'type'=>$t]) }}"
       class="px-4 py-1.5 rounded-xl text-sm font-medium transition-colors {{ $type === $t ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-400' }}">
      {{ $l }}
    </a>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Partner</th>
            <th class="table-header">Code</th>
            <th class="table-header">Type</th>
            <th class="table-header">Motorcycles</th>
            <th class="table-header">Riders</th>
            <th class="table-header">Balance</th>
            <th class="table-header">Joined</th>
            <th class="table-header">Status</th>
            <th class="table-header">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($partners as $p)
          <tr class="hover:bg-slate-50/50">
            <td class="table-cell">
              <div class="font-medium text-slate-900">{{ $p->display_name }}</div>
              <div class="text-xs text-slate-500">{{ $p->user->email }}</div>
            </td>
            <td class="table-cell"><span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $p->partner_code }}</span></td>
            <td class="table-cell text-xs capitalize text-slate-600">{{ str_replace('_',' ',$p->partner_type) }}</td>
            <td class="table-cell text-center font-semibold text-slate-700">{{ $p->motorcycles_count }}</td>
            <td class="table-cell text-center font-semibold text-slate-700">{{ $p->riders_count }}</td>
            <td class="table-cell font-semibold text-emerald-700">₦{{ number_format($p->wallet_balance,0) }}</td>
            <td class="table-cell text-slate-500 text-xs">{{ $p->created_at->format('d M Y') }}</td>
            <td class="table-cell">
              <span class="badge-{{ ['active'=>'success','pending'=>'warning','suspended'=>'danger'][$p->status] ?? 'gray' }}">{{ ucfirst($p->status) }}</span>
            </td>
            <td class="table-cell">
              <div class="flex items-center gap-2">
                <a href="{{ route('admin.partners.show',$p) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View</a>
                @if($p->status === 'pending')
                <form method="POST" action="{{ route('admin.partners.approve',$p) }}">
                  @csrf
                  <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">Approve</button>
                </form>
                @elseif($p->status === 'active')
                <form method="POST" action="{{ route('admin.partners.suspend',$p) }}" onsubmit="return confirm('Suspend this partner?')">
                  @csrf
                  <button type="submit" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Suspend</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="table-cell text-center text-slate-400 py-10">No partners found</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $partners->links() }}</div>
  </div>
</div>
@endsection
