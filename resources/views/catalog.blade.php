<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Catalogus - Tebo Metaal</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-800">
        <div class="min-h-screen flex flex-col">
            <header class="bg-white shadow-lg">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-20">
                        <a href="{{ route('catalog') }}" class="flex items-center">
                            <img src="{{ asset('images/logo.svg') }}" alt="Tebo Metaal" class="h-12">
                        </a>
                        <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-slate-800 transition-colors">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Inloggen
                        </a>
                    </div>
                </div>
            </header>

            <main class="flex-1" x-data="catalog()" x-cloak>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-slate-800 mb-2">Productcatalogus</h1>
                        <p class="text-slate-500">Overzicht van ons assortiment en beschikbaarheid.</p>
                    </div>

                    {{-- Search + filters --}}
                    <div class="bg-white rounded-xl shadow-sm p-4 mb-8 space-y-4 sm:space-y-0 sm:flex sm:items-center sm:gap-4">
                        {{-- Search --}}
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-model="search" placeholder="Zoek op product of afmeting..."
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Category filter --}}
                        <div class="flex flex-wrap gap-2">
                            <button @click="activeCategory = null"
                                    :class="activeCategory === null ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                Alles
                            </button>
                            @foreach($categories as $category)
                                <button @click="activeCategory = activeCategory === '{{ $category->slug }}' ? null : '{{ $category->slug }}'"
                                        :class="activeCategory === '{{ $category->slug }}' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Stock filter --}}
                        <div>
                            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer whitespace-nowrap">
                                <input type="checkbox" x-model="onlyInStock" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Alleen op voorraad
                            </label>
                        </div>
                    </div>

                    {{-- Results count --}}
                    <p class="text-sm text-slate-400 mb-6" x-show="search || activeCategory || onlyInStock">
                        <span x-text="visibleCount"></span> producten gevonden
                    </p>

                    {{-- Categories + products --}}
                    <div class="space-y-12">
                        @foreach($categories as $category)
                        <div x-show="isCategoryVisible('{{ $category->slug }}')" x-transition.opacity data-category="{{ $category->slug }}">
                            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-200">
                                <div class="w-9 h-9 text-indigo-700">
                                    <x-category-icon :slug="$category->slug" />
                                </div>
                                <h2 class="text-xl font-bold text-slate-800">{{ $category->name }}</h2>
                                <span class="text-sm text-slate-400">({{ $category->products->count() }})</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($category->products as $product)
                                    @php
                                        $totalStock = $product->variants->sum(fn($v) => $v->stockItems->sum('quantity'));
                                        $inStock = $totalStock > 0;
                                        $variantCount = $product->variants->count();
                                    @endphp
                                    <div x-show="isProductVisible('{{ $category->slug }}', {{ json_encode($product->name) }}, {{ json_encode($product->dimension) }}, {{ $inStock ? 'true' : 'false' }})"
                                         class="bg-white rounded-lg shadow-sm p-4 flex items-center justify-between">
                                        <div>
                                            <span class="font-semibold text-slate-800">{{ $product->name }}</span>
                                            @if($variantCount > 0)
                                                <span class="block text-xs text-slate-400 mt-0.5">
                                                    {{ $variantCount }} {{ $variantCount === 1 ? 'variant' : 'varianten' }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($inStock)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                Op voorraad
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                Niet op voorraad
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- No results --}}
                    <div x-show="visibleCount === 0" class="text-center py-16">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <p class="text-slate-500">Geen producten gevonden.</p>
                        <button @click="search = ''; activeCategory = null; onlyInStock = false" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800">Filters wissen</button>
                    </div>
                </div>
            </main>

            <footer class="bg-slate-800 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                        <p class="text-sm text-slate-400">&copy; {{ date('Y') }} Tebo Metaal. Alle rechten voorbehouden.</p>
                        <p class="text-xs text-slate-500">Productcatalogus</p>
                    </div>
                </div>
            </footer>
        </div>

        <script>
        function catalog() {
            return {
                search: '',
                activeCategory: null,
                onlyInStock: false,

                isProductVisible(categorySlug, name, dimension, inStock) {
                    if (this.activeCategory && this.activeCategory !== categorySlug) return false;
                    if (this.onlyInStock && !inStock) return false;
                    if (this.search.length >= 2) {
                        const q = this.search.toLowerCase();
                        const haystack = (name + ' ' + (dimension || '')).toLowerCase();
                        if (!haystack.includes(q)) return false;
                    }
                    return true;
                },

                isCategoryVisible(slug) {
                    if (this.activeCategory && this.activeCategory !== slug) return false;
                    const el = document.querySelector(`[data-category="${slug}"]`);
                    if (!el) return true;
                    const cards = el.querySelectorAll('[x-show^="isProductVisible"]');
                    // Let Alpine evaluate first, then check - use a simpler approach
                    return true; // Category header always shows if filter matches, products hide individually
                },

                get visibleCount() {
                    let count = 0;
                    document.querySelectorAll('[data-category]').forEach(section => {
                        const slug = section.dataset.category;
                        if (this.activeCategory && this.activeCategory !== slug) return;
                        section.querySelectorAll('.grid > div').forEach(card => {
                            if (card.style.display !== 'none') count++;
                        });
                    });
                    return count;
                }
            }
        }
        </script>
    </body>
</html>
