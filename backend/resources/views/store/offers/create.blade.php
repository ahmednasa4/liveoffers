@extends('layouts.store')

@section('title', 'إنشاء عرض جديد')

@section('content-body')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">إنشاء عرض جديد</h1>
    <p class="text-sm text-slate-500 mt-1">أضف عرضاً جديداً مع مولد الوصف بالذكاء الاصطناعي</p>
</div>

<form action="{{ route('store.offers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Image & AI -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Image Upload -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-bold text-slate-700 mb-4">صورة المنتج</h3>
                <div id="image-preview" class="w-full aspect-square rounded-xl bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden mb-3">
                    <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <input type="file" name="image" id="image-input" accept="image/*" class="hidden" required>
                <button type="button" id="image-btn" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-lg font-semibold transition">اختر صورة</button>
                <p class="text-xs text-slate-400 mt-2 text-center">PNG, JPG حتى 2MB</p>
            </div>

            <!-- AI Description Generator -->
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl shadow-sm border border-purple-200 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <h3 class="font-bold text-purple-900">مولد الوصف بالذكاء الاصطناعي</h3>
                </div>
                <p class="text-xs text-purple-700 mb-4">ارفع صورة المنتج وأدخل الاسم والسعر، ثم اضغط لتوليد وصف تسويقي احترافي عبر Google Gemini AI.</p>
                <button type="button" id="ai-generate-btn" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-lg font-semibold transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span id="ai-btn-text">توليد الوصف</span>
                </button>
                <div id="ai-loading" class="hidden mt-3 text-center text-sm text-purple-600">
                    <svg class="animate-spin inline w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    جاري التوليد...
                </div>
                <div id="ai-error" class="hidden mt-3 bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-lg text-sm"></div>
            </div>
        </div>

        <!-- Right Column: Form Fields -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">عنوان العرض <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" id="title-input" value="{{ old('title') }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required placeholder="مثال: هاتف ذكي بسعر مخفض">
                </div>

                <!-- Category & Subcategory -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الفئة <span class="text-rose-500">*</span></label>
                        <select name="category_id" id="category-select" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required>
                            <option value="">اختر فئة</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-old="{{ old('category_id') == $category->id ? '1' : '0' }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">الفئة الفرعية</label>
                        <select name="subcategory_id" id="subcategory-select" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                            <option value="">اختر فئة فرعية</option>
                        </select>
                    </div>
                </div>

                <!-- Prices -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">السعر الأصلي (د.أ) <span class="text-rose-500">*</span></label>
                        <input type="number" name="original_price" id="original-price-input" value="{{ old('original_price') }}" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">سعر العرض (د.أ) <span class="text-rose-500">*</span></label>
                        <input type="number" name="offer_price" id="offer-price-input" value="{{ old('offer_price') }}" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الوصف <span class="text-rose-500">*</span></label>
                    <textarea name="description" id="description-input" rows="5" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required placeholder="اكتب وصف العرض أو استخدم مولد الذكاء الاصطناعي">{{ old('description') }}</textarea>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">تاريخ البداية <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date', date('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">تاريخ الانتهاء <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date', date('Y-m-d\TH:i', strtotime('+7 days'))) }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none" required>
                    </div>
                </div>

                <!-- Options -->
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 text-orange-600 rounded border-slate-300" {{ old('is_featured') ? 'checked' : '' }}>
                        <span class="text-sm font-semibold text-slate-700">عرض مميز</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_ai_generated" id="is-ai-generated" value="1" class="w-4 h-4 text-purple-600 rounded border-slate-300">
                        <span class="text-sm font-semibold text-slate-700">وصف AI</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2.5 rounded-lg font-semibold transition">إنشاء العرض</button>
                <a href="{{ route('store.offers.index') }}" class="text-slate-600 hover:text-slate-800 px-4 py-2.5 font-semibold">إلغاء</a>
            </div>
        </div>
    </div>
</form>

<script>
// Subcategories data
const subcategories = @json($subcategories->mapWithKeys(function($items, $catId) {
    return [$catId => $items->map->only(['id', 'name'])];
}));

// Image preview
const imageInput = document.getElementById('image-input');
const imageBtn = document.getElementById('image-btn');
const imagePreview = document.getElementById('image-preview');

imageBtn.addEventListener('click', () => imageInput.click());

imageInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (ev) => {
            imagePreview.innerHTML = `<img src="${ev.target.result}" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(file);
    }
});

// Category -> Subcategory cascade
const categorySelect = document.getElementById('category-select');
const subcategorySelect = document.getElementById('subcategory-select');

categorySelect.addEventListener('change', (e) => {
    const catId = e.target.value;
    subcategorySelect.innerHTML = '<option value="">اختر فئة فرعية</option>';
    if (catId && subcategories[catId]) {
        subcategories[catId].forEach(sub => {
            const opt = document.createElement('option');
            opt.value = sub.id;
            opt.textContent = sub.name;
            subcategorySelect.appendChild(opt);
        });
    }
});

// Restore old category selection
document.querySelectorAll('#category-select option').forEach(opt => {
    if (opt.dataset.old === '1') opt.selected = true;
});
if (categorySelect.value) categorySelect.dispatchEvent(new Event('change'));

// AI Description Generator
const aiBtn = document.getElementById('ai-generate-btn');
const aiLoading = document.getElementById('ai-loading');
const aiError = document.getElementById('ai-error');
const aiBtnText = document.getElementById('ai-btn-text');

aiBtn.addEventListener('click', async () => {
    const file = imageInput.files[0];
    const title = document.getElementById('title-input').value;
    const offerPrice = document.getElementById('offer-price-input').value;

    if (!file) { showAIError('يرجى رفع صورة المنتج أولاً'); return; }
    if (!title) { showAIError('يرجى إدخال عنوان العرض'); return; }
    if (!offerPrice) { showAIError('يرجى إدخال سعر العرض'); return; }

    aiBtn.disabled = true;
    aiBtnText.textContent = 'جاري التوليد...';
    aiLoading.classList.remove('hidden');
    aiError.classList.add('hidden');

    const formData = new FormData();
    formData.append('image', file);
    formData.append('title', title);
    formData.append('offer_price', offerPrice);

    try {
        const response = await fetch('{{ route("store.ai.generate-description") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            document.getElementById('description-input').value = data.description;
            document.getElementById('is-ai-generated').checked = true;
        } else {
            showAIError(data.message || 'فشل توليد الوصف');
        }
    } catch (err) {
        showAIError('حدث خطأ في الاتصال');
    } finally {
        aiBtn.disabled = false;
        aiBtnText.textContent = 'توليد الوصف';
        aiLoading.classList.add('hidden');
    }
});

function showAIError(msg) {
    aiError.textContent = msg;
    aiError.classList.remove('hidden');
}
</script>
@endsection