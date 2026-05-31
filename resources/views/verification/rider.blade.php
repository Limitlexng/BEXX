<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cartlex Rider Verification</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45] flex items-center justify-center p-4">
  <div class="w-full max-w-sm">
    <div class="text-center mb-6">
      <div class="inline-flex items-center gap-2 mb-2">
        <div class="w-8 h-8 bg-[#C41E3A] rounded-xl flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <span class="text-white font-bold text-lg">Cartlex</span>
      </div>
      <div class="text-slate-400 text-sm">Rider Verification Portal</div>
    </div>

    @if($is_valid)
    <div class="bg-white rounded-2xl p-6 shadow-2xl">
      <div class="flex items-center justify-center gap-2 mb-5">
        <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span class="text-emerald-700 font-bold text-lg">Verified Rider</span>
      </div>

      <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl mb-4">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center text-2xl font-bold text-emerald-700 flex-shrink-0">
          {{ strtoupper(substr($rider->first_name,0,1)) }}
        </div>
        <div>
          <div class="font-bold text-slate-900 text-lg">{{ $rider->full_name }}</div>
          <div class="text-slate-500 text-sm">{{ $rider->rider_id }}</div>
          <div class="text-slate-500 text-sm">{{ $partner->display_name }}</div>
        </div>
      </div>

      <dl class="space-y-2.5">
        @foreach([
          'Status' => ucfirst($rider->status),
          'Card Number' => $card->card_number,
          'Issued' => $card->issue_date->format('d M Y'),
          'Expires' => $card->expiry_date->format('d M Y'),
          'Motorcycle' => $motorcycle?->fleet_id.' – '.($motorcycle?->full_name ?? ''),
        ] as $label => $value)
        @if(trim($value, ' –'))
        <div class="flex justify-between py-1.5 border-b border-slate-50">
          <dt class="text-xs text-slate-500">{{ $label }}</dt>
          <dd class="text-xs font-medium text-slate-800">{{ $value }}</dd>
        </div>
        @endif
        @endforeach
      </dl>

      <div class="mt-4 pt-4 border-t border-slate-100 text-center">
        <div class="text-xs text-slate-400">Verified at {{ \Carbon\Carbon::parse($verified_at)->format('d M Y, H:i:s') }}</div>
      </div>
    </div>
    @else
    <div class="bg-white rounded-2xl p-6 shadow-2xl text-center">
      <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </div>
      <div class="text-red-700 font-bold text-lg mb-1">Card Expired or Revoked</div>
      <div class="text-slate-500 text-sm mb-4">Card: {{ $card->card_number }}</div>
      <div class="text-xs text-slate-400">This ID card is no longer valid. Please request a new card from your fleet manager.</div>
    </div>
    @endif

    <p class="text-center text-slate-500 text-xs mt-4">© {{ date('Y') }} Cartlex · Limitlex Technologies</p>
  </div>
</body>
</html>
