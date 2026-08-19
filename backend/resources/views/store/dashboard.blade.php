@extends('layouts.store')

@section('title', 'لوحة المعلومات')

@section('content-body')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">مرحباً، {{ auth()->user()->username }}!</h1>
    <p class="text-sm text-slate-500 mt-1">نظرة عامة على أداء متجرك</p>
</div>

<!-- Store Status Banner -->
@if(!$store->is_active)
<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-center gap-3">
    <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div>
        <p class="text-sm font-semibold text-amber-800">متجرك قيد المراجعة</p>
        <p class="text-xs text-amber-600">سيتم تفعيل المتجر بعد موافقة المشرف. يمكنك إضافة العروض وإعداد البث المباشر.</p>
    </div>
</div>
@endif

<!-- Metrics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <span class="text-2xl font-bold text-slate-800">{{ $metrics['total_offers'] }}</span>
        </div>
        <p class="text-sm text-slate-500">إجمالي العروض</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-2xl font-bold text-slate-800">{{ $metrics['active_offers'] }}</span>
        </div>
        <p class="text-sm text-slate-500">العروض النشطة</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <span class="text-2xl font-bold text-slate-800">{{ $metrics['total_views'] }}</span>
        </div>
        <p class="text-sm text-slate-500">إجمالي المشاهدات</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-2xl font-bold text-slate-800">{{ $metrics['total_streams'] }}</span>
        </div>
        <p class="text-sm text-slate-500">إجمالي البث المباشر</p>
    </div>
</div>

<!-- Active Stream Alert -->
@if($activeStream)
<div class="bg-rose-50 border border-rose-200 rounded-xl p-5 mb-8 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="w-3 h-3 bg-rose-600 rounded-full animate-pulse"></span>
        <div>
            <p class="font-bold text-rose-800">بث مباشر نشط الآن!</p>
            <p class="text-sm text-rose-600">القناة: {{ $activeStream->channel_name }}</p>
        </div>
    </div>
    <a href="{{ route('store.live-streams.broadcast') }}" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">إدارة البث</a>
</div>
@endif

<!-- Recent Offers -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h2 class="font-bold text-slate-700">أحدث العروض</h2>
        <a href="{{ route('store.offers.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-semibold">عرض الكل</a>
    </div>
    <div class="divide-y divide-slate-100">
        @forelse($recentOffers as $offer)
        <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition">
            <div class="flex items-center gap-4">
                <img src="{{ $offer->image ? Storage::url($offer->image) : 'https://placehold.co/50x50?text=...' }}" class="w-12 h-12 rounded-lg object-cover" alt="{{ $offer->title }}">
                <div>
                    <p class="font-semibold text-slate-800">{{ $offer->title }}</p>
                    <p class="text-xs text-slate-500">{{ $offer->category->name ?? '—' }} • {{ $offer->view_count }} مشاهدة</p>
                </div>
            </div>
            <div class="text-left">
                <p class="font-bold text-orange-600">{{ number_format($offer->offer_price, 2) }} د.أ</p>
                <p class="text-xs text-slate-400 line-through">{{ number_format($offer->original_price, 2) }} د.أ</p>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center text-slate-400">
            <p class="mb-3">لا توجد عروض بعد</p>
            <a href="{{ route('store.offers.create') }}" class="text-orange-600 hover:text-orange-700 font-semibold text-sm">إنشاء أول عرض</a>
        </div>
        @endforelse
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <a href="{{ route('store.offers.create') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:border-orange-300 hover:shadow-md transition group">
        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-orange-600 transition">
            <svg class="w-6 h-6 text-orange-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">إنشاء عرض جديد</h3>
        <p class="text-sm text-slate-500">أضف عرضاً جديداً مع مولد الوصف بالذكاء الاصطناعي</p>
    </a>
    <a href="{{ route('store.profile.edit') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:border-orange-300 hover:shadow-md transition group">
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-600 transition">
            <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">تعديل بيانات المتجر</h3>
        <p class="text-sm text-slate-500">تحديث معلومات المتجر والشعار والعنوان</p>
    </a>
    <a href="{{ route('store.live-streams.broadcast') }}" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:border-rose-300 hover:shadow-md transition group">
        <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-rose-600 transition">
            <svg class="w-6 h-6 text-rose-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        </div>
        <h3 class="font-bold text-slate-800 mb-1">بدء بث مباشر</h3>
        <p class="text-sm text-slate-500">ابدأ بثاً مباشراً لمنتجاتك عبر Agora</p>
    </a>
</div>
@endsection