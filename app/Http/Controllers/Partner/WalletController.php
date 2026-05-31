<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\PartnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function __construct(private PartnerService $partnerService) {}

    public function index(Request $request)
    {
        $partner = $request->user()->partner;

        $transactions = $partner->walletTransactions()->latest()->paginate(20);
        $withdrawals = $partner->withdrawalRequests()->latest()->paginate(10);

        return view('partner.wallet.index', compact('partner', 'transactions', 'withdrawals'));
    }

    public function requestWithdrawal(Request $request)
    {
        $partner = $request->user()->partner;

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000', "max:{$partner->wallet_balance}"],
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:100',
        ]);

        $pending = $partner->withdrawalRequests()->where('status', 'pending')->exists();
        if ($pending) {
            return back()->with('error', 'You already have a pending withdrawal request.');
        }

        $withdrawal = $partner->withdrawalRequests()->create([
            'reference' => 'WDR-' . strtoupper(Str::random(10)),
            'amount' => $validated['amount'],
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'status' => 'pending',
        ]);

        $partner->increment('pending_balance', $validated['amount']);
        $partner->decrement('wallet_balance', $validated['amount']);

        return back()->with('success', "Withdrawal request of ₦" . number_format($validated['amount'], 2) . " submitted successfully.");
    }
}
