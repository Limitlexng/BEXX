@extends('layouts.app')
@section('title', 'Edit Motorcycle')
@section('page-title', 'Edit ' . $motorcycle->fleet_id)
@section('page-subtitle', $motorcycle->full_name)

@section('content')
<div class="py-4 max-w-3xl">
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
    <form method="POST" action="{{ route('partner.fleet.update', $motorcycle) }}" class="space-y-6">
      @csrf @method('PUT')

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach([
          ['brand','Brand','text',true,null],
          ['model','Model','text',true,null],
          ['year','Year','number',true,'min=2010 max='.(date('Y')+1)],
          ['color','Color','text',false,null],
          ['plate_number','Plate Number','text',false,null],
          ['vin_number','VIN Number','text',false,null],
          ['engine_number','Engine Number','text',false,null],
          ['vehicle_number','Vehicle Number','text',false,null],
          ['purchase_date','Purchase Date','date',false,null],
          ['purchase_cost','Purchase Cost (₦)','number',false,'min=0 step=0.01'],
          ['insurance_provider','Insurance Provider','text',false,null],
          ['insurance_policy_number','Policy Number','text',false,null],
          ['insurance_expiry','Insurance Expiry','date',false,null],
          ['road_worthiness_expiry','Road Worthiness Expiry','date',false,null],
        ] as [$name,$label,$type,$req,$extra])
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $label }}@if($req) <span class="text-red-500">*</span>@endif</label>
          <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $motorcycle->$name) }}" {{ $req ? 'required' : '' }} {{ $extra }}
                 class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
        </div>
        @endforeach

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Insurance Status</label>
          <select name="insurance_status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
            @foreach(['active'=>'Active','expired'=>'Expired','pending'=>'Pending'] as $v=>$l)
            <option value="{{ $v }}" {{ old('insurance_status',$motorcycle->insurance_status) === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
          <select name="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">
            @foreach(['active'=>'Active','maintenance'=>'Maintenance','suspended'=>'Suspended','retired'=>'Retired','lost'=>'Lost'] as $v=>$l)
            <option value="{{ $v }}" {{ old('status',$motorcycle->status) === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
        <textarea name="notes" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A]">{{ old('notes',$motorcycle->notes) }}</textarea>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">Save Changes</button>
        <a href="{{ route('partner.fleet.show', $motorcycle) }}" class="btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
