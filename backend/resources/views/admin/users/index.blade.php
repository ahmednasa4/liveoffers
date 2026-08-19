@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')

@section('content-body')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">إدارة المستخدمين</h1>
        <p class="text-sm text-slate-500 mt-1">عرض وإدارة جميع مستخدمي المنصة</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 border border-slate-200 mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد..."
            class="flex-1 px-4 py-2 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
        <select name="role" class="px-4 py-2 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
            <option value="">كل الأدوار</option>
            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>مشرف</option>
            <option value="store_owner" {{ request('role') == 'store_owner' ? 'selected' : '' }}>صاحب متجر</option>
        </select>
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">بحث</button>
        <a href="{{ route('admin.users.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2 rounded-lg text-sm font-semibold transition text-center">إعادة تعيين</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">المستخدم</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الدور</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">البريد</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الهاتف</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">المتجر</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-primary-100 rounded-full flex items-center justify-center">
                                    <span class="text-primary-700 font-bold text-sm">{{ strtoupper(mb_substr($user->username, 0, 1)) }}</span>
                                </div>
                                <span class="font-semibold text-slate-800">{{ $user->username }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">مشرف</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">صاحب متجر</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $user->store->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">نشط</span>
                            @else
                                <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs font-semibold rounded-full">معطل</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->id === auth()->id())
                                <span class="text-xs text-slate-400">حسابك الحالي</span>
                            @else
                                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->is_active)
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 text-sm font-semibold transition" onclick="return confirm('هل أنت متأكد من تعطيل هذا المستخدم؟')">تعطيل</button>
                                    @else
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold transition">تفعيل</button>
                                    @endif
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">لا يوجد مستخدمون مطابقون</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection