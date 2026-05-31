<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use App\Services\FleetService;
use App\Services\RiderIdCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RiderController extends Controller
{
    public function __construct(
        private FleetService $fleetService,
        private RiderIdCardService $idCardService,
    ) {}

    public function index(Request $request)
    {
        $partner = $request->user()->partner;
        $status = $request->get('status');

        $riders = $partner->riders()
            ->with(['currentMotorcycle', 'activeIdCard'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('partner.riders.index', compact('partner', 'riders', 'status'));
    }

    public function create(Request $request)
    {
        $partner = $request->user()->partner;
        $availableMotorcycles = $partner->motorcycles()
            ->where('status', 'active')
            ->whereDoesntHave('currentRider')
            ->get();

        return view('partner.riders.create', compact('partner', 'availableMotorcycles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'nin' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50',
            'license_expiry' => 'nullable|date',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
        ]);

        $partner = $request->user()->partner;

        $rider = $partner->riders()->create([
            ...$validated,
            'rider_id' => 'CTR-RID-' . str_pad(Rider::max('id') + 1, 4, '0', STR_PAD_LEFT),
            'status' => 'active',
        ]);

        if ($validated['motorcycle_id'] ?? null) {
            $motorcycle = $partner->motorcycles()->find($validated['motorcycle_id']);
            if ($motorcycle) {
                $this->fleetService->assignRider($motorcycle, $rider);
            }
        }

        // Generate ID card
        $this->idCardService->generateIdCard($rider);

        return redirect()->route('partner.riders.show', $rider)
            ->with('success', "Rider {$rider->full_name} registered and ID card generated.");
    }

    public function show(Request $request, Rider $rider)
    {
        $this->authorize('view', $rider);

        $rider->load([
            'currentMotorcycle',
            'assignments.motorcycle',
            'earnings' => fn($q) => $q->latest('earning_date')->limit(10),
            'complianceRecords',
            'idCards',
            'documents',
        ]);

        return view('partner.riders.show', compact('rider'));
    }

    public function generateIdCard(Request $request, Rider $rider)
    {
        $this->authorize('update', $rider);
        $card = $this->idCardService->generateIdCard($rider);

        return redirect()->route('partner.riders.show', $rider)
            ->with('success', 'New ID card generated: ' . $card->card_number);
    }

    public function suspend(Request $request, Rider $rider)
    {
        $this->authorize('update', $rider);
        $rider->update(['status' => 'suspended']);
        $this->fleetService->unassignRider($rider);

        return back()->with('success', "Rider {$rider->full_name} has been suspended.");
    }

    public function activate(Request $request, Rider $rider)
    {
        $this->authorize('update', $rider);
        $rider->update(['status' => 'active']);

        return back()->with('success', "Rider {$rider->full_name} has been activated.");
    }
}
