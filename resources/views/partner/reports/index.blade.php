@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('page-subtitle', 'Generate and download fleet performance reports')

@section('content')
<div class="py-4">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach([
      ['Fleet Performance Report', 'A complete overview of all motorcycles, their status, health, and earnings.', route('partner.reports.fleet'), 'blue', 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z'],
      ['Earnings Report', 'Detailed earnings breakdown by motorcycle, rider, and time period.', route('partner.reports.earnings'), 'emerald', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ] as [$title,$desc,$href,$color,$icon])
    <a href="{{ $href }}" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-{{ $color }}-200 transition-all group">
      <div class="w-11 h-11 bg-{{ $color }}-50 group-hover:bg-{{ $color }}-100 rounded-xl flex items-center justify-center mb-4 transition-colors">
        <svg class="w-5 h-5 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
      </div>
      <h3 class="text-sm font-bold text-slate-900 mb-2">{{ $title }}</h3>
      <p class="text-xs text-slate-500">{{ $desc }}</p>
      <div class="mt-4 text-xs font-medium text-{{ $color }}-600 group-hover:underline">Generate Report →</div>
    </a>
    @endforeach
  </div>
</div>
@endsection
