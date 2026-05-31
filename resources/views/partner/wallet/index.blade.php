@extends('layouts.app')
@section('title', 'Wallet')
@section('page-title', 'Wallet & Payouts')
@section('page-subtitle', 'Manage your earnings and withdrawal requests')

@section('content')
<div class="py-4 space-y-6">

  {{-- Wallet Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45] rounded-2xl p-6 text-white shadow-lg">
      <div class="text-slate-400 text-xs uppercase tracking-wider mb-1">Available Balance</div>
      <div class="text-3xl font-bold mt-1">₦{{ number_format($partner->wallet_balance,2) }}</div>
      <div class="text-slate-400 text-xs mt-2">Ready to withdraw</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="text-slate-500 text-xs uppercase tracking-wider mb-1">Pending</div>
      <div class="text-2xl font-bold text-amber-600">₦{{ number_format($partner->pending_balance,2) }}</div>
      <div class="text-slate-400 text-xs mt-2">Awaiting processing</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
      <div class="text-slate-500 text-xs uppercase tracking-wider mb-1">Total Withdrawn</div>
      <div class="text-2xl font-bold text-emerald-600">₦{{ number_format($partner->total_withdrawn,2) }}</div>
      <div class="text-slate-400 text-xs mt-2">All time payouts</div>
    </div>
  </div>

  <div class="grid lg:grid-cols-5 gap-6">

    {{-- Withdrawal Form --}}
    <div class="lg:col-span-2">
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-5">Request Withdrawal</h3>

        @if($partner->withdrawalRequests()->where('status','pending')->exists())
        <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl mb-4">
          <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <p class="text-xs text-amber-700">You have a pending withdrawal request. Wait for it to be processed before submitting a new one.</p>
        </div>
        @endif

        <form method="POST" action="{{ route('partner.wallet.withdraw') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Amount (₦) <span class="text-red-500">*</span></label>
            <input type="number" name="amount" value="{{ old('amount') }}" required min="1000" max="{{ $partner->wallet_balance }}" step="0.01"
                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
                   placeholder="Minimum ₦1,000">
            <p class="text-xs text-slate-400 mt-1">Available: ₦{{ number_format($partner->wallet_balance,2) }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Bank Name <span class="text-red-500">*</span></label>
            <input type="text" name="bank_name" value="{{ old('bank_name', $partner->bank_name) }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]" placeholder="e.g. GTBank">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Account Number <span class="text-red-500">*</span></label>
            <input type="text" name="account_number" value="{{ old('account_number', $partner->bank_account_number) }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Account Name <span class="text-red-500">*</span></label>
            <input type="text" name="account_name" value="{{ old('account_name', $partner->bank_account_name) }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <button type="submit" class="btn-primary w-full justify-center"
                  {{ $partner->wallet_balance < 1000 || $partner->withdrawalRequests()->where('status','pending')->exists() ? 'disabled' : '' }}>
            Submit Withdrawal Request
          </button>
        </form>
      </div>
    </div>

    {{-- Transactions & Withdrawals --}}
    <div class="lg:col-span-3 space-y-5">

      {{-- Withdrawal History --}}
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Withdrawal Requests</h3>
        @forelse($withdrawals as $wd)
        <div class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
          <div>
            <div class="text-sm font-medium text-slate-800">₦{{ number_format($wd->amount,2) }}</div>
            <div class="text-xs text-slate-500">{{ $wd->bank_name }} · {{ $wd->account_number }}</div>
            <div class="text-xs text-slate-400">{{ $wd->created_at->format('d M Y, H:i') }}</div>
          </div>
          <div class="text-right">
            <span class="badge-{{ ['pending'=>'warning','approved'=>'info','processing'=>'info','completed'=>'success','rejected'=>'danger'][$wd->status] ?? 'gray' }}">{{ ucfirst($wd->status) }}</span>
            <div class="text-xs font-mono text-slate-400 mt-1">{{ $wd->reference }}</div>
          </div>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-6">No withdrawal requests yet</p>
        @endforelse
      </div>

      {{-- Transaction History --}}
      <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
          <h3 class="text-sm font-bold text-slate-900">Transaction History</h3>
        </div>
        <div class="divide-y divide-slate-50">
          @forelse($transactions as $txn)
          <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-slate-50/50 transition-colors">
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
              {{ $txn->type === 'credit' || $txn->type === 'bonus' ? 'bg-emerald-100' : 'bg-red-50' }}">
              @if(in_array($txn->type,['credit','bonus']))
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
              @else
              <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
              @endif
            </div>
            <div class="flex-1">
              <div class="text-sm font-medium text-slate-800">{{ $txn->description }}</div>
              <div class="text-xs text-slate-400">{{ $txn->created_at->format('d M Y, H:i') }} · {{ $txn->reference }}</div>
            </div>
            <div class="text-right">
              <div class="text-sm font-bold {{ in_array($txn->type,['credit','bonus']) ? 'text-emerald-700' : 'text-red-600' }}">
                {{ in_array($txn->type,['credit','bonus']) ? '+' : '-' }}₦{{ number_format($txn->amount,2) }}
              </div>
              <div class="text-xs text-slate-400">Bal: ₦{{ number_format($txn->balance_after,2) }}</div>
            </div>
          </div>
          @empty
          <div class="px-6 py-10 text-center text-slate-400 text-sm">No transactions yet</div>
          @endforelse
        </div>
        <div class="px-6 py-4 border-t border-slate-100">{{ $transactions->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
