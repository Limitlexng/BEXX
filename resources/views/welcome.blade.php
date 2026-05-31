<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cartlex Fleet Partner Portal</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-[#0D1B2A] to-[#1A2F45]">

  {{-- Nav --}}
  <nav class="flex items-center justify-between px-8 py-5 max-w-7xl mx-auto">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 bg-[#C41E3A] rounded-xl flex items-center justify-center">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
      </div>
      <div>
        <span class="text-white font-bold text-xl">Cartlex</span>
        <span class="text-slate-400 text-sm ml-1.5">Fleet Partner Portal</span>
      </div>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('login') }}" class="text-slate-300 hover:text-white text-sm font-medium transition-colors">Sign In</a>
      <a href="{{ route('register') }}" class="px-5 py-2 bg-[#C41E3A] hover:bg-[#A01830] text-white text-sm font-semibold rounded-xl transition-colors">Get Started</a>
    </div>
  </nav>

  {{-- Hero --}}
  <section class="max-w-7xl mx-auto px-8 pt-20 pb-28 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 text-slate-300 text-xs font-medium rounded-full mb-8 border border-white/10">
      <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
      Fleet Partner Program — Now Open
    </div>
    <h1 class="text-4xl sm:text-6xl font-extrabold text-white leading-tight mb-6">
      Your Fleet.<br>
      <span class="text-[#C41E3A]">Your Returns.</span><br>
      Full Visibility.
    </h1>
    <p class="text-slate-300 text-lg max-w-2xl mx-auto mb-10">
      The official investment & fleet management portal for Cartlex Delivery Partners.
      Monitor motorcycles, riders, earnings, and compliance from one powerful dashboard.
    </p>
    <div class="flex items-center justify-center gap-4 flex-wrap">
      <a href="{{ route('register') }}" class="px-8 py-3.5 bg-[#C41E3A] hover:bg-[#A01830] text-white font-bold rounded-xl transition-colors text-sm shadow-lg shadow-[#C41E3A]/30">
        Start as Fleet Partner
      </a>
      <a href="{{ route('login') }}" class="px-8 py-3.5 bg-white/10 hover:bg-white/15 text-white font-semibold rounded-xl transition-colors text-sm border border-white/10">
        Sign In to Portal
      </a>
    </div>
  </section>

  {{-- Feature Grid --}}
  <section class="max-w-7xl mx-auto px-8 pb-24">
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach([
        ['Fleet Registry', 'Register and track every motorcycle with full asset details, insurance, and health scoring.', 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z', 'blue'],
        ['Rider Management', 'Create riders, generate digital ID cards with QR verification, and track performance.', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'emerald'],
        ['Earnings Dashboard', 'Track daily, weekly, monthly, and lifetime earnings per motorcycle and per rider.', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'amber'],
        ['Compliance Monitoring', 'Track licenses, insurance, road worthiness, and get alerts before anything expires.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'purple'],
        ['Wallet & Payouts', 'Real-time wallet balance, withdrawal requests, and payout history in one place.', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'indigo'],
        ['Investment Analytics', 'ROI tracking, payback period, asset rankings, and revenue forecasting.', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'rose'],
      ] as [$title, $desc, $icon, $color])
      <div class="bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl p-6 transition-colors">
        <div class="w-10 h-10 bg-{{ $color }}-500/20 rounded-xl flex items-center justify-center mb-4">
          <svg class="w-5 h-5 text-{{ $color }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
          </svg>
        </div>
        <h3 class="text-white font-semibold mb-2">{{ $title }}</h3>
        <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
      </div>
      @endforeach
    </div>
  </section>

  {{-- Footer --}}
  <footer class="border-t border-white/10 px-8 py-6 max-w-7xl mx-auto">
    <div class="flex items-center justify-between text-sm text-slate-500">
      <div>© {{ date('Y') }} Cartlex Fleet Partner Portal · Powered by <strong class="text-slate-400">Limitlex Technologies</strong></div>
      <div class="flex items-center gap-4">
        <a href="tel:07052004934" class="hover:text-slate-300 transition-colors">07052004934</a>
        <a href="mailto:help@gocartlex.com" class="hover:text-slate-300 transition-colors">help@gocartlex.com</a>
      </div>
    </div>
  </footer>

</body>
</html>
