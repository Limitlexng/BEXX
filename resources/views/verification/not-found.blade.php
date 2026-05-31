<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cartlex – Verification Failed</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45] flex items-center justify-center p-4">
  <div class="w-full max-w-sm text-center">
    <div class="bg-white rounded-2xl p-8 shadow-2xl">
      <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </div>
      <h2 class="text-xl font-bold text-slate-900 mb-2">Rider Not Found</h2>
      <p class="text-slate-500 text-sm mb-2">No rider found for card number:</p>
      <code class="text-xs bg-slate-100 px-3 py-1.5 rounded-lg font-mono">{{ $cardNumber }}</code>
      <p class="text-slate-400 text-xs mt-4">This card may be invalid, expired, or not registered in the Cartlex system.</p>
    </div>
    <p class="text-slate-500 text-xs mt-4">© {{ date('Y') }} Cartlex</p>
  </div>
</body>
</html>
