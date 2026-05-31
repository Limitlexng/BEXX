<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Cartlex') }} Fleet Portal - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-400 hover:bg-white/10 hover:text-white transition-all duration-200 text-sm font-medium; }
        .sidebar-link.active { @apply bg-white/15 text-white; }
        .stat-card { @apply bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow; }
        .badge-success { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800; }
        .badge-warning { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800; }
        .badge-danger { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800; }
        .badge-info { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800; }
        .badge-gray { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700; }
        .btn-primary { @apply inline-flex items-center gap-2 px-4 py-2.5 bg-[#C41E3A] text-white text-sm font-semibold rounded-xl hover:bg-[#A01830] transition-colors shadow-sm; }
        .btn-secondary { @apply inline-flex items-center gap-2 px-4 py-2.5 bg-white text-slate-700 text-sm font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm; }
        .table-header { @apply px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider; }
        .table-cell { @apply px-4 py-4 text-sm text-slate-700; }
    </style>
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-full">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-[#0D1B2A] to-[#1A2F45] flex flex-col shadow-2xl transform transition-transform duration-300 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
            <div class="w-9 h-9 bg-[#C41E3A] rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <div class="text-white font-bold text-lg leading-none">Cartlex</div>
                <div class="text-slate-400 text-xs">Fleet Partner Portal</div>
            </div>
        </div>

        <!-- Partner Info -->
        @if(auth()->user()->partner)
        <div class="mx-4 mt-4 p-4 bg-white/5 rounded-xl border border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#C41E3A]/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-[#C41E3A] font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                </div>
                <div class="min-w-0">
                    <div class="text-white text-sm font-medium truncate">{{ auth()->user()->partner->display_name }}</div>
                    <div class="text-slate-400 text-xs">{{ auth()->user()->partner->partner_code }}</div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-white/10">
                <div class="text-slate-400 text-xs">Wallet Balance</div>
                <div class="text-white font-bold text-lg">₦{{ number_format(auth()->user()->partner->wallet_balance, 2) }}</div>
            </div>
        </div>
        @endif

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            @if(auth()->user()->isAdmin())
            <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-4 mb-2">Admin</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.partners.index') }}" class="sidebar-link {{ request()->routeIs('admin.partners.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Partners
            </a>
            <a href="{{ route('admin.withdrawals.index') }}" class="sidebar-link {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Withdrawals
            </a>
            <a href="{{ route('admin.fleet.index') }}" class="sidebar-link {{ request()->routeIs('admin.fleet.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h9a1 1 0 001-1zm0 0l2 2 3-3-2-2"/></svg>
                Fleet Overview
            </a>
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Reports
            </a>

            <div class="border-t border-white/10 my-3"></div>
            @endif

            @if(auth()->user()->partner && auth()->user()->partner->status === 'active')
            <div class="text-slate-500 text-xs font-semibold uppercase tracking-wider px-4 mb-2">Partner Portal</div>

            <a href="{{ route('partner.dashboard') }}" class="sidebar-link {{ request()->routeIs('partner.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>

            <a href="{{ route('partner.fleet.index') }}" class="sidebar-link {{ request()->routeIs('partner.fleet.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h9a1 1 0 001-1zm0 0l2 2 3-3-2-2"/></svg>
                Fleet Management
            </a>

            <a href="{{ route('partner.riders.index') }}" class="sidebar-link {{ request()->routeIs('partner.riders.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Riders
                @if(auth()->user()->partner->riders()->where('status','active')->count() > 0)
                <span class="ml-auto bg-[#C41E3A]/20 text-[#C41E3A] text-xs font-bold px-2 py-0.5 rounded-full">{{ auth()->user()->partner->riders()->where('status','active')->count() }}</span>
                @endif
            </a>

            <a href="{{ route('partner.earnings.index') }}" class="sidebar-link {{ request()->routeIs('partner.earnings.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Earnings
            </a>

            <a href="{{ route('partner.maintenance.index') }}" class="sidebar-link {{ request()->routeIs('partner.maintenance.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Maintenance
            </a>

            <a href="{{ route('partner.compliance.index') }}" class="sidebar-link {{ request()->routeIs('partner.compliance.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Compliance
            </a>

            <a href="{{ route('partner.wallet.index') }}" class="sidebar-link {{ request()->routeIs('partner.wallet.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Wallet
            </a>

            <a href="{{ route('partner.documents.index') }}" class="sidebar-link {{ request()->routeIs('partner.documents.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Documents
            </a>

            <a href="{{ route('partner.reports.index') }}" class="sidebar-link {{ request()->routeIs('partner.reports.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Reports
            </a>

            <a href="{{ route('partner.alerts.index') }}" class="sidebar-link {{ request()->routeIs('partner.alerts.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Alerts
                @php $unreadAlerts = auth()->user()->partner?->alerts()->where('read',false)->count() ?? 0 @endphp
                @if($unreadAlerts > 0)
                <span class="ml-auto bg-[#C41E3A] text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadAlerts }}</span>
                @endif
            </a>
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-2">
                <div class="w-8 h-8 bg-white/10 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-medium text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-slate-400 text-xs truncate">{{ auth()->user()->email }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-white transition-colors" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-cloak></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen lg:pl-72">
        <!-- Top Bar -->
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm">
            <div class="flex items-center gap-4 px-6 py-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex-1">
                    <h1 class="text-xl font-bold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                    <p class="text-sm text-slate-500 mt-0.5">@yield('page-subtitle')</p>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    @if(auth()->user()->partner)
                    <a href="{{ route('partner.alerts.index') }}" class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @php $unread = auth()->user()->partner?->alerts()->where('read',false)->count() ?? 0 @endphp
                        @if($unread > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-[#C41E3A] text-white text-xs rounded-full flex items-center justify-center font-bold">{{ $unread }}</span>
                        @endif
                    </a>
                    @endif

                    @yield('header-actions')
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-4">
            @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm mb-4">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm mb-4">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
            @endif
            @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm mb-4">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 px-6 pb-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="px-6 py-4 border-t border-slate-200 bg-white">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span>© {{ date('Y') }} Cartlex Fleet Partner Portal. Powered by Limitlex Technologies.</span>
                <span>v1.0.0</span>
            </div>
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
