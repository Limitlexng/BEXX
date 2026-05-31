<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user()->partner;
        $alerts = $partner->alerts()->latest()->paginate(30);
        return view('partner.alerts.index', compact('partner', 'alerts'));
    }

    public function markRead(Request $request, Alert $alert)
    {
        abort_unless($request->user()->partner?->id === $alert->partner_id, 403);
        $alert->markAsRead();
        return back();
    }
}
