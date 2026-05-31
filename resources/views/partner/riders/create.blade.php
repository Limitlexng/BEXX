@extends('layouts.app')
@section('title', 'Add Rider')
@section('page-title', 'Register Rider')
@section('page-subtitle', 'Add a new rider to your fleet')

@section('content')
<div class="py-4 max-w-3xl">
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
    <form method="POST" action="{{ route('partner.riders.store') }}" class="space-y-6">
      @csrf

      <div>
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Personal Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
            <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]" placeholder="08012345678">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">NIN</label>
            <input type="text" name="nin" value="{{ old('nin') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
            <input type="text" name="address" value="{{ old('address') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">City</label>
            <input type="text" name="city" value="{{ old('city') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">State</label>
            <input type="text" name="state" value="{{ old('state') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
        </div>
      </div>

      <div class="border-t border-slate-100 pt-6">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">License & Compliance</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">License Number</label>
            <input type="text" name="license_number" value="{{ old('license_number') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">License Expiry</label>
            <input type="date" name="license_expiry" value="{{ old('license_expiry') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Emergency Contact Name</label>
            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Emergency Contact Phone</label>
            <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          </div>
        </div>
      </div>

      @if($availableMotorcycles->count())
      <div class="border-t border-slate-100 pt-6">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Assign Motorcycle (Optional)</h3>
        <select name="motorcycle_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
          <option value="">-- No assignment yet --</option>
          @foreach($availableMotorcycles as $moto)
          <option value="{{ $moto->id }}" {{ old('motorcycle_id') == $moto->id ? 'selected' : '' }}>{{ $moto->fleet_id }} – {{ $moto->full_name }}</option>
          @endforeach
        </select>
      </div>
      @endif

      <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="btn-primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Register Rider & Generate ID Card
        </button>
        <a href="{{ route('partner.riders.index') }}" class="btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
