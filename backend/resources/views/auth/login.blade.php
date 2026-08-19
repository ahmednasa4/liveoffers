@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-primary-900 p-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 rounded-2xl mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">منصة العروض المحلية</h1>
            <p class="text-slate-400 mt-2">لوحة تحكم الإدارة وأصحاب المتاجر</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">تسجيل الدخول</h2>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg mb-4 text-sm">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-2">اسم المستخدم</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition"
                        placeholder="أدخل اسم المستخدم">
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">كلمة المرور</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition"
                        placeholder="أدخل كلمة المرور">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span>تذكرني</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-sm text-primary-600 hover:text-primary-700 font-semibold">إنشاء حساب جديد</a>
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-md">دخول</button>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-slate-50 rounded-lg border border-slate-200">
                <p class="text-xs font-semibold text-slate-500 mb-2">بيانات تجريبية:</p>
                <div class="grid grid-cols-2 gap-2 text-xs text-slate-600">
                    <div><p class="font-semibold">مشرف:</p><p>admin / admin123</p></div>
                    <div><p class="font-semibold">صاحب متجر:</p><p>storeowner / store123</p></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection