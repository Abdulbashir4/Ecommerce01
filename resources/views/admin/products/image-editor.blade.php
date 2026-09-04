@extends('layouts.admin')

@section('content')
@php
    $imagePath = $selectedProduct?->featured_image ?: $selectedProduct?->thumbnail;
    if ($imagePath) {
        $imagePath = ltrim($imagePath, '/');
        if (!\Illuminate\Support\Str::startsWith($imagePath, ['uploads/products/', 'storage/'])) {
            $imagePath = 'uploads/products/'.$imagePath;
        }
    }
@endphp

<div class="mx-auto max-w-[1600px] space-y-5">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.2em] text-indigo-600">Graphics Studio</p>
            <h1 class="text-3xl font-black tracking-tight">Product Image Editor</h1>
            <p class="mt-1 text-sm text-slate-500">Resize, crop, padding, background removal, centering and visual adjustments — then save the finished main image.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.products') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">← Products</a>
            @if($selectedProduct)
                <button type="button" id="autoDesignBtn" class="rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-indigo-200 hover:-translate-y-0.5">✨ Auto Design</button>
            @endif
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-[300px_minmax(0,1fr)_360px]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 p-4">
                <h2 class="font-black">1. Select Product</h2>
                <form class="mt-3 flex gap-2" method="GET" action="{{ route('admin.products.image-editor') }}">
                    <input name="q" value="{{ request('q') }}" placeholder="Search product or SKU" class="min-w-0 flex-1 rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100">
                    <button class="rounded-xl bg-slate-950 px-3 text-white"><i class="fa-solid fa-search"></i></button>
                </form>
            </div>
            <div class="max-h-[680px] overflow-y-auto p-2">
                @forelse($products as $p)
                    @php
                        $thumb = $p->thumbnail ?: $p->featured_image;
                        if ($thumb) {
                            $thumb = ltrim($thumb, '/');
                            if (!\Illuminate\Support\Str::startsWith($thumb, ['uploads/products/', 'storage/'])) $thumb = 'uploads/products/'.$thumb;
                        }
                    @endphp
                    <a href="{{ route('admin.products.image-editor', array_filter(['product' => $p->product_id, 'q' => request('q')])) }}" class="mb-1 flex items-center gap-3 rounded-xl p-2.5 transition {{ $selectedProduct?->product_id === $p->product_id ? 'bg-indigo-50 ring-1 ring-indigo-200' : 'hover:bg-slate-50' }}">
                        @if($thumb)
                            <img src="{{ asset($thumb) }}" class="h-12 w-12 shrink-0 rounded-lg border border-slate-200 bg-white object-contain" alt="">
                        @else
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-lg bg-slate-100 text-slate-400"><i class="fa-solid fa-image"></i></span>
                        @endif
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-black text-slate-800">{{ $p->product_name }}</span>
                            <span class="block truncate text-xs text-slate-500">#{{ $p->product_id }}{{ $p->sku ? ' · '.$p->sku : '' }}</span>
                        </span>
                    </a>
                @empty
                    <div class="p-6 text-center text-sm text-slate-500">No products found.</div>
                @endforelse
            </div>
            <div class="border-t border-slate-100 p-3">{{ $products->links() }}</div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            @if(!$selectedProduct || !$imagePath)
                <div class="flex min-h-[650px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-8 text-center">
                    <div class="grid h-20 w-20 place-items-center rounded-3xl bg-white text-3xl text-indigo-500 shadow-sm"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                    <h2 class="mt-5 text-xl font-black">Select a product to start</h2>
                    <p class="mt-2 max-w-md text-sm text-slate-500">Choose a product from the left. Its featured/main image will be loaded into the graphics editor.</p>
                </div>
            @else
                <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-black">2. Edit Main Image</h2>
                        <p class="text-xs text-slate-500">{{ $selectedProduct->product_name }}</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Changes are previewed before saving</span>
                </div>

                <div id="canvasWrap" class="relative flex min-h-[520px] items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-[linear-gradient(45deg,#f1f5f9_25%,transparent_25%),linear-gradient(-45deg,#f1f5f9_25%,transparent_25%),linear-gradient(45deg,transparent_75%,#f1f5f9_75%),linear-gradient(-45deg,transparent_75%,#f1f5f9_75%)] bg-[length:28px_28px] bg-[position:0_0,0_14px,14px_-14px,-14px_0px] p-4">
                    <canvas id="editorCanvas" class="max-h-[620px] max-w-full rounded-xl shadow-xl"></canvas>
                    <div id="emptyCanvas" class="hidden text-sm text-slate-500">Image preview unavailable.</div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-8">
                    <button type="button" data-action="rotate-left" class="tool-btn">↶ Rotate</button>
                    <button type="button" data-action="rotate-right" class="tool-btn">↷ Rotate</button>
                    <button type="button" data-action="flip-h" class="tool-btn">↔ Flip H</button>
                    <button type="button" data-action="flip-v" class="tool-btn">↕ Flip V</button>
                    <button type="button" data-action="fit" class="tool-btn">⊙ Center</button>
                    <button type="button" data-action="reset-crop" class="tool-btn">⌗ Reset Crop</button>
                    <button type="button" data-action="reset" class="tool-btn">↺ Reset</button>
                    <button type="button" id="renderBtn" class="tool-btn bg-indigo-600 text-white hover:bg-indigo-700">Apply</button>
                </div>

                <div class="mt-4 rounded-xl border border-indigo-100 bg-indigo-50 p-3 text-xs leading-5 text-indigo-800">
                    <b>Tip:</b> Use <b>Auto Design</b> for a square, centered product image with background removal and balanced padding. For difficult/complex backgrounds, review the preview because automatic removal uses edge-connected background detection rather than an external AI service.
                </div>
            @endif
        </section>

        @if($selectedProduct && $imagePath)
            <aside class="space-y-4">
                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 class="font-black">3. Canvas & Resize</h2>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <label class="text-xs font-bold text-slate-600">Width (px)<input id="outW" type="number" min="200" max="5000" value="1200" class="field"></label>
                        <label class="text-xs font-bold text-slate-600">Height (px)<input id="outH" type="number" min="200" max="5000" value="1200" class="field"></label>
                    </div>
                    <label class="mt-3 flex items-center gap-2 text-xs font-bold text-slate-600"><input id="lockRatio" type="checkbox" checked class="rounded"> Lock ratio</label>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <button type="button" id="resizePresetSquare" class="preset">1200 × 1200</button>
                        <button type="button" id="resizePresetLarge" class="preset">1600 × 1600</button>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 class="font-black">Padding</h2>
                    <p class="mt-1 text-xs text-slate-500">Add space independently around the product.</p>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <label class="text-xs font-bold text-slate-600">Top<input id="padT" type="number" min="0" max="1000" value="80" class="field"></label>
                        <label class="text-xs font-bold text-slate-600">Right<input id="padR" type="number" min="0" max="1000" value="80" class="field"></label>
                        <label class="text-xs font-bold text-slate-600">Bottom<input id="padB" type="number" min="0" max="1000" value="80" class="field"></label>
                        <label class="text-xs font-bold text-slate-600">Left<input id="padL" type="number" min="0" max="1000" value="80" class="field"></label>
                    </div>
                    <button type="button" id="equalPadding" class="mt-3 w-full preset">Equal padding</button>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div><h2 class="font-black">Crop</h2><p class="text-xs text-slate-500">Trim each edge of the source image.</p></div>
                        <button type="button" id="autoCropBtn" class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-bold hover:bg-slate-200">Auto crop</button>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <label class="text-xs font-bold text-slate-600">Top %<input id="cropT" type="number" min="0" max="45" value="0" class="field"></label>
                        <label class="text-xs font-bold text-slate-600">Right %<input id="cropR" type="number" min="0" max="45" value="0" class="field"></label>
                        <label class="text-xs font-bold text-slate-600">Bottom %<input id="cropB" type="number" min="0" max="45" value="0" class="field"></label>
                        <label class="text-xs font-bold text-slate-600">Left %<input id="cropL" type="number" min="0" max="45" value="0" class="field"></label>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 class="font-black">Background & Visual</h2>
                    <button type="button" id="removeBgBtn" class="mt-3 w-full rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm font-black text-rose-700 hover:bg-rose-100">✂ Remove Background</button>
                    <label class="mt-3 block text-xs font-bold text-slate-600">Removal tolerance <input id="bgTolerance" type="range" min=20 max=100 value="45" class="w-full"><span id="bgToleranceVal">45</span></label>
                    <label class="mt-3 flex items-center gap-2 text-xs font-bold text-slate-600"><input id="transparentBg" type="checkbox" checked class="rounded"> Transparent background</label>
                    <label class="mt-3 block text-xs font-bold text-slate-600">Background color<input id="bgColor" type="color" value="#ffffff" class="mt-1 h-10 w-full cursor-pointer rounded-lg border border-slate-200 bg-white"></label>
                    <div class="mt-4 space-y-3">
                        <label class="block text-xs font-bold text-slate-600">Brightness <input id="brightness" type="range" min="-100" max="100" value="0" class="w-full"><span id="brightnessVal">0</span></label>
                        <label class="block text-xs font-bold text-slate-600">Contrast <input id="contrast" type="range" min="-100" max="100" value="0" class="w-full"><span id="contrastVal">0</span></label>
                        <label class="block text-xs font-bold text-slate-600">Saturation <input id="saturation" type="range" min="-100" max="100" value="0" class="w-full"><span id="saturationVal">0</span></label>
                    </div>
                </section>

                <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                    <h2 class="font-black text-emerald-900">Save to Product</h2>
                    <p class="mt-1 text-xs leading-5 text-emerald-800">The generated image will replace both the product thumbnail and featured/main image. The old main images are deleted after the new file is stored successfully.</p>
                    <form id="saveForm" class="mt-3" method="POST" action="{{ route('admin.products.image-editor.save', $selectedProduct) }}">
                        @csrf
                        <input type="hidden" name="image" id="imagePayload">
                        <input type="hidden" name="width" id="saveW">
                        <input type="hidden" name="height" id="saveH">
                        <button id="saveBtn" type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 hover:bg-emerald-700">💾 Save Image to Product</button>
                    </form>
                    <p class="mt-2 text-center text-[11px] text-emerald-700">PNG/WebP/JPEG input is rendered to WebP for the new main image.</p>
                </section>
            </aside>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($selectedProduct && $imagePath)
<script>
(() => {
    const sourceUrl = @json(asset($imagePath));
    const canvas = document.getElementById('editorCanvas');
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    const source = new Image();
    source.crossOrigin = 'anonymous';

    const $ = id => document.getElementById(id);
    const num = id => Math.max(0, Number($(id).value || 0));
    const state = {
        rotate: 0,
        flipH: false,
        flipV: false,
        removeBg: false,
        crop: {t:0,r:0,b:0,l:0},
        originalW: 0,
        originalH: 0,
        bgTolerance: 45,
    };

    const updateLabels = () => {
        $('brightnessVal').textContent = $('brightness').value;
        $('contrastVal').textContent = $('contrast').value;
        $('saturationVal').textContent = $('saturation').value;
    };

    function clampCrop() {
        state.crop.t = Math.min(45, num('cropT'));
        state.crop.r = Math.min(45, num('cropR'));
        state.crop.b = Math.min(45, num('cropB'));
        state.crop.l = Math.min(45, num('cropL'));
        const vertical = state.crop.t + state.crop.b;
        const horizontal = state.crop.l + state.crop.r;
        if (vertical >= 90) state.crop.b = Math.max(0, 89 - state.crop.t);
        if (horizontal >= 90) state.crop.r = Math.max(0, 89 - state.crop.l);
        ['cropT','cropR','cropB','cropL'].forEach((id, i) => $(id).value = [state.crop.t,state.crop.r,state.crop.b,state.crop.l][i]);
    }

    function getCropCanvas() {
        const rot = ((state.rotate % 360) + 360) % 360;
        const swap = rot === 90 || rot === 270;
        const cw = Math.max(1, Math.round(state.originalW * (1 - (state.crop.l + state.crop.r) / 100)));
        const ch = Math.max(1, Math.round(state.originalH * (1 - (state.crop.t + state.crop.b) / 100)));
        const temp = document.createElement('canvas');
        temp.width = swap ? ch : cw;
        temp.height = swap ? cw : ch;
        const c = temp.getContext('2d', {willReadFrequently:true});
        c.save();
        c.translate(temp.width/2, temp.height/2);
        c.rotate(rot * Math.PI / 180);
        c.scale(state.flipH ? -1 : 1, state.flipV ? -1 : 1);
        c.filter = `brightness(${100 + Number($('brightness').value)}%) contrast(${100 + Number($('contrast').value)}%) saturate(${100 + Number($('saturation').value)}%)`;
        c.drawImage(source,
            Math.round(state.originalW * state.crop.l / 100),
            Math.round(state.originalH * state.crop.t / 100),
            cw, ch,
            -cw/2, -ch/2, cw, ch
        );
        c.restore();
        return temp;
    }

    function removeBackgroundFromCanvas(input) {
        const c = input.getContext('2d', {willReadFrequently:true});
        const img = c.getImageData(0,0,input.width,input.height);
        const d = img.data;
        const w = input.width, h = input.height;
        const visited = new Uint8Array(w*h);
        const queue = new Int32Array(w*h);
        let head=0, tail=0;
        const seed = (x,y) => {
            if(x<0||y<0||x>=w||y>=h) return;
            const idx=y*w+x;
            if(visited[idx]) return;
            visited[idx]=1; queue[tail++]=idx;
        };
        for(let x=0;x<w;x++){seed(x,0);seed(x,h-1)}
        for(let y=1;y<h-1;y++){seed(0,y);seed(w-1,y)}
        let sr=0,sg=0,sb=0,count=0;
        const edgeSamples=[];
        for(let i=0;i<Math.min(tail, 5000);i++){
            const idx=queue[i], p=idx*4; edgeSamples.push([d[p],d[p+1],d[p+2]]);
        }
        edgeSamples.forEach(v=>{sr+=v[0];sg+=v[1];sb+=v[2];count++});
        const br=sr/count,bg=sg/count,bb=sb/count;
        const tolerance = state.bgTolerance;
        const colorDist=(p)=>Math.sqrt((d[p]-br)**2+(d[p+1]-bg)**2+(d[p+2]-bb)**2);
        while(head<tail){
            const idx=queue[head++];
            const p=idx*4;
            if(colorDist(p) <= tolerance) {
                d[p+3]=0;
                const x=idx%w,y=Math.floor(idx/w);
                if(x>0) seed(x-1,y); if(x<w-1) seed(x+1,y); if(y>0) seed(x,y-1); if(y<h-1) seed(x,y+1);
            }
        }
        c.putImageData(img,0,0);
        return input;
    }

    function trimTransparent(input) {
        const c=input.getContext('2d',{willReadFrequently:true});
        const d=c.getImageData(0,0,input.width,input.height).data;
        let minX=input.width,minY=input.height,maxX=-1,maxY=-1;
        for(let y=0;y<input.height;y++) for(let x=0;x<input.width;x++) {
            if(d[(y*input.width+x)*4+3] > 8){minX=Math.min(minX,x);minY=Math.min(minY,y);maxX=Math.max(maxX,x);maxY=Math.max(maxY,y)}
        }
        if(maxX<0) return input;
        const out=document.createElement('canvas'); out.width=maxX-minX+1; out.height=maxY-minY+1;
        out.getContext('2d').drawImage(input,minX,minY,out.width,out.height,0,0,out.width,out.height);
        return out;
    }

    function render() {
        if(!source.complete || !source.naturalWidth) return;
        clampCrop();
        let work=getCropCanvas();
        if(state.removeBg) work=removeBackgroundFromCanvas(work);
        const autoObject = state.removeBg ? trimTransparent(work) : work;
        const ow=Math.max(200, Math.min(5000, Number($('outW').value)||1200));
        const oh=Math.max(200, Math.min(5000, Number($('outH').value)||1200));
        const pt=Math.min(1000,num('padT')), pr=Math.min(1000,num('padR')), pb=Math.min(1000,num('padB')), pl=Math.min(1000,num('padL'));
        const availableW=Math.max(1,ow-pl-pr), availableH=Math.max(1,oh-pt-pb);
        const scale=Math.min(availableW/autoObject.width,availableH/autoObject.height);
        const dw=Math.max(1,Math.round(autoObject.width*scale)), dh=Math.max(1,Math.round(autoObject.height*scale));
        canvas.width=ow; canvas.height=oh;
        ctx.clearRect(0,0,ow,oh);
        if(!$('transparentBg').checked){ctx.fillStyle=$('bgColor').value;ctx.fillRect(0,0,ow,oh)}
        ctx.imageSmoothingEnabled=true; ctx.imageSmoothingQuality='high';
        ctx.drawImage(autoObject,pl+(availableW-dw)/2,pt+(availableH-dh)/2,dw,dh);
        $('saveW').value=ow; $('saveH').value=oh;
    }

    function resetAll() {
        state.rotate=0; state.flipH=false; state.flipV=false; state.removeBg=false; state.crop={t:0,r:0,b:0,l:0};
        ['cropT','cropR','cropB','cropL'].forEach(id=>$(id).value=0);
        ['brightness','contrast','saturation'].forEach(id=>$(id).value=0);
        $('outW').value=1200; $('outH').value=1200;
        ['padT','padR','padB','padL'].forEach(id=>$(id).value=80);
        $('transparentBg').checked=true; updateLabels(); render();
    }

    function autoDesign() {
        $('outW').value=1200; $('outH').value=1200;
        ['padT','padR','padB','padL'].forEach(id=>$(id).value=100);
        ['cropT','cropR','cropB','cropL'].forEach(id=>$(id).value=0);
        $('transparentBg').checked=true;
        state.removeBg=true;
        render();
    }

    $('autoDesignBtn')?.addEventListener('click', autoDesign);
    $('autoCropBtn')?.addEventListener('click',()=>{
        if(!state.removeBg) state.removeBg=true;
        render();
        const tmp=getCropCanvas(); const trimmed=trimTransparent(removeBackgroundFromCanvas(tmp));
        // Convert the detected object bounds into crop percentages on the original image.
        if(trimmed.width < tmp.width || trimmed.height < tmp.height){
            const left=(tmp.width-trimmed.width)/2, top=(tmp.height-trimmed.height)/2;
            const baseW=state.originalW, baseH=state.originalH;
            $('cropL').value=Math.min(40, Math.max(0, left/baseW*100));
            $('cropR').value=Math.min(40, Math.max(0, left/baseW*100));
            $('cropT').value=Math.min(40, Math.max(0, top/baseH*100));
            $('cropB').value=Math.min(40, Math.max(0, top/baseH*100));
            clampCrop(); render();
        }
    });
    $('equalPadding').addEventListener('click',()=>{const v=Math.round(num('padT'));['padT','padR','padB','padL'].forEach(id=>$(id).value=v);render()});
    $('resizePresetSquare').addEventListener('click',()=>{$('outW').value=1200;$('outH').value=1200;render()});
    $('resizePresetLarge').addEventListener('click',()=>{$('outW').value=1600;$('outH').value=1600;render()});
    $('removeBgBtn').addEventListener('click',()=>{state.removeBg=!state.removeBg; $('removeBgBtn').textContent=state.removeBg?'✓ Background Removal ON':'✂ Remove Background'; render()});
    $('transparentBg').addEventListener('change',render); $('bgColor').addEventListener('input',render);
    $('bgTolerance').addEventListener('input',()=>{state.bgTolerance=Number($('bgTolerance').value);$('bgToleranceVal').textContent=state.bgTolerance;render()});
    ['brightness','contrast','saturation','outW','outH','padT','padR','padB','padL','cropT','cropR','cropB','cropL'].forEach(id=>$(id).addEventListener('input',()=>{updateLabels();render()}));
    $('outW').addEventListener('input',()=>{if($('lockRatio').checked && state.originalW){$('outH').value=Math.round(Number($('outW').value)*Number($('outH').dataset.ratio||1));render()}});
    $('outH').dataset.ratio=1;
    document.querySelectorAll('[data-action]').forEach(btn=>btn.addEventListener('click',()=>{
        const a=btn.dataset.action;
        if(a==='rotate-left') state.rotate-=90;
        if(a==='rotate-right') state.rotate+=90;
        if(a==='flip-h') state.flipH=!state.flipH;
        if(a==='flip-v') state.flipV=!state.flipV;
        if(a==='fit'){['padT','padR','padB','padL'].forEach(id=>$(id).value=100);}
        if(a==='reset-crop'){['cropT','cropR','cropB','cropL'].forEach(id=>$(id).value=0);}
        if(a==='reset'){resetAll();return;}
        render();
    }));
    $('renderBtn').addEventListener('click',render);
    $('saveForm').addEventListener('submit',(e)=>{
        render();
        const payload=canvas.toDataURL('image/webp',0.92);
        if(payload.length > 20*1024*1024){e.preventDefault();alert('The generated image is too large. Reduce the output size and try again.');return;}
        $('imagePayload').value=payload;
        $('saveBtn').disabled=true; $('saveBtn').textContent='Saving image…';
    });

    $('bgToleranceVal').textContent = $('bgTolerance').value;
    source.onload=()=>{
        state.originalW=source.naturalWidth; state.originalH=source.naturalHeight;
        const ratio=state.originalH/state.originalW;
        $('outH').dataset.ratio=ratio;
        $('outW').value=1200; $('outH').value=1200;
        updateLabels(); render();
    };
    source.onerror=()=>{$('emptyCanvas').classList.remove('hidden');canvas.classList.add('hidden')};
    source.src=sourceUrl;
})();
</script>
<style>
.tool-btn{border-radius:.75rem;border:1px solid #e2e8f0;background:#fff;padding:.65rem .55rem;font-size:.75rem;font-weight:800;color:#334155;transition:.15s}.tool-btn:hover{background:#f8fafc}
.field{margin-top:.35rem;width:100%;border:1px solid #e2e8f0;border-radius:.7rem;padding:.6rem .7rem;font-size:.85rem;font-weight:700;outline:none}.field:focus{border-color:#818cf8;box-shadow:0 0 0 4px #eef2ff}
.preset{width:100%;border:1px solid #e2e8f0;border-radius:.7rem;background:#f8fafc;padding:.55rem;font-size:.75rem;font-weight:800;color:#475569}.preset:hover{background:#eef2ff;color:#3730a3}
</style>
@endif
@endpush
