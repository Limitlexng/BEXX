<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Services\PartnerService;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct(private PartnerService $partnerService) {}

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $type = $request->get('type');

        $partners = Partner::with('user')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->when($type, fn($q) => $q->where('partner_type', $type))
            ->withCount(['motorcycles', 'riders'])
            ->latest()
            ->paginate(20);

        return view('admin.partners.index', compact('partners', 'status', 'type'));
    }

    public function show(Partner $partner)
    {
        $partner->load(['user', 'motorcycles', 'riders', 'walletTransactions' => fn($q) => $q->latest()->limit(10)]);
        return view('admin.partners.show', compact('partner'));
    }

    public function approve(Request $request, Partner $partner)
    {
        $this->partnerService->approvePartner($partner, $request->user());
        return back()->with('success', "Partner {$partner->display_name} approved successfully.");
    }

    public function suspend(Partner $partner)
    {
        $partner->update(['status' => 'suspended']);
        return back()->with('success', "Partner {$partner->display_name} has been suspended.");
    }

    public function uploadEarnings(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
            'rider_id' => 'nullable|exists:riders,id',
            'amount' => 'required|numeric|min:0',
            'platform_fee' => 'required|numeric|min:0',
            'earning_date' => 'required|date',
            'period_type' => 'required|in:daily,weekly,monthly',
            'source' => 'required|in:delivery,rental,bonus,adjustment',
            'notes' => 'nullable|string',
        ]);

        $netAmount = $validated['amount'] - $validated['platform_fee'];

        $earning = $partner->earnings()->create([
            ...$validated,
            'net_amount' => $netAmount,
            'status' => 'confirmed',
        ]);

        // Credit wallet
        app(\App\Services\PartnerService::class)->creditWallet(
            $partner, $netAmount, "Earnings for {$validated['earning_date']}", $earning->id
        );

        // Update motorcycle and rider total earnings
        if ($earning->motorcycle_id) {
            $earning->motorcycle->increment('total_earnings', $netAmount);
        }
        if ($earning->rider_id) {
            $earning->rider->increment('total_earnings', $netAmount);
        }

        return back()->with('success', "₦" . number_format($netAmount, 2) . " earnings uploaded successfully.");
    }
}
