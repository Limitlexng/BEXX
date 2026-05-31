@extends('layouts.guest')
@section('title', 'Partner Onboarding')
@section('content')
<div class="mb-6">
  <h2 class="text-2xl font-bold text-slate-900">Partner Onboarding</h2>
  <p class="text-slate-500 text-sm mt-1">Tell us about your fleet partnership</p>
</div>

<form method="POST" action="{{ route('onboarding.store') }}" class="space-y-5" x-data="{ type: '{{ old('partner_type', 'independent_investor') }}' }">
  @csrf

  <div>
    <label class="block text-sm font-medium text-slate-700 mb-2">Partner Type <span class="text-red-500">*</span></label>
    <div class="grid grid-cols-1 gap-2">
      @foreach([
        'independent_investor' => ['Independent Investor', 'Own 1–10 motorcycles. Earn returns from fleet utilization.'],
        'logistics_company' => ['Logistics Company', 'Own multiple motorcycles. Manage fleets and riders at scale.'],
        'corporate_fleet_partner' => ['Corporate Fleet Partner', 'Large organization with department management and advanced reporting.'],
      ] as $value => [$label, $desc])
      <label class="flex items-start gap-3 p-3 border-2 rounded-xl cursor-pointer transition-colors"
             :class="type === '{{ $value }}' ? 'border-[#C41E3A] bg-[#C41E3A]/5' : 'border-slate-200 hover:border-slate-300'">
        <input type="radio" name="partner_type" value="{{ $value }}" x-model="type" class="mt-1 text-[#C41E3A]">
        <div>
          <div class="text-sm font-semibold text-slate-800">{{ $label }}</div>
          <div class="text-xs text-slate-500 mt-0.5">{{ $desc }}</div>
        </div>
      </label>
      @endforeach
    </div>
  </div>

  <div x-show="type !== 'independent_investor'" x-cloak>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Company Name <span class="text-red-500">*</span></label>
    <input type="text" name="company_name" value="{{ old('company_name') }}"
           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
           placeholder="Your company name">
  </div>

  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1.5">Contact Person <span class="text-red-500">*</span></label>
      <input type="text" name="contact_person" value="{{ old('contact_person', auth()->user()->name) }}" required
             class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
    </div>
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone <span class="text-red-500">*</span></label>
      <input type="text" name="phone" value="{{ old('phone') }}" required
             class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
             placeholder="08012345678">
    </div>
  </div>

  <div class="grid grid-cols-2 gap-3">
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1.5">City</label>
      <input type="text" name="city" value="{{ old('city') }}"
             class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
    </div>
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1.5">State</label>
      <input type="text" name="state" value="{{ old('state') }}"
             class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
    </div>
  </div>

  <div x-show="type !== 'independent_investor'" x-cloak>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">CAC Number</label>
    <input type="text" name="cac_number" value="{{ old('cac_number') }}"
           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
           placeholder="RC1234567">
  </div>

  <button type="submit" class="w-full py-3 bg-[#C41E3A] hover:bg-[#A01830] text-white font-semibold rounded-xl transition-colors text-sm">
    Submit Application
  </button>
</form>
@endsection
