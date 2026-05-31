<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user()->partner;

        $records = $partner->complianceRecords()
            ->with(['rider', 'motorcycle'])
            ->latest()
            ->paginate(20);

        $score = $this->calculateScore($partner);

        $stats = [
            'total' => $partner->complianceRecords()->count(),
            'valid' => $partner->complianceRecords()->where('status', 'valid')->count(),
            'expiring_soon' => $partner->complianceRecords()->where('status', 'expiring_soon')->count(),
            'expired' => $partner->complianceRecords()->where('status', 'expired')->count(),
            'violations' => $partner->complianceRecords()->where('status', 'violation')->where('resolved', false)->count(),
        ];

        return view('partner.compliance.index', compact('partner', 'records', 'score', 'stats'));
    }

    private function calculateScore($partner): float
    {
        $total = $partner->complianceRecords()->count();
        if ($total === 0) return 100;

        $bad = $partner->complianceRecords()
            ->whereIn('status', ['expired', 'violation'])
            ->where('resolved', false)
            ->count();

        return round((($total - $bad) / $total) * 100, 1);
    }
}
