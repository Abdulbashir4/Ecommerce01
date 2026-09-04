@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div>
        <p class="text-sm font-bold uppercase tracking-widest text-fuchsia-600">Demo Product Tools</p>
        <h1 class="mt-1 text-3xl font-black text-slate-950">Amazon → Preview → নিজের Demo Content → Import</h1>
        <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-500">
            Amazon-এর একটি public product URL দিন। Preview থেকে পাওয়া তথ্য আপনি নিজের মতো করে edit করবেন। তারপর শুধু Demo Product হিসেবে আপনার existing Product system-এ তৈরি হবে। কোনো Amazon API credential এই workflow-এ প্রয়োজন নেই এবং login/CAPTCHA bypass করা হয় না।
        </p>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-800">{{ $errors->first() }}</div>
    @endif

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="mb-2 block text-sm font-black">Amazon Product URL</label>
                <input id="productUrl" type="url" value="{{ old('source_url') }}" required
                    placeholder="https://www.amazon.com/dp/..."
                    class="w-full rounded-2xl border border-slate-200 px-4 py-4 text-sm font-semibold outline-none focus:border-fuchsia-400 focus:ring-4 focus:ring-fuchsia-50">
                <p class="mt-2 text-xs text-slate-400">শুধু একটি public Amazon product page URL দিন।</p>
            </div>
            <button id="previewBtn" type="button" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-fuchsia-600 to-purple-600 px-7 py-4 text-sm font-black text-white shadow-lg shadow-fuchsia-200 hover:-translate-y-0.5">
                <i class="fa-solid fa-eye mr-2"></i> Preview Product
            </button>
        </div>
        <div id="previewStatus" class="mt-4 hidden rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600"></div>
    </section>

    <form id="importForm" method="POST" action="{{ route('admin.product-importer.import') }}" class="hidden space-y-6">
        @csrf
        <input type="hidden" name="source_url" id="sourceUrl">
        <input type="hidden" name="source_product_id" id="sourceProductId">

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-fuchsia-600">Step 1</p>
                    <h2 class="mt-1 text-xl font-black">নিজের Demo Content তৈরি করুন</h2>
                </div>
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700">DEMO PRODUCT</span>
            </div>

            <div class="grid gap-5 lg:grid-cols-[280px_1fr]">
                <div>
                    <div id="mainPreviewWrap" class="hidden overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 p-3">
                        <img id="mainPreview" class="h-64 w-full rounded-2xl object-contain" alt="Amazon product image">
                    </div>
                    <div id="noImageMessage" class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm font-semibold text-slate-500">Preview করার পর Amazon থেকে পাওয়া আসল product images এখানে দেখা যাবে। কোনো default image ব্যবহার করা হবে না।</div>
                    <p id="imageHelp" class="mt-2 text-xs leading-5 text-slate-400">শুধু Amazon product gallery থেকে পাওয়া আসল image নির্বাচন করুন। <b>Make Main</b> দিয়ে Main image নির্ধারণ করুন; কোনো image নিজে থেকে Main করা হবে না।</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="md:col-span-2">
                        <span class="mb-2 block text-sm font-black">Demo Product Name *</span>
                        <input name="product_name" id="productName" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">SKU</span>
                        <input name="sku" id="sku" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">Demo Price (৳)</span>
                        <input name="price" id="price" type="number" min="0" step="0.01" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">Discount Amount</span>
                        <input name="discount_price" id="discountPrice" type="number" min="0" step="0.01" value="0" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">Discount %</span>
                        <input name="discount_percent" id="discountPercent" type="number" min="0" max="100" value="0" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">Stock Qty</span>
                        <input name="stock_qty" id="stockQty" type="number" min="0" value="1" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">Stock Status</span>
                        <select name="stock_status" id="stockStatus" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                            <option>In Stock</option><option>Out of Stock</option>
                        </select>
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">Category</span>
                        <select name="category_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                            <option value="">— Select —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">Subcategory</span>
                        <select name="subcategory_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                            <option value="">— Select —</option>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->subcategory_id }}">{{ $subcategory->subcategory_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-2 block text-sm font-black">Brand</span>
                        <select name="brand_id" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                            <option value="">— Select —</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->brand_id }}">{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="md:col-span-2">
                        <span class="mb-2 block text-sm font-black">Short Description</span>
                        <textarea name="short_description" id="shortDescription" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm leading-6"></textarea>
                    </label>
                    <label class="md:col-span-2">
                        <span class="mb-2 block text-sm font-black">Long Description</span>
                        <textarea name="long_description" id="longDescription" rows="7" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm leading-6"></textarea>
                    </label>
                    <label class="md:col-span-2">
                        <span class="mb-2 block text-sm font-black">Specifications / Demo Details</span>
                        <textarea name="specifications" id="specifications" rows="7" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm leading-6"></textarea>
                    </label>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-widest text-fuchsia-600">Step 2</p>
                <h2 class="mt-1 text-xl font-black">Images নির্বাচন করুন</h2>
                <p class="mt-1 text-sm text-slate-500">যে image-এ <b>Make Main</b> নির্বাচন করবেন সেটিই Main/Thumbnail হবে। <b>Keep image</b> দিয়ে কোন কোন অতিরিক্ত image Gallery-তে যাবে তা আলাদা করে নির্বাচন করুন।</p>
            </div>
            <div id="imageGrid" class="grid gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-6"></div>
            <p id="noGalleryMessage" class="hidden mt-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-700">এই Amazon page থেকে কোনো আসল product gallery image পাওয়া যায়নি। Default image দেখানো বা ব্যবহার করা হবে না।</p>
            <div id="imageInputs"></div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <p class="text-xs font-black uppercase tracking-widest text-fuchsia-600">Step 3</p>
                <h2 class="mt-1 text-xl font-black">SEO Content</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label>
                    <span class="mb-2 block text-sm font-black">Meta Title</span>
                    <input name="meta_title" id="metaTitle" maxlength="255" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </label>
                <label>
                    <span class="mb-2 block text-sm font-black">Meta Keywords</span>
                    <input name="meta_keywords" id="metaKeywords" maxlength="255" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </label>
                <label class="md:col-span-2">
                    <span class="mb-2 block text-sm font-black">Meta Description</span>
                    <textarea name="meta_description" id="metaDescription" maxlength="160" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></textarea>
                </label>
            </div>
        </section>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <button id="importBtn" type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-fuchsia-600 to-purple-600 px-8 py-4 text-sm font-black text-white shadow-lg shadow-fuchsia-200 hover:-translate-y-0.5">
                <i class="fa-solid fa-cloud-arrow-down mr-2"></i> Import Demo Product
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const urlInput = document.getElementById('productUrl');
    const previewBtn = document.getElementById('previewBtn');
    const previewStatus = document.getElementById('previewStatus');
    const importForm = document.getElementById('importForm');
    const token = document.querySelector('input[name="_token"]').value;
    const imageGrid = document.getElementById('imageGrid');
    const imageInputs = document.getElementById('imageInputs');
    const mainPreviewWrap = document.getElementById('mainPreviewWrap');
    const noImageMessage = document.getElementById('noImageMessage');
    const noGalleryMessage = document.getElementById('noGalleryMessage');
    let images = [];

    const setValue = (id, value) => { document.getElementById(id).value = value ?? ''; };

    function rebuildImageInputs() {
        imageInputs.innerHTML = '';
        const checked = [...imageGrid.querySelectorAll('input[data-role="keep"]:checked')].map(x => x.value);
        const main = imageGrid.querySelector('input[data-role="main"]:checked')?.value || '';

        if (main) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'thumbnail_url';
            input.value = main;
            imageInputs.appendChild(input);
        }

        checked.filter(src => src !== main).slice(0, 11).forEach(src => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'gallery_urls[]';
            input.value = src;
            imageInputs.appendChild(input);
        });

        if (main) document.getElementById('mainPreview').src = main;
        mainPreviewWrap.classList.toggle('hidden', !main);
    }

    function renderImages(list) {
        images = [...new Set((list || []).filter(Boolean))].slice(0, 12);
        imageGrid.innerHTML = '';
        noGalleryMessage.classList.toggle('hidden', images.length > 0);
        noImageMessage.classList.toggle('hidden', images.length > 0);
        mainPreviewWrap.classList.add('hidden');
        imageInputs.innerHTML = '';
        if (images.length === 0) return;

        images.forEach((src, index) => {
            const card = document.createElement('div');
            card.className = 'group rounded-2xl border border-slate-200 bg-slate-50 p-2 hover:border-fuchsia-300';
            const safeSrc = src.replaceAll('"', '&quot;');
            card.innerHTML = `
                <div class="relative overflow-hidden rounded-xl bg-white">
                    <img src="${safeSrc}" class="h-36 w-full object-contain" alt="">
                    <span data-main-badge class="absolute left-2 top-2 hidden rounded-full bg-fuchsia-600 px-2 py-1 text-[10px] font-black text-white shadow">MAIN</span>
                </div>
                <div class="mt-2 space-y-2 px-1 py-1 text-xs font-bold text-slate-600">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="main_image_choice" data-role="main" value="${safeSrc}" class="border-slate-300 text-fuchsia-600 focus:ring-fuchsia-500">
                        <span>Make Main</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" data-role="keep" value="${safeSrc}" class="rounded border-slate-300 text-fuchsia-600 focus:ring-fuchsia-500">
                        <span>Keep image</span>
                    </label>
                </div>`;
            imageGrid.appendChild(card);
        });

        imageGrid.querySelectorAll('input[data-role="main"]').forEach(radio => radio.addEventListener('change', () => {
            imageGrid.querySelectorAll('[data-main-badge]').forEach(badge => badge.classList.add('hidden'));
            radio.closest('div.group').querySelector('[data-main-badge]').classList.remove('hidden');
            const keep = radio.closest('div.group').querySelector('input[data-role="keep"]');
            if (keep) keep.checked = true;
            rebuildImageInputs();
        }));

        imageGrid.querySelectorAll('input[data-role="keep"]').forEach(cb => cb.addEventListener('change', () => {
            const selected = imageGrid.querySelectorAll('input[data-role="keep"]:checked');
            if (selected.length > 12) cb.checked = false;
            rebuildImageInputs();
        }));
    }

    previewBtn.addEventListener('click', async () => {
        const url = urlInput.value.trim();
        if (!url) return;
        previewBtn.disabled = true;
        previewBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Reading Amazon...';
        previewStatus.classList.remove('hidden');
        previewStatus.textContent = 'Amazon product page পড়া হচ্ছে। কয়েক সেকেন্ড লাগতে পারে...';
        try {
            const response = await fetch('{{ route('admin.product-importer.preview') }}', {
                method: 'POST',
                headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':token},
                body: JSON.stringify({url})
            });
            const result = await response.json();
            if (!response.ok || !result.ok) throw new Error(result.message || 'Preview failed');
            const p = result.product || {};
            document.getElementById('sourceUrl').value = url;
            setValue('sourceProductId', p.source_product_id || '');
            setValue('productName', p.name || '');
            setValue('sku', p.sku || '');
            setValue('price', p.price ?? '');
            setValue('shortDescription', p.description || '');
            setValue('longDescription', p.description || '');
            setValue('specifications', p.specifications || '');
            setValue('metaTitle', p.name || '');
            setValue('metaKeywords', [p.name, p.brand, 'demo product', 'medical equipment'].filter(Boolean).join(', '));
            setValue('metaDescription', (p.description || p.name || '').slice(0, 160));
            const previewImages = p.images || (p.thumbnail ? [p.thumbnail] : []);
            renderImages(previewImages);
            if (previewImages.length > 0) {
                document.getElementById('mainPreview').src = previewImages[0];
            }
            importForm.classList.remove('hidden');
            previewStatus.textContent = 'Preview সফল। এখন title, description, price, category ও images নিজের মতো করে ঠিক করে Import Demo Product চাপুন।';
            importForm.scrollIntoView({behavior:'smooth', block:'start'});
        } catch (e) {
            previewStatus.textContent = e.message || 'Preview failed';
        } finally {
            previewBtn.disabled = false;
            previewBtn.innerHTML = '<i class="fa-solid fa-eye mr-2"></i> Preview Product';
        }
    });

    document.getElementById('importForm').addEventListener('submit', () => {
        rebuildImageInputs();
        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Creating Demo Product...';
    });
})();
</script>
@endpush
