@extends('layouts.store')

@section('title', 'بيانات المتجر')

@section('content-body')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">{{ $store ? 'تعديل بيانات المتجر' : 'إنشاء متجر جديد' }}</h1>
    <p class="text-sm text-slate-500 mt-1">أدخل معلومات متجرك بدقة</p>
</div>

<form action="{{ route('store.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
    @csrf
    @method('PUT')

    <!-- Logo Upload -->
    <div class="flex items-center gap-6">
        <div class="w-24 h-24 rounded-xl bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden">
            @if($store && $store->logo)
                <img src="{{ Storage::url($store->logo) }}" class="w-full h-full object-cover" alt="شعار المتجر">
            @else
                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            @endif
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">شعار المتجر</label>
            <input type="file" name="logo" accept="image/*" class="text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
            <p class="text-xs text-slate-400 mt-1">PNG, JPG حتى 2MB</p>
        </div>
    </div>

    <!-- Name & Phone -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">اسم المتجر <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $store->name ?? '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">رقم الهاتف <span class="text-rose-500">*</span></label>
            <input type="text" name="phone" value="{{ old('phone', $store->phone ?? '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required>
        </div>
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">وصف المتجر</label>
        <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">{{ old('description', $store->description ?? '') }}</textarea>
    </div>

    <!-- Address -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">العنوان <span class="text-rose-500">*</span></label>
        <input type="text" name="address" value="{{ old('address', $store->address ?? '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required>
    </div>

    <!-- Coordinates -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">خط العرض (Latitude)</label>
            <input type="text" name="latitude" value="{{ old('latitude', $store->latitude ?? '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">خط الطول (Longitude)</label>
            <input type="text" name="longitude" value="{{ old('longitude', $store->longitude ?? '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
        </div>
    </div>

    <!-- WhatsApp -->
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">رقم واتساب</label>
        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $store->whatsapp_number ?? '') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
    </div>

    @if($store && !$store->is_active)
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-700">
        متجرك قيد المراجعة من قبل المشرف. سيتم تفعيله بعد الموافقة.
    </div>
    @endif

    <div class="flex items-center gap-3">
        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">
            {{ $store ? 'حفظ التغييرات' : 'إنشاء المتجر' }}
        </button>
        <a href="{{ route('store.dashboard') }}" class="text-slate-600 hover:text-slate-800 px-4 py-2.5 font-semibold">إلغاء</a>
    </div>
</form>
@endsection