@extends('layouts.admin')

@section('title', 'إدارة الأقسام الفرعية')

@section('content-body')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">إدارة الأقسام الفرعية</h1>
        <p class="text-sm text-slate-500 mt-1">إنشاء وتعديل وحذف الأقسام الفرعية</p>
    </div>
    <button onclick="document.getElementById('create-modal').classList.remove('hidden')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        قسم فرعي جديد
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الاسم</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">القسم الرئيسي</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($subcategories as $sub)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $sub->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $sub->category->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($sub->is_active)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">نشط</span>
                            @else
                                <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs font-semibold rounded-full">معطل</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <button onclick="editSub({{ $sub->id }}, '{{ $sub->name }}', {{ $sub->category_id }}, {{ $sub->is_active ? 1 : 0 }})" class="text-blue-600 hover:text-blue-700 text-sm font-semibold transition">تعديل</button>
                            <form action="{{ route('admin.subcategories.delete', $sub->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700 text-sm font-semibold transition" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">لا توجد أقسام فرعية</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-200">{{ $subcategories->withQueryString()->links() }}</div>
</div>

<!-- Create Modal -->
<div id="create-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">إضافة قسم فرعي</h3>
        <form action="{{ route('admin.subcategories.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الاسم</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">القسم الرئيسي</label>
                    <select name="category_id" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                        @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">حفظ</button>
                <button type="button" onclick="document.getElementById('create-modal').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold transition">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">تعديل القسم الفرعي</h3>
        <form id="edit-form" action="" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الاسم</label>
                    <input type="text" name="name" id="edit-name" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">القسم الرئيسي</label>
                    <select name="category_id" id="edit-category" required class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 outline-none text-sm">
                        @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit-active" class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <label class="text-sm font-semibold text-slate-700">نشط</label>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">حفظ</button>
                <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold transition">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
function editSub(id, name, catId, active) {
    document.getElementById('edit-form').action = '{{ route("admin.subcategories.update", ":id") }}'.replace(':id', id);
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-category').value = catId;
    document.getElementById('edit-active').checked = active == 1;
    document.getElementById('edit-modal').classList.remove('hidden');
}
</script>
@endsection