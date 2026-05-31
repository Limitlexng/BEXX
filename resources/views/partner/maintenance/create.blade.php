@extends('layouts.app')
@section('title', 'Log Maintenance')
@section('page-title', 'Log Service / Maintenance')
@section('page-subtitle', 'Record a service or repair for a motorcycle')

@section('content')
<div class="py-4 max-w-3xl">
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
    <form method="POST" action="{{ route('partner.maintenance.store') }}" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Motorcycle <span class="text-red-500">*</span></label>
          <select name="motorcycle_id" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
            <option value="">-- Select Motorcycle --</option>
            @foreach($motorcycles as $m)
            <option value="{{ $m->id }}" {{ old('motorcycle_id') == $m->id ? 'selected' : '' }}>{{ $m->fleet_id }} – {{ $m->brand }} {{ $m->model }} {{ $m->year }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Service Type <span class="text-red-500">*</span></label>
          <select name="type" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
            @foreach(['routine_service'=>'Routine Service','repair'=>'Repair','inspection'=>'Inspection','emergency'=>'Emergency','upgrade'=>'Upgrade'] as $v=>$l)
            <option value="{{ $v }}" {{ old('type') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Status <span class="text-red-500">*</span></label>
          <select name="status" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
            @foreach(['completed'=>'Completed','in_progress'=>'In Progress','scheduled'=>'Scheduled','cancelled'=>'Cancelled'] as $v=>$l)
            <option value="{{ $v }}" {{ old('status','completed') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Description <span class="text-red-500">*</span></label>
          <input type="text" name="description" value="{{ old('description') }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]" placeholder="Brief description of the service">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Workshop Name</label>
          <input type="text" name="workshop_name" value="{{ old('workshop_name') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Technician Name</label>
          <input type="text" name="technician_name" value="{{ old('technician_name') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Cost (₦) <span class="text-red-500">*</span></label>
          <input type="number" name="cost" value="{{ old('cost', 0) }}" required min="0" step="0.01" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Downtime (hours)</label>
          <input type="number" name="downtime_hours" value="{{ old('downtime_hours', 0) }}" min="0" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Service Date <span class="text-red-500">*</span></label>
          <input type="date" name="service_date" value="{{ old('service_date', today()->toDateString()) }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Next Service Due</label>
          <input type="date" name="next_service_due" value="{{ old('next_service_due') }}" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Details / Notes</label>
          <textarea name="details" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]" placeholder="Detailed description of work done...">{{ old('details') }}</textarea>
        </div>
      </div>
      <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="btn-primary">Save Maintenance Log</button>
        <a href="{{ route('partner.maintenance.index') }}" class="btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
