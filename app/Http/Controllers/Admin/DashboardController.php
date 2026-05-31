<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Motorcycle;
use App\Models\Partner;
use App\Models\Rider;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_partners' => Partner::count(),
            'pending_partners' => Partner::where('status', 'pending')->count(),
            'active_partners' => Partner::where('status', 'active')->count(),
            'total_motorcycles' => Motorcycle::count(),
            'active_motorcycles' => Motorcycle::where('status', 'active')->count(),
            'total_riders' => Rider::count(),
            'active_riders' => Rider::where('status', 'active')->count(),
            'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
            'total_earnings_today' => Earning::whereDate('earning_date', today())->sum('net_amount'),
            'total_earnings_month' => Earning::whereMonth('earning_date', now()->month)->whereYear('earning_date', now()->year)->sum('net_amount'),
            'total_earnings_lifetime' => Earning::sum('net_amount'),
            'platform_fees_month' => Earning::whereMonth('earning_date', now()->month)->sum('platform_fee'),
        ];

        $recentPartners = Partner::with('user')->latest()->take(5)->get();
        $pendingWithdrawals = WithdrawalRequest::with('partner.user')->where('status', 'pending')->latest()->take(10)->get();
        $recentEarnings = Earning::with(['partner', 'motorcycle', 'rider'])->latest('earning_date')->take(10)->get();

        $earningsTrend = Earning::selectRaw('DATE(earning_date) as date, SUM(net_amount) as total, SUM(platform_fee) as fees')
            ->where('earning_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'recentPartners', 'pendingWithdrawals', 'recentEarnings', 'earningsTrend'
        ));
    }
}
