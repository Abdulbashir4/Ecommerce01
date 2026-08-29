@php
    $hasChildren = $node->relationLoaded('childrenRecursive') ? $node->childrenRecursive->isNotEmpty() : $node->children()->exists();
    $hasBrands = $node->relationLoaded('brands') ? $node->brands->isNotEmpty() : $node->brands()->exists();
    $hasMenu = $hasChildren || $hasBrands;
@endphp

<li class="category-tree-item group relative">
    <div class="flex items-center justify-between rounded-xl px-3 py-2.5 transition hover:bg-cyan-50">
        <a href="{{ route('shop', ['subcategory_id' => $node->subcategory_id]) }}" class="min-w-0 flex-1 truncate text-slate-700 hover:text-cyan-700">{{ $node->subcategory_name }}</a>
        @if($hasMenu)
            <button type="button" class="category-tree-toggle ml-2 grid h-7 w-7 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-cyan-100 hover:text-cyan-700 lg:hidden" aria-label="Open {{ $node->subcategory_name }}">
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
            </button>
            <span class="category-tree-arrow hidden h-7 w-7 place-items-center rounded-lg text-slate-400 lg:grid"><i class="fa-solid fa-chevron-right text-[9px]"></i></span>
        @endif
    </div>

    @if($hasMenu)
        <ul class="category-tree-children hidden list-none space-y-1 border-l border-cyan-100 bg-slate-50/70 py-1 pl-2 lg:static lg:mt-1 lg:ml-3 lg:w-auto lg:rounded-xl lg:border-l-2 lg:border-cyan-100 lg:bg-slate-50 lg:p-1.5 lg:shadow-none">
            @if($hasChildren)
                @foreach($node->childrenRecursive as $child)
                    @include('partials.category-sidebar-node', ['node' => $child, 'depth' => ($depth ?? 1) + 1])
                @endforeach
            @endif

            @if($hasBrands)
                @foreach($node->brands as $brand)
                    <li>
                        <a href="{{ route('shop', ['brand_id' => $brand->brand_id]) }}" class="block rounded-xl px-3 py-2.5 text-slate-600 transition hover:bg-cyan-50 hover:text-cyan-700">
                            <i class="fa-solid fa-tag mr-2 text-[10px] text-cyan-500"></i>{{ $brand->brand_name }}
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    @endif
</li>
