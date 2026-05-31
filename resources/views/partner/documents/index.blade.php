@extends('layouts.app')
@section('title', 'Documents')
@section('page-title', 'Document Management')
@section('page-subtitle', 'Store and manage all fleet-related documents')

@section('content')
<div class="py-4 space-y-6">

  {{-- Upload Form --}}
  <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
    <h3 class="text-sm font-bold text-slate-900 mb-5">Upload Document</h3>
    <form method="POST" action="{{ route('partner.documents.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Document Type</label>
        <select name="type" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          @foreach(['cac_certificate'=>'CAC Certificate','insurance'=>'Insurance','road_worthiness'=>'Road Worthiness','vehicle_papers'=>'Vehicle Papers','purchase_receipt'=>'Purchase Receipt','agreement'=>'Agreement','rider_id'=>'Rider ID','verification_doc'=>'Verification Doc','other'=>'Other'] as $v=>$l)
          <option value="{{ $v }}">{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">File <span class="text-red-500">*</span></label>
        <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Issue Date</label>
        <input type="date" name="issue_date" value="{{ old('issue_date') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Expiry Date</label>
        <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
      </div>
      <div class="flex items-end">
        <button type="submit" class="btn-primary w-full justify-center">Upload Document</button>
      </div>
    </form>
  </div>

  {{-- Documents Grid --}}
  @if($documents->isEmpty())
  <div class="bg-white rounded-2xl p-12 shadow-sm border border-slate-100 text-center">
    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
      <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
    </div>
    <p class="text-slate-500 text-sm">No documents uploaded yet</p>
  </div>
  @else
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($documents as $doc)
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-start gap-4">
      <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
      </div>
      <div class="flex-1 min-w-0">
        <div class="text-sm font-semibold text-slate-800 truncate">{{ $doc->title }}</div>
        <div class="text-xs text-slate-500 capitalize mt-0.5">{{ str_replace('_',' ',$doc->type) }}</div>
        @if($doc->expiry_date)
        <div class="text-xs {{ $doc->expiry_date->isPast() ? 'text-red-600' : 'text-slate-400' }} mt-0.5">
          Expires {{ $doc->expiry_date->format('d M Y') }}
        </div>
        @endif
        <div class="flex items-center gap-2 mt-2">
          <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline">View</a>
          <form method="POST" action="{{ route('partner.documents.destroy', $doc) }}" onsubmit="return confirm('Delete this document?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
          </form>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  <div class="mt-4">{{ $documents->links() }}</div>
  @endif
</div>
@endsection
