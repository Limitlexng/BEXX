<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Partner;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $summary = [
            'total_earnings' => Earning::whereBetween('earning_date', [$from, $to])->sum('net_amount'),
            'total_fees' => Earning::whereBetween('earning_date', [$from, $to])->sum('platform_fee'),
            'total_partners' => Partner::count(),
        ];

        return view('admin.reports.index', compact('summary', 'from', 'to'));
    }
}
