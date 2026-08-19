@extends('layouts.admin')

@section('title', 'لوحة المعلومات')

@section('content-body')
<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Stores -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m4-4h1m-1 4h1"/></svg>
            </div>
            <span class="text-2xl font-bold text-slate-800">{{ $metrics['stores'] }}</span>
        </div>
        <p class="text-sm font-semibold text-slate-600">إجمالي المتاجر</p>
        <p class="text-xs text-slate-400 mt-1">{{ $metrics['active_stores'] }} نشط · {{ $metrics['pending_stores'] }} بانتظار الاعتماد</p>
    </div>

    <!-- Total Offers -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <span class="text-2xl font-bold text-slate-800">{{ $metrics['offers'] }}</span>
        </div>
        <p class="text-sm font-semibold text-slate-600">إجمالي العروض</p>
        <p class="text-xs text-slate-400 mt-1">{{ $metrics['active_offers'] }} عرض نشط</p>
    </div>

    <!-- Live Streams -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-2xl font-bold text-slate-800">{{ $metrics['live_streams'] }}</span>
        </div>
        <p class="text-sm font-semibold text-slate-600">البث المباشر النشط</p>
        <p class="text-xs text-slate-400 mt-1">@if($metrics['live_streams'] > 0) <span class="text-rose-600 font-semibold">● مباشر الآن</span> @else لا يوجد بث نشط @endif</p>
    </div>

    <!-- Total Users -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <span class="text-2xl font-bold text-slate-800">{{ $metrics['users'] }}</span>
        </div>
        <p class="text-sm font-semibold text-slate-600">إجمالي المستخدمين</p>
        <p class="text-xs text-slate-400 mt-1">{{ $metrics['categories'] }} قسم رئيسي</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Stores -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">أحدث المتاجر المسجلة</h3>
            <a href="{{ route('admin.stores.index') }}" class="text-sm text-primary-600 hover:text-primary-700">عرض الكل</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentStores as $store)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                            <span class="text-slate-600 font-bold">{{ mb_substr($store->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $store->name }}</p>
                            <p class="text-xs text-slate-500">{{ $store->owner->username ?? '—' }}</p>
                        </div>
                    </div>
                    @if($store->is_active)
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">نشط</span>
                    @else
                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">بانتظار الاعتماد</span>
                    @endif
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-400 text-sm">لا توجد متاجر مسجلة</div>
            @endforelse
        </div>
    </div>

    <!-- Active Live Streams -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">البث المباشر النشط</h3>
            <a href="{{ route('admin.live-streams.index') }}" class="text-sm text-primary-600 hover:text-primary-700">عرض الكل</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($activeStreams as $stream)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $stream->store->name ?? 'غير معروف' }}</p>
                            <p class="text-xs text-slate-500">بدأ في {{ $stream->started_at->format('H:i') }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs font-semibold rounded-full flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-rose-600 rounded-full animate-pulse"></span> مباشر
                    </span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-400 text-sm">لا يوجد بث مباشر نشط حالياً</div>
            @endforelse
        </div>
    </div>
</div>
@endsection