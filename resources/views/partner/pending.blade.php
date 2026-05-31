@extends('layouts.guest')
@section('title', 'Account Pending')
@section('content')
<div class="text-center">
  <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  </div>
  <h2 class="text-xl font-bold text-slate-900 mb-2">Account Under Review</h2>
  <p class="text-slate-500 text-sm mb-6">Your partner account has been submitted and is being reviewed by the Cartlex team. You'll receive an email once approved — typically within 24 hours.</p>
  <div class="p-4 bg-slate-50 rounded-xl text-left mb-6">
    <div class="text-xs font-medium text-slate-700 mb-2">What happens next:</div>
    <ul class="space-y-2 text-xs text-slate-600">
      <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-[#C41E3A] rounded-full"></span>Cartlex operations team reviews your application</li>
      <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-[#C41E3A] rounded-full"></span>You receive approval confirmation via email</li>
      <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 bg-[#C41E3A] rounded-full"></span>Full portal access is activated</li>
    </ul>
  </div>
  <form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="btn-secondary w-full justify-center">Sign Out</button>
  </form>
</div>
@endsection
