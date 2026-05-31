@extends('layouts.app')
@section('title', 'Register Motorcycle')
@section('page-title', 'Register Motorcycle')
@section('page-subtitle', 'Add a new motorcycle to your fleet')

@section('content')
<div class="py-4 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <form method="POST" action="{{ route('partner.fleet.store') }}" class="space-y-6">
            @csrf

            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Vehicle Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Brand <span class="text-red-500">*</span></label>
                        <input type="text" name="brand" value="{{ old('brand') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
                               placeholder="e.g. Honda">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Model <span class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
                               placeholder="e.g. CB300R">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Year <span class="text-red-500">*</span></label>
                        <input type="number" name="year" value="{{ old('year', date('Y')) }}" required min="2010" max="{{ date('Y') + 1 }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Color</label>
                        <input type="text" name="color" value="{{ old('color') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
                               placeholder="e.g. Red">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Plate Number</label>
                        <input type="text" name="plate_number" value="{{ old('plate_number') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
                               placeholder="e.g. LG-001-ABC">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">VIN Number</label>
                        <input type="text" name="vin_number" value="{{ old('vin_number') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
                               placeholder="Vehicle ID Number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Engine Number</label>
                        <input type="text" name="engine_number" value="{{ old('engine_number') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Vehicle Number</label>
                        <input type="text" name="vehicle_number" value="{{ old('vehicle_number') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Purchase Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Purchase Cost (₦)</label>
                        <input type="number" name="purchase_cost" value="{{ old('purchase_cost') }}" min="0" step="0.01"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Insurance & Compliance</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Insurance Provider</label>
                        <input type="text" name="insurance_provider" value="{{ old('insurance_provider') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Policy Number</label>
                        <input type="text" name="insurance_policy_number" value="{{ old('insurance_policy_number') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Insurance Expiry</label>
                        <input type="date" name="insurance_expiry" value="{{ old('insurance_expiry') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Road Worthiness Expiry</label>
                        <input type="date" name="road_worthiness_expiry" value="{{ old('road_worthiness_expiry') }}"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]"
                          placeholder="Any additional notes...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Register Motorcycle
                </button>
                <a href="{{ route('partner.fleet.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
