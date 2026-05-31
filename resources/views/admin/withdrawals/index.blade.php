@extends('layouts.app')
@section('title', 'Withdrawals')
@section('page-title', 'Withdrawal Requests')
@section('page-subtitle', 'Manage partner payout requests')

@section('content')
<div class="py-4 space-y-5">
  <div class="flex items-center gap-2">
    @foreach(['pending'=>'Pending','approved'=>'Approved','completed'=>'Completed','rejected'=>'Rejected','all'=>'All'] as $s=>$l)
    <a href="{{ route('admin.withdrawals.index', ['status'=>$s]) }}"
       class="px-4 py-1.5 rounded-xl text-sm font-medium transition-colors {{ $status === $s ? 'bg-[#C41E3A] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-[#C41E3A]/40' }}">
      {{ $l }}
    </a>
    @endforeach
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="table-header">Reference</th>
            <th class="table-header">Partner</th>
            <th class="table-header">Amount</th>
            <th class="table-header">Bank Details</th>
            <th class="table-header">Requested</th>
            <th class="table-header">Status</th>
            <th class="table-header">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          @forelse($withdrawals as $wd)
          <tr class="hover:bg-slate-50/50">
            <td class="table-cell"><span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $wd->reference }}</span></td>
            <td class="table-cell">
              <a href="{{ route('admin.partners.show',$wd->partner) }}" class="font-medium text-blue-600 hover:underline">{{ $wd->partner->display_name }}</a>
              <div class="text-xs text-slate-400">{{ $wd->partner->partner_code }}</div>
            </td>
            <td class="table-cell text-xl font-bold text-slate-900">₦{{ number_format($wd->amount,2) }}</td>
            <td class="table-cell">
              <div class="text-sm font-medium text-slate-800">{{ $wd->bank_name }}</div>
              <div class="text-xs text-slate-500">{{ $wd->account_number }} · {{ $wd->account_name }}</div>
            </td>
            <td class="table-cell text-slate-500 text-xs">{{ $wd->created_at->format('d M Y, H:i') }}</td>
            <td class="table-cell">
              <span class="badge-{{ ['pending'=>'warning','approved'=>'info','processing'=>'info','completed'=>'success','rejected'=>'danger'][$wd->status] ?? 'gray' }}">{{ ucfirst($wd->status) }}</span>
            </td>
            <td class="table-cell">
              <div class="flex items-center gap-2">
                @if($wd->status === 'pending')
                <form method="POST" action="{{ route('admin.withdrawals.approve',$wd) }}">
                  @csrf
                  <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.withdrawals.reject',$wd) }}" x-data x-on:submit.prevent="
                  const reason = prompt('Rejection reason (required):');
                  if(reason && reason.trim().length >= 10) {
                    document.getElementById('reject-reason-{{ $wd->id }}').value = reason;
                    $el.submit();
                  } else if(reason !== null) { alert('Please provide at least 10 characters.'); }">
                  @csrf
                  <input type="hidden" id="reject-reason-{{ $wd->id }}" name="rejection_reason" value="">
                  <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Reject</button>
                </form>
                @elseif($wd->status === 'approved')
                <form method="POST" action="{{ route('admin.withdrawals.complete',$wd) }}">
                  @csrf
                  <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Mark Complete</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="table-cell text-center text-slate-400 py-10">No withdrawal requests</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100">{{ $withdrawals->links() }}</div>
  </div>
</div>
@endsection
