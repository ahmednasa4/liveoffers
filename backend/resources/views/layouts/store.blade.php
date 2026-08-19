@extends('layouts.app')

@section('content')
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:block">
        <div class="p-6">
            <a href="{{ route('store.dashboard') }}" class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold">لوحة المتجر</h1>
                    <p class="text-xs text-slate-400">منصة العروض المحلية</p>
                </div>
            </a>
        </div>
        <nav class="px-4 space-y-1">
            <a href="{{ route('store.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('store.dashboard') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                <span>لوحة المعلومات</span>
            </a>
            <a href="{{ route('store.profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('store.profile.*') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                <span>بيانات المتجر</span>
            </a>
            <a href="{{ route('store.offers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('store.offers.*') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
                <span>العروض</span>
            </a>
            <a href="{{ route('store.live-streams.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('store.live-streams.*') ? 'bg-primary-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span>البث المباشر</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navbar -->
        <header class="bg-white shadow-sm border-b border-slate-200">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-btn" class="md:hidden text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="text-xl font-bold text-slate-800">@yield('title', 'لوحة التحكم')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('store.live-streams.broadcast') }}" class="hidden sm:flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>بدء بث مباشر</span>
                    </a>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->username }}</p>
                        <p class="text-xs text-slate-500">صاحب متجر</p>
                    </div>
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                        <span class="text-primary-700 font-bold">{{ strtoupper(auth()->user()->username[0]) }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:text-rose-600 transition" title="تسجيل الخروج">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content-body')
        </main>
    </div>
</div>

<!-- Mobile FAB for Live Stream -->
<a href="{{ route('store.live-streams.broadcast') }}" class="sm:hidden fixed bottom-6 left-6 z-50 w-14 h-14 bg-rose-600 hover:bg-rose-700 text-white rounded-full shadow-lg flex items-center justify-center transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
</a>

<!-- Mobile Sidebar Overlay -->
<div id="mobile-sidebar" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden">
    <div class="absolute top-0 right-0 w-64 h-full bg-slate-900 text-white p-6">
        <nav class="space-y-1">
            <a href="{{ route('store.dashboard') }}" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">لوحة المعلومات</a>
            <a href="{{ route('store.profile.edit') }}" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">بيانات المتجر</a>
            <a href="{{ route('store.offers.index') }}" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">العروض</a>
            <a href="{{ route('store.live-streams.index') }}" class="block px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800">البث المباشر</a>
        </nav>
    </div>
</div>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-sidebar').classList.toggle('hidden');
    });
</script>
@endsection