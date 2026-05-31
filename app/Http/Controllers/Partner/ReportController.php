<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user()->partner;
        return view('partner.reports.index', compact('partner'));
    }

    public function fleet(Request $request)
    {
        $partner = $request->user()->partner;
        $motorcycles = $partner->motorcycles()->with(['currentRider', 'maintenanceLogs'])->get();
        return view('partner.reports.fleet', compact('partner', 'motorcycles'));
    }

    public function earnings(Request $request)
    {
        $partner = $request->user()->partner;
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $earnings = $partner->earnings()
            ->with(['motorcycle', 'rider'])
            ->whereBetween('earning_date', [$from, $to])
            ->latest('earning_date')
            ->get();

        $total = $earnings->sum('net_amount');

        return view('partner.reports.earnings', compact('partner', 'earnings', 'total', 'from', 'to'));
    }
}
