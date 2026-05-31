<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use Illuminate\Http\Request;

class FleetOverviewController extends Controller
{
    public function index(Request $request)
    {
        $motorcycles = Motorcycle::with(['partner', 'currentRider'])
            ->withCount(['maintenanceLogs', 'earnings'])
            ->paginate(30);

        $stats = [
            'total' => Motorcycle::count(),
            'active' => Motorcycle::where('status', 'active')->count(),
            'maintenance' => Motorcycle::where('status', 'maintenance')->count(),
            'suspended' => Motorcycle::where('status', 'suspended')->count(),
            'retired' => Motorcycle::where('status', 'retired')->count(),
        ];

        return view('admin.fleet.index', compact('motorcycles', 'stats'));
    }
}
