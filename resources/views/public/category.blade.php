<x-layouts.public :title="$category->name">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-indigo-500">Home</a>
            <span class="mx-2">›</span>
            <span class="text-slate-800 font-medium">{{ $category->name }}</span>
        </nav>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-800">{{ $category->name }}</h1>
            <a href="{{ route('home') }}" class="text-sm text-indigo-800 hover:text-indigo-500 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Terug
            </a>
        </div>

        <p class="text-slate-500 mb-6">Selecteer een afmeting</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($category->products as $product)
            <a href="{{ route('product.show', $product) }}"
               class="bg-white rounded-xl shadow-md hover:shadow-lg hover:scale-[1.02] transition-all duration-200 p-5 text-center group">
                <div class="w-10 h-10 mx-auto mb-2 text-indigo-800 group-hover:text-indigo-500 transition-colors opacity-40">
                    <x-category-icon :slug="$category->slug" />
                </div>
                <span class="font-semibold text-indigo-800 group-hover:text-indigo-500 transition-colors text-lg">{{ $product->dimension }}</span>
                <span class="block text-xs text-slate-400 mt-1">{{ $product->variants->count() }} {{ $product->variants->count() === 1 ? 'variant' : 'varianten' }}</span>
            </a>
            @endforeach
        </div>
    </div>
</x-layouts.public>
