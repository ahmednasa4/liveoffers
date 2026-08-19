@extends('layouts.store')

@section('title', 'العروض')

@section('content-body')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">العروض</h1>
        <p class="text-sm text-slate-500 mt-1">إدارة عروض متجرك</p>
    </div>
    <a href="{{ route('store.offers.create') }}" class="flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-lg font-semibold transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        <span>عرض جديد</span>
    </a>
</div>

<!-- Filters -->
<form action="{{ route('store.offers.index') }}" method="GET" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-3">
    <div class="flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن عرض..." class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
    </div>
    <div>
        <select name="status" class="px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
            <option value="">كل الحالات</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
        </select>
    </div>
    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg font-semibold transition">تصفية</button>
</form>

<!-- Offers Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($offers as $offer)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition">
        <div class="relative">
            <img src="{{ $offer->image ? Storage::url($offer->image) : 'https://placehold.co/400x200?text=...' }}" class="w-full h-40 object-cover" alt="{{ $offer->title }}">
            <span class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-bold {{ $offer->is_active ? 'bg-emerald-500 text-white' : 'bg-slate-400 text-white' }}">
                {{ $offer->is_active ? 'نشط' : 'متوقف' }}
            </span>
            @if($offer->is_featured)
            <span class="absolute top-2 left-2 px-2 py-1 rounded-full text-xs font-bold bg-amber-500 text-white">مميز</span>
            @endif
        </div>
        <div class="p-4">
            <h3 class="font-bold text-slate-800 mb-1 truncate">{{ $offer->title }}</h3>
            <p class="text-xs text-slate-500 mb-3">{{ $offer->category->name ?? '—' }} • {{ $offer->subcategory->name ?? '—' }}</p>
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="font-bold text-orange-600">{{ number_format($offer->offer_price, 2) }} د.أ</p>
                    <p class="text-xs text-slate-400 line-through">{{ number_format($offer->original_price, 2) }} د.أ</p>
                </div>
                <div class="text-left">
                    <p class="text-xs text-slate-500">{{ $offer->view_count }} مشاهدة</p>
                    @if($offer->is_ai_generated)
                    <span class="text-xs text-purple-600 font-semibold">AI</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('store.offers.edit', $offer->id) }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 rounded-lg text-sm font-semibold transition">تعديل</a>
                <form action="{{ route('store.offers.delete', $offer->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العرض؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 px-3 py-2 rounded-lg text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/></svg>
        <p class="text-slate-400 mb-3">لا توجد عروض بعد</p>
        <a href="{{ route('store.offers.create') }}" class="text-orange-600 hover:text-orange-700 font-semibold">إنشاء أول عرض</a>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $offers->links() }}
</div>
@endsection