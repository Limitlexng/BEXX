<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $withdrawals = WithdrawalRequest::with('partner.user')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.withdrawals.index', compact('withdrawals', 'status'));
    }

    public function approve(Request $request, WithdrawalRequest $withdrawal)
    {
        $withdrawal->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
        ]);

        return back()->with('success', "Withdrawal #{$withdrawal->reference} approved.");
    }

    public function complete(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate(['payment_proof' => 'nullable|string']);

        $withdrawal->update([
            'status' => 'completed',
            'processed_at' => now(),
            'payment_proof' => $request->payment_proof,
        ]);

        $partner = $withdrawal->partner;
        $partner->decrement('pending_balance', $withdrawal->amount);
        $partner->increment('total_withdrawn', $withdrawal->amount);

        return back()->with('success', "Withdrawal #{$withdrawal->reference} marked as completed.");
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate(['rejection_reason' => 'required|string|min:10']);

        $withdrawal->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $partner = $withdrawal->partner;
        $partner->decrement('pending_balance', $withdrawal->amount);
        $partner->increment('wallet_balance', $withdrawal->amount);

        return back()->with('success', "Withdrawal rejected and funds returned to wallet.");
    }
}
