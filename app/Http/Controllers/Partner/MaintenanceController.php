<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\Motorcycle;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user()->partner;
        $logs = $partner->maintenanceLogs()
            ->with('motorcycle')
            ->latest('service_date')
            ->paginate(20);

        $totalCost = $partner->maintenanceLogs()->sum('cost');
        $upcomingService = $partner->maintenanceLogs()
            ->whereNotNull('next_service_due')
            ->where('next_service_due', '>=', now())
            ->where('next_service_due', '<=', now()->addDays(30))
            ->with('motorcycle')
            ->orderBy('next_service_due')
            ->get();

        return view('partner.maintenance.index', compact('partner', 'logs', 'totalCost', 'upcomingService'));
    }

    public function create(Request $request)
    {
        $partner = $request->user()->partner;
        $motorcycles = $partner->motorcycles()->select('id', 'fleet_id', 'brand', 'model', 'year')->get();
        return view('partner.maintenance.create', compact('partner', 'motorcycles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'type' => 'required|in:routine_service,repair,inspection,emergency,upgrade',
            'description' => 'required|string|max:255',
            'details' => 'nullable|string',
            'workshop_name' => 'nullable|string|max:100',
            'technician_name' => 'nullable|string|max:100',
            'cost' => 'required|numeric|min:0',
            'service_date' => 'required|date',
            'next_service_due' => 'nullable|date|after:service_date',
            'downtime_hours' => 'nullable|integer|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $partner = $request->user()->partner;
        $log = $partner->maintenanceLogs()->create($validated);

        // Update motorcycle maintenance cost
        $log->motorcycle->increment('total_maintenance_cost', $validated['cost']);

        if ($validated['status'] === 'in_progress' || $validated['status'] === 'scheduled') {
            $log->motorcycle->update(['status' => 'maintenance']);
        }

        return redirect()->route('partner.maintenance.index')
            ->with('success', 'Maintenance log recorded successfully.');
    }
}
