<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $partner = $request->user()->partner;
        $documents = $partner->documents()
            ->with(['rider', 'motorcycle'])
            ->latest()
            ->paginate(20);

        return view('partner.documents.index', compact('partner', 'documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:cac_certificate,insurance,road_worthiness,vehicle_papers,purchase_receipt,agreement,rider_id,verification_doc,other',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
            'rider_id' => 'nullable|exists:riders,id',
            'motorcycle_id' => 'nullable|exists:motorcycles,id',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
        ]);

        $partner = $request->user()->partner;
        $file = $request->file('file');
        $path = $file->store("documents/partner-{$partner->id}", 'public');

        $partner->documents()->create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'rider_id' => $validated['rider_id'] ?? null,
            'motorcycle_id' => $validated['motorcycle_id'] ?? null,
            'issue_date' => $validated['issue_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'active',
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(Request $request, Document $document)
    {
        abort_unless($request->user()->partner?->id === $document->partner_id, 403);
        $document->delete();
        return back()->with('success', 'Document removed.');
    }
}
