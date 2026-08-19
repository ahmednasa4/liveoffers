@extends('layouts.admin')

@section('title', 'إدارة المتاجر')

@section('content-body')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">إدارة المتاجر</h1>
        <p class="text-sm text-slate-500 mt-1">اعتماد وتعطيل المتاجر المحلية</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 border border-slate-200 mb-6">
    <form action="{{ route('admin.stores.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو العنوان..."
            class="flex-1 px-4 py-2 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
        <select name="status" class="px-4 py-2 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
            <option value="">كل الحالات</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار الاعتماد</option>
        </select>
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">بحث</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">المتجر</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">المالك</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">العنوان</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الهاتف</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($stores as $store)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <span class="text-primary-700 font-bold text-sm">{{ mb_substr($store->name, 0, 1) }}</span>
                                </div>
                                <span class="font-semibold text-slate-800">{{ $store->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $store->owner->username ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $store->address }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $store->phone }}</td>
                        <td class="px-6 py-4">
                            @if($store->is_active)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">نشط</span>
                            @else
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">بانتظار الاعتماد</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($store->is_active)
                                <form action="{{ route('admin.stores.suspend', $store->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 text-sm font-semibold transition" onclick="return confirm('هل أنت متأكد من تعطيل هذا المتجر؟')">تعطيل</button>
                                </form>
                            @else
                                <form action="{{ route('admin.stores.approve', $store->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold transition">اعتماد</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">لا توجد متاجر مطابقة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $stores->withQueryString()->links() }}
    </div>
</div>
@endsection