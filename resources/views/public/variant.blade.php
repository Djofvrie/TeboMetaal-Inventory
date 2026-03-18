<x-layouts.public :title="$variant->product->name . ' - ' . $variant->displayName()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-indigo-500">Home</a>
            <span class="mx-2">›</span>
            <a href="{{ route('category.show', $variant->product->category->slug) }}" class="hover:text-indigo-500">{{ $variant->product->category->name }}</a>
            <span class="mx-2">›</span>
            <a href="{{ route('product.show', $variant->product) }}" class="hover:text-indigo-500">{{ $variant->product->dimension }}</a>
            <span class="mx-2">›</span>
            <span class="text-slate-800 font-medium">
                @if($variant->wall_thickness > 0){{ str_replace('.', ',', rtrim(rtrim(number_format($variant->wall_thickness, 1), '0'), ',')) }} mm — @endif{{ $variant->quality }}
            </span>
        </nav>

        {{-- Product info --}}
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-slate-800 mb-2">{{ $variant->product->name }}</h1>
            <div class="flex flex-wrap gap-3 text-sm">
                <span class="bg-slate-100 px-3 py-1 rounded-full text-slate-600">Afmeting: <strong>{{ $variant->product->dimension }}</strong></span>
                @if($variant->wall_thickness > 0)
                <span class="bg-slate-100 px-3 py-1 rounded-full text-slate-600">Wanddikte: <strong>{{ str_replace('.', ',', $variant->wall_thickness) }} mm</strong></span>
                @endif
                <span class="bg-slate-100 px-3 py-1 rounded-full text-slate-600">Kwaliteit: <strong>{{ $variant->quality }}</strong></span>
                @if($variant->drawer)
                <span class="bg-indigo-50 px-3 py-1 rounded-full text-indigo-700">Vak: <strong>{{ $variant->drawer }}</strong></span>
                @endif
            </div>
        </div>

        {{-- Stock overview --}}
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Huidige voorraad</h2>

            @php
                $grouped = $variant->stockItems->where('quantity', '>', 0)
                    ->groupBy('length_mm')
                    ->map(fn($items) => $items->sum('quantity'))
                    ->sortKeysDesc();
            @endphp

            @if($grouped->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-slate-600">Lengte</th>
                            <th class="px-4 py-2 text-center font-semibold text-slate-600">Aantal stuks</th>
                            <th class="px-4 py-2 text-right font-semibold text-slate-600">Totaal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grouped as $lengthMm => $qty)
                        <tr class="border-b border-slate-100">
                            <td class="px-4 py-2 text-slate-700">{{ number_format($lengthMm / 1000, 2, ',', '.') }} m</td>
                            <td class="px-4 py-2 text-center text-slate-700">{{ $qty }}x</td>
                            <td class="px-4 py-2 text-right text-slate-700">{{ number_format(($lengthMm * $qty) / 1000, 2, ',', '.') }} m</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="mt-4 pt-4 border-t border-slate-200 flex justify-between items-center">
                <div>
                    <span class="text-slate-500 text-sm">Totaal:</span>
                    <span class="font-bold text-lg text-indigo-800 ml-2">{{ $grouped->sum() }} stuks</span>
                </div>
                <div>
                    <span class="text-slate-500 text-sm">Totale lengte:</span>
                    <span class="font-bold text-lg text-indigo-800 ml-2">
                        {{ number_format($grouped->reduce(fn($carry, $qty, $len) => $carry + $len * $qty, 0) / 1000, 2, ',', '.') }} m
                    </span>
                </div>
            </div>
            @else
            <p class="text-slate-400 italic">Geen voorraad beschikbaar.</p>
            @endif
        </div>

        {{-- Deduction form --}}
        @if($grouped->count() > 0)
        <div class="bg-white rounded-xl shadow-md p-6" x-data="{ mode: 'cut', quantity: 1 }">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Materiaal afboeken</h2>


            {{-- Mode toggle --}}
            <div class="flex gap-2 mb-5">
                <button type="button" @click="mode = 'cut'"
                        :class="mode === 'cut' ? 'bg-indigo-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                    Op maat
                </button>
                <button type="button" @click="mode = 'full'"
                        :class="mode === 'full' ? 'bg-indigo-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                    Heel stuk
                </button>
            </div>

            {{-- Cut mode: specify length --}}
            <form x-show="mode === 'cut'" action="{{ route('variant.deduct', $variant) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="mode" value="cut">
                <div>
                    <label for="source_length" class="block text-sm font-medium text-slate-700 mb-1">Afboeken van stuk</label>
                    <select name="source_length" id="source_length" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg p-3">
                        @foreach($grouped as $lengthMm => $qty)
                        <option value="{{ $lengthMm }}" {{ old('source_length') == $lengthMm ? 'selected' : '' }}>
                            {{ number_format($lengthMm / 1000, 2, ',', '.') }} m — {{ $lengthMm }} mm ({{ $qty }}x beschikbaar)
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="length_mm" class="block text-sm font-medium text-slate-700 mb-1">Gewenste lengte (in mm)</label>
                    <input type="number" name="length_mm" id="length_mm" step="1" min="1" required
                           value="{{ old('length_mm') }}"
                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg p-3"
                           placeholder="Bijv. 1500">
                    @error('length_mm')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full sm:w-auto bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 px-8 rounded-lg transition-colors text-lg shadow-md">
                    Afboeken
                </button>
            </form>

            {{-- Full piece mode --}}
            <form x-show="mode === 'full'" action="{{ route('variant.deduct', $variant) }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="mode" value="full">
                <div>
                    <label for="source_length_full" class="block text-sm font-medium text-slate-700 mb-1">Welk stuk pakken?</label>
                    <select name="source_length" id="source_length_full" required
                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg p-3">
                        @foreach($grouped as $lengthMm => $qty)
                        <option value="{{ $lengthMm }}">
                            {{ number_format($lengthMm / 1000, 2, ',', '.') }} m — {{ $lengthMm }} mm ({{ $qty }}x beschikbaar)
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">Aantal stuks</label>
                    <input type="number" name="quantity" id="quantity" step="1" min="1" required
                           x-model="quantity"
                           value="{{ old('quantity', 1) }}"
                           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-lg p-3"
                           placeholder="1">
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full sm:w-auto bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 px-8 rounded-lg transition-colors text-lg shadow-md">
                    <span x-text="quantity > 1 ? quantity + ' stuks afboeken' : 'Heel stuk afboeken'"></span>
                </button>
            </form>
        </div>
        @endif

        {{-- Back link --}}
        <div class="mt-6">
            <a href="{{ route('product.show', $variant->product) }}"
               class="text-sm text-indigo-800 hover:text-indigo-500 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Terug naar {{ $variant->product->dimension }}
            </a>
        </div>
    </div>
</x-layouts.public>
