@extends('layouts.guest')
@section('title', 'Create Account')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-900">Join as Fleet Partner</h2>
    <p class="text-slate-500 text-sm mt-1">Create your Cartlex Fleet Partner account</p>
</div>

<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required autofocus
               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A] transition-all text-sm @error('name') border-red-400 @enderror"
               placeholder="John Doe">
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A] transition-all text-sm @error('email') border-red-400 @enderror"
               placeholder="you@company.com">
        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
        <input type="password" name="password" required
               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A] transition-all text-sm @error('password') border-red-400 @enderror"
               placeholder="Minimum 8 characters">
        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
        <input type="password" name="password_confirmation" required
               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#C41E3A]/30 focus:border-[#C41E3A] transition-all text-sm"
               placeholder="Repeat password">
    </div>

    <button type="submit"
            class="w-full py-3 px-4 bg-[#C41E3A] hover:bg-[#A01830] text-white font-semibold rounded-xl transition-colors shadow-sm text-sm">
        Create Partner Account
    </button>
</form>

<div class="mt-6 text-center">
    <p class="text-slate-500 text-sm">
        Already have an account?
        <a href="{{ route('login') }}" class="text-[#C41E3A] font-semibold hover:underline">Sign in</a>
    </p>
</div>
@endsection
