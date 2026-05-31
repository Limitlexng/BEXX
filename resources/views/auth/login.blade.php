@extends('layouts.guest')
@section('title', 'Sign In')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900">Welcome back</h2>
    <p class="text-slate-500 text-sm mt-1">Sign in to your Fleet Partner account</p>
</div>

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A] transition-all text-sm @error('email') border-red-400 @enderror"
               placeholder="you@company.com">
        @error('email')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
        <input type="password" name="password" required
               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A] transition-all text-sm"
               placeholder="Your password">
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 text-[#C41E3A] border-slate-300 rounded">
            <span class="text-sm text-slate-600">Remember me</span>
        </label>
    </div>

    <button type="submit"
            class="w-full py-3 px-4 bg-[#C41E3A] hover:bg-[#A01830] text-white font-semibold rounded-xl transition-colors shadow-sm text-sm">
        Sign In to Portal
    </button>
</form>

<div class="mt-6 text-center">
    <p class="text-slate-500 text-sm">
        New fleet partner?
        <a href="{{ route('register') }}" class="text-[#C41E3A] font-semibold hover:underline">Create account</a>
    </p>
</div>

<div class="mt-4 p-4 bg-slate-50 rounded-xl">
    <p class="text-xs text-slate-500 font-medium mb-2">Demo credentials:</p>
    <p class="text-xs text-slate-600">Partner: <strong>demo@partner.com</strong> / <strong>Demo@2025!</strong></p>
    <p class="text-xs text-slate-600">Admin: <strong>admin@gocartlex.com</strong> / <strong>Cartlex@2025!</strong></p>
</div>
@endsection
