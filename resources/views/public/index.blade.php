<x-layouts.public title="Categorieën">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Selecteer een categorie</h1>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}"
               class="bg-white rounded-xl shadow-md hover:shadow-lg hover:scale-[1.02] transition-all duration-200 p-6 flex flex-col items-center text-center group">
                <div class="w-16 h-16 sm:w-20 sm:h-20 mb-4 text-indigo-800 group-hover:text-indigo-500 transition-colors">
                    <x-category-icon :slug="$category->slug" />
                </div>
                <span class="font-semibold text-slate-700 group-hover:text-indigo-500 transition-colors">{{ $category->name }}</span>
                <span class="text-xs text-slate-400 mt-1">{{ $category->products_count }} producten</span>
            </a>
            @endforeach
        </div>
    </div>
</x-layouts.public>
