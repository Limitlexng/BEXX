<?php

namespace App\Http\Controllers;

use App\Services\PartnerService;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(private PartnerService $partnerService) {}

    public function show(Request $request)
    {
        if ($request->user()->partner) {
            return redirect()->route('partner.dashboard');
        }
        return view('onboarding');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_type' => 'required|in:independent_investor,logistics_company,corporate_fleet_partner',
            'company_name' => 'required_if:partner_type,logistics_company,corporate_fleet_partner|nullable|string|max:255',
            'contact_person' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'cac_number' => 'required_if:partner_type,logistics_company,corporate_fleet_partner|nullable|string|max:50',
        ]);

        $this->partnerService->createPartner($request->user(), $validated);

        return redirect()->route('partner.dashboard')
            ->with('success', 'Your partner account has been submitted for review.');
    }
}
