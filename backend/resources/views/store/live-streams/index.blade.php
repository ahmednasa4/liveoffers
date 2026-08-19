@extends('layouts.store')

@section('title', 'البث المباشر')

@section('content-body')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">البث المباشر</h1>
        <p class="text-sm text-slate-500 mt-1">إدارة ومراقبة بثك المباشر</p>
    </div>
    <a href="{{ route('store.live-streams.broadcast') }}" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-lg font-semibold transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        بدء بث جديد
    </a>
</div>

{{-- Active Stream Banner --}}
@if($activeStream)
<div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
        </span>
        <div>
            <p class="font-bold text-emerald-800">بث مباشر نشط الآن</p>
            <p class="text-sm text-emerald-600">بدأ في {{ \Carbon\Carbon::parse($activeStream->started_at)->diffForHumans() }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('store.live-streams.broadcast') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">العودة للبث</a>
        <form action="{{ route('store.live-streams.end', $activeStream->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إنهاء البث؟')">
            @csrf
            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">إنهاء البث</button>
        </form>
    </div>
</div>
@endif

{{-- Streams History --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">اسم القناة</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">المشاهدون</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">البداية</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">النهاية</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($streams as $stream)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4 text-sm text-slate-700">{{ $stream->id }}</td>
                    <td class="px-6 py-4 text-sm text-slate-700 font-mono">{{ $stream->channel_name }}</td>
                    <td class="px-6 py-4">
                        @if($stream->is_active)
                        <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-semibold">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                            نشط
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full text-xs font-semibold">
                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                            منتهي
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-700">{{ $stream->max_viewers }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ \Carbon\Carbon::parse($stream->started_at)->format('Y/m/d H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $stream->ended_at ? \Carbon\Carbon::parse($stream->ended_at)->format('Y/m/d H:i') : '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        لا توجد سجلات بث بعد
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $streams->links() }}
</div>
@endsection