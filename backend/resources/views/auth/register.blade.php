@extends('layouts.app')

@section('title', 'إنشاء حساب جديد')

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
            <p class="text-slate-400 mt-2">انضم إلينا كصاحب متجر</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">إنشاء حساب جديد</h2>

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg mb-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-2">اسم المستخدم</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition"
                        placeholder="اختر اسم مستخدم">
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition"
                        placeholder="example@email.com">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">رقم الهاتف</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition"
                        placeholder="07XXXXXXXX">
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">كلمة المرور</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition"
                        placeholder="6 أحرف على الأقل">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">تأكيد كلمة المرور</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none transition"
                        placeholder="أعد إدخال كلمة المرور">
                </div>
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition shadow-md">إنشاء الحساب</button>
            </form>

            <p class="text-center text-sm text-slate-600 mt-6">
                لديك حساب بالفعل؟
                <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-semibold">تسجيل الدخول</a>
            </p>
        </div>
    </div>
</div>
@endsection