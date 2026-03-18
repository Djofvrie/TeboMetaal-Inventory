<x-layouts.public title="Zoeken">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Zoeken</h1>

        {{-- Search form --}}
        <form action="{{ route('search') }}" method="GET" class="mb-8">
            <div class="flex">
                <input type="text" name="q" value="{{ $q }}" placeholder="Zoek op productnaam, afmeting of categorie..."
                       autofocus
                       class="flex-1 rounded-l-xl border-slate-300 text-lg px-5 py-3 focus:border-indigo-400 focus:ring-indigo-400">
                <button type="submit"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-3 rounded-r-xl font-semibold transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>

        {{-- Results --}}
        @if(strlen($q) < 2)
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-slate-500 text-lg">Typ minimaal 2 tekens om te zoeken</p>
            </div>
        @elseif($products->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-slate-500 text-lg">Geen resultaten gevonden voor '<span class="font-semibold text-slate-700">{{ $q }}</span>'</p>
            </div>
        @else
            <p class="text-sm text-slate-500 mb-4">{{ $products->count() }} {{ $products->count() === 1 ? 'resultaat' : 'resultaten' }} voor '<span class="font-semibold text-slate-700">{{ $q }}</span>'</p>

            @foreach($products->groupBy('category.name') as $categoryName => $categoryProducts)
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-indigo-800 mb-3">{{ $categoryName }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($categoryProducts as $product)
                            <a href="{{ route('product.show', $product) }}"
                               class="bg-white rounded-xl shadow-md hover:shadow-lg hover:scale-[1.02] transition-all duration-200 p-5 group">
                                <div class="font-semibold text-slate-700 group-hover:text-indigo-500 transition-colors">
                                    {{ $product->name }}
                                </div>
                                @if($product->dimension)
                                    <div class="text-sm text-slate-500 mt-1">{{ $product->dimension }}</div>
                                @endif
                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-xs text-slate-400">{{ $product->category->name }}</span>
                                    <span class="text-xs bg-slate-100 text-slate-600 rounded-full px-2 py-0.5">
                                        {{ $product->variants->count() }} {{ $product->variants->count() === 1 ? 'variant' : 'varianten' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</x-layouts.public>
