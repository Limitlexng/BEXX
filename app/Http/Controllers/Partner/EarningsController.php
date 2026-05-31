<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user()->partner;
        $period = $request->get('period', 'monthly');
        $motorcycleId = $request->get('motorcycle_id');
        $riderId = $request->get('rider_id');

        $query = $partner->earnings()
            ->with(['motorcycle', 'rider'])
            ->when($motorcycleId, fn($q) => $q->where('motorcycle_id', $motorcycleId))
            ->when($riderId, fn($q) => $q->where('rider_id', $riderId));

        $earnings = $query->latest('earning_date')->paginate(30);

        $summary = [
            'today' => $partner->earnings()->whereDate('earning_date', today())->sum('net_amount'),
            'this_week' => $partner->earnings()->whereBetween('earning_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('net_amount'),
            'this_month' => $partner->earnings()->whereMonth('earning_date', now()->month)->whereYear('earning_date', now()->year)->sum('net_amount'),
            'this_year' => $partner->earnings()->whereYear('earning_date', now()->year)->sum('net_amount'),
            'lifetime' => $partner->lifetime_earnings,
        ];

        $byMotorcycle = $partner->motorcycles()
            ->select('id', 'fleet_id', 'brand', 'model', 'year', 'total_earnings')
            ->orderByDesc('total_earnings')
            ->get();

        $byRider = $partner->riders()
            ->select('id', 'rider_id', 'first_name', 'last_name', 'total_earnings', 'total_deliveries')
            ->orderByDesc('total_earnings')
            ->get();

        $trend = $partner->earnings()
            ->selectRaw('DATE(earning_date) as date, SUM(net_amount) as total')
            ->where('earning_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $motorcycles = $partner->motorcycles()->select('id', 'fleet_id', 'brand', 'model')->get();
        $riders = $partner->riders()->select('id', 'rider_id', 'first_name', 'last_name')->get();

        $avgPerMotorcycle = $partner->motorcycles()->count() > 0
            ? $summary['this_month'] / $partner->motorcycles()->count()
            : 0;

        $avgPerRider = $partner->riders()->count() > 0
            ? $summary['this_month'] / $partner->riders()->count()
            : 0;

        $purchaseCost = $partner->motorcycles()->sum('purchase_cost');
        $roi = $purchaseCost > 0 ? round(($partner->lifetime_earnings / $purchaseCost) * 100, 2) : 0;

        return view('partner.earnings.index', compact(
            'partner', 'earnings', 'summary', 'byMotorcycle', 'byRider',
            'trend', 'motorcycles', 'riders', 'avgPerMotorcycle', 'avgPerRider', 'roi'
        ));
    }
}
