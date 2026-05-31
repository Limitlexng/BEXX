<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user()->partner;

        if (!$partner) {
            return redirect()->route('onboarding');
        }

        if ($partner->status === 'pending') {
            return view('partner.pending');
        }

        $stats = [
            'total_motorcycles' => $partner->motorcycles()->count(),
            'active_motorcycles' => $partner->motorcycles()->where('status', 'active')->count(),
            'maintenance_motorcycles' => $partner->motorcycles()->where('status', 'maintenance')->count(),
            'total_riders' => $partner->riders()->count(),
            'active_riders' => $partner->riders()->where('status', 'active')->count(),
            'inactive_riders' => $partner->riders()->where('status', 'inactive')->count(),
            'fleet_utilization' => $partner->fleet_utilization_rate,
            'wallet_balance' => $partner->wallet_balance,
            'pending_balance' => $partner->pending_balance,
            'lifetime_earnings' => $partner->lifetime_earnings,
        ];

        $earnings = [
            'today' => $partner->earnings()->whereDate('earning_date', today())->sum('net_amount'),
            'this_week' => $partner->earnings()->whereBetween('earning_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('net_amount'),
            'this_month' => $partner->earnings()->whereMonth('earning_date', now()->month)->whereYear('earning_date', now()->year)->sum('net_amount'),
            'this_year' => $partner->earnings()->whereYear('earning_date', now()->year)->sum('net_amount'),
        ];

        $compliance = [
            'score' => $this->calculateComplianceScore($partner),
            'expiring_soon' => $partner->complianceRecords()->where('status', 'expiring_soon')->count(),
            'expired' => $partner->complianceRecords()->where('status', 'expired')->count(),
            'violations' => $partner->complianceRecords()->where('status', 'violation')->where('resolved', false)->count(),
        ];

        $recentAlerts = $partner->alerts()->latest()->take(5)->get();

        $topMotorcycles = $partner->motorcycles()
            ->orderBy('total_earnings', 'desc')
            ->take(5)
            ->get();

        $earningsTrend = $partner->earnings()
            ->selectRaw('DATE(earning_date) as date, SUM(net_amount) as total')
            ->where('earning_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('partner.dashboard', compact(
            'partner', 'stats', 'earnings', 'compliance', 'recentAlerts', 'topMotorcycles', 'earningsTrend'
        ));
    }

    private function calculateComplianceScore($partner): float
    {
        $total = $partner->complianceRecords()->count();
        if ($total === 0) return 100;

        $violations = $partner->complianceRecords()
            ->whereIn('status', ['expired', 'violation'])
            ->where('resolved', false)
            ->count();

        return round((($total - $violations) / $total) * 100, 1);
    }
}
