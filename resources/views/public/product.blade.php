<x-layouts.public :title="$product->name">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-indigo-500">Home</a>
            <span class="mx-2">›</span>
            <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-indigo-500">{{ $product->category->name }}</a>
            <span class="mx-2">›</span>
            <span class="text-slate-800 font-medium">{{ $product->dimension }}</span>
        </nav>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-800">{{ $product->name }}</h1>
            <a href="{{ route('category.show', $product->category->slug) }}" class="text-sm text-indigo-800 hover:text-indigo-500 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Terug
            </a>
        </div>

        <p class="text-slate-500 mb-6">Selecteer wanddikte en kwaliteit</p>

        @php
            $hasWallThickness = $product->variants->where('wall_thickness', '>', 0)->isNotEmpty();
        @endphp

        @if($hasWallThickness)
        {{-- Group variants by wall_thickness --}}
        @php
            $grouped = $product->variants->groupBy(fn($v) => number_format($v->wall_thickness, 1));
        @endphp

        <div class="space-y-4">
            @foreach($grouped as $thickness => $variants)
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-700 text-lg">
                        Wanddikte {{ str_replace('.', ',', rtrim(rtrim($thickness, '0'), '.')) }} mm
                    </h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($variants as $variant)
                        <a href="{{ route('variant.show', $variant) }}"
                           class="flex items-center justify-between p-4 rounded-lg border-2 border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 transition-all group">
                            <div>
                                <span class="font-semibold text-indigo-800 group-hover:text-indigo-500 transition-colors">
                                    {{ $variant->quality }}
                                </span>
                                <span class="block text-xs text-slate-400 mt-0.5">
                                    {{ $variant->stockItems->where('quantity', '>', 0)->sum('quantity') }} stuks op voorraad
                                </span>
                            </div>
                            <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- Products without wall thickness (Vierkant, As, Plaat) - go directly to variant --}}
        <div class="space-y-3">
            @foreach($product->variants as $variant)
            <a href="{{ route('variant.show', $variant) }}"
               class="flex items-center justify-between p-5 bg-white rounded-xl shadow-md hover:shadow-lg hover:scale-[1.01] transition-all group">
                <div>
                    <span class="font-semibold text-indigo-800 group-hover:text-indigo-500 transition-colors text-lg">
                        {{ $variant->quality }}
                    </span>
                    <span class="block text-sm text-slate-400 mt-0.5">
                        {{ $variant->stockItems->where('quantity', '>', 0)->sum('quantity') }} stuks op voorraad
                    </span>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</x-layouts.public>
