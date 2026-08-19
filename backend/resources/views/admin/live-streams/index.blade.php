@extends('layouts.admin')

@section('title', 'مراقبة البث المباشر')

@section('content-body')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">مراقبة البث المباشر</h1>
    <p class="text-sm text-slate-500 mt-1">عرض وإنهاء البث المباشر على المنصة</p>
</div>

@php $activeCount = $activeStreams->count(); @endphp

<!-- Active Streams Section -->
<div class="mb-8">
    <h2 class="text-lg font-bold text-slate-700 mb-4 flex items-center gap-2">
        <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
        البث المباشر النشط ({{ $activeCount }})
    </h2>

    @if($activeStreams->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
        @foreach($activeStreams as $stream)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-bold text-slate-800">{{ $stream->store->name ?? '—' }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $stream->store->owner->phone ?? '—' }}</p>
                </div>
                <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs font-semibold rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-rose-600 rounded-full animate-pulse"></span> مباشر
                </span>
            </div>
            <div class="space-y-1 text-sm text-slate-600">
                <p>القناة: <span class="font-mono text-xs">{{ $stream->channel_name }}</span></p>
                <p>المشاهدون: {{ $stream->max_viewers }}</p>
                <p>بدأ في: {{ $stream->started_at?->format('Y-m-d H:i') }}</p>
            </div>
            <form action="{{ route('admin.live-streams.end', $stream->id) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition" onclick="return confirm('هل أنت متأكد من إنهاء هذا البث قسرياً؟')">
                    إنهاء البث
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 text-center">
        <p class="text-emerald-700 text-sm font-semibold">لا يوجد بث مباشر نشط حالياً</p>
    </div>
    @endif
</div>

<!-- All Streams History -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h2 class="font-bold text-slate-700">سجل البث المباشر</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">المتجر</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">القناة</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">المشاهدون</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">بدأ في</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">انتهى في</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentStreams as $stream)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $stream->store->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600 font-mono">{{ $stream->channel_name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $stream->max_viewers }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $stream->started_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $stream->ended_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($stream->is_active)
                                <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs font-semibold rounded-full">مباشر</span>
                            @else
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-semibold rounded-full">انتهى</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">لا يوجد بث مباشر</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-200">{{ $recentStreams->withQueryString()->links() }}</div>
</div>
@endsection