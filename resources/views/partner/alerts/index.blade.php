@extends('layouts.app')
@section('title', 'Alerts')
@section('page-title', 'Alerts & Notifications')
@section('page-subtitle', 'Stay informed about your fleet and compliance status')

@section('content')
<div class="py-4 space-y-4">
  @forelse($alerts as $alert)
  <div class="bg-white rounded-2xl p-5 shadow-sm border {{ $alert->read ? 'border-slate-100' : 'border-amber-200' }} flex items-start gap-4">
    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
      {{ ['danger'=>'bg-red-100','warning'=>'bg-amber-100','success'=>'bg-emerald-100','info'=>'bg-blue-100'][$alert->severity] ?? 'bg-slate-100' }}">
      @if($alert->severity === 'danger')
      <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      @elseif($alert->severity === 'warning')
      <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      @elseif($alert->severity === 'success')
      <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      @else
      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      @endif
    </div>
    <div class="flex-1">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-sm font-semibold text-slate-900">{{ $alert->title }}</div>
          <div class="text-sm text-slate-600 mt-1">{{ $alert->message }}</div>
          <div class="text-xs text-slate-400 mt-2">{{ $alert->created_at->diffForHumans() }}</div>
        </div>
        @if(!$alert->read)
        <form method="POST" action="{{ route('partner.alerts.read', $alert) }}">
          @csrf
          <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 whitespace-nowrap">Mark read</button>
        </form>
        @else
        <span class="text-xs text-slate-300">Read</span>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="bg-white rounded-2xl p-16 shadow-sm border border-slate-100 text-center">
    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
      <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    </div>
    <p class="text-slate-500 text-sm">No alerts at this time</p>
  </div>
  @endforelse
  <div>{{ $alerts->links() }}</div>
</div>
@endsection
