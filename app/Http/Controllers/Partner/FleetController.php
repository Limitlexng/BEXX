<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use App\Services\FleetService;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    public function __construct(private FleetService $fleetService) {}

    public function index(Request $request)
    {
        $partner = $request->user()->partner;
        $motorcycles = $partner->motorcycles()
            ->with(['currentRider', 'maintenanceLogs' => fn($q) => $q->latest()->limit(1)])
            ->latest()
            ->paginate(20);

        return view('partner.fleet.index', compact('partner', 'motorcycles'));
    }

    public function create(Request $request)
    {
        $partner = $request->user()->partner;
        return view('partner.fleet.create', compact('partner'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:2010|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
            'plate_number' => 'nullable|string|max:20',
            'vin_number' => 'nullable|string|max:50',
            'engine_number' => 'nullable|string|max:50',
            'vehicle_number' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'insurance_provider' => 'nullable|string|max:100',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expiry' => 'nullable|date',
            'road_worthiness_expiry' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $partner = $request->user()->partner;
        $motorcycle = $this->fleetService->registerMotorcycle($partner, $validated);

        return redirect()->route('partner.fleet.show', $motorcycle)
            ->with('success', "Motorcycle {$motorcycle->fleet_id} registered successfully.");
    }

    public function show(Request $request, Motorcycle $motorcycle)
    {
        $this->authorize('view', $motorcycle);

        $motorcycle->load([
            'currentRider',
            'assignments.rider',
            'earnings' => fn($q) => $q->latest('earning_date')->limit(10),
            'maintenanceLogs' => fn($q) => $q->latest('service_date'),
            'complianceRecords',
            'documents',
        ]);

        $health = $this->fleetService->calculateHealthScore($motorcycle);
        $motorcycle->update(['health_score' => $health['score'], 'health_rating' => $health['rating']]);

        return view('partner.fleet.show', compact('motorcycle', 'health'));
    }

    public function edit(Request $request, Motorcycle $motorcycle)
    {
        $this->authorize('update', $motorcycle);
        return view('partner.fleet.edit', compact('motorcycle'));
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        $this->authorize('update', $motorcycle);

        $validated = $request->validate([
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:2010|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
            'plate_number' => 'nullable|string|max:20',
            'vin_number' => 'nullable|string|max:50',
            'engine_number' => 'nullable|string|max:50',
            'vehicle_number' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'insurance_provider' => 'nullable|string|max:100',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_expiry' => 'nullable|date',
            'insurance_status' => 'required|in:active,expired,pending',
            'road_worthiness_expiry' => 'nullable|date',
            'status' => 'required|in:active,maintenance,suspended,retired,lost',
            'notes' => 'nullable|string',
        ]);

        $motorcycle->update($validated);

        return redirect()->route('partner.fleet.show', $motorcycle)
            ->with('success', 'Motorcycle updated successfully.');
    }
}
