<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('admin.stock.index') }}" class="hover:text-gray-700">Voorraadoverzicht</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold text-xl">{{ $variant->product->name }} - {{ str_replace('.', ',', $variant->wall_thickness) }}mm{{ $variant->quality ? ' / ' . $variant->quality : '' }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4">
                    <ul class="list-disc list-inside text-sm text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Variant details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Variantgegevens</h3>
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-gray-500 text-xs uppercase tracking-wide">Categorie</dt>
                        <dd class="font-semibold text-gray-900 mt-1">{{ $variant->product->category->name }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-gray-500 text-xs uppercase tracking-wide">Product</dt>
                        <dd class="font-semibold text-gray-900 mt-1">{{ $variant->product->name }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-gray-500 text-xs uppercase tracking-wide">Wanddikte</dt>
                        <dd class="font-semibold text-gray-900 mt-1">{{ str_replace('.', ',', $variant->wall_thickness) }} mm</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-gray-500 text-xs uppercase tracking-wide">Kwaliteit</dt>
                        <dd class="font-semibold text-gray-900 mt-1">{{ $variant->quality ?: '-' }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <dt class="text-gray-500 text-xs uppercase tracking-wide">Vak</dt>
                        <dd class="font-semibold text-gray-900 mt-1">{{ $variant->drawer ? 'Vak ' . $variant->drawer : '-' }}</dd>
                    </div>
                </dl>

                {{-- Low stock threshold --}}
                <form method="POST" action="{{ route('admin.stock.settings', $variant) }}" class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap items-end gap-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="drawer" class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Vak</label>
                        <select name="drawer" id="drawer" class="w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-</option>
                            @for($i = 1; $i <= 20; $i++)
                                <option value="{{ $i }}" {{ $variant->drawer == $i ? 'selected' : '' }}>Vak {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="low_stock_threshold" class="block text-xs text-gray-500 uppercase tracking-wide mb-1">Min. voorraad</label>
                        <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ $variant->low_stock_threshold }}" min="0"
                               class="w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition">
                        Opslaan
                    </button>
                </form>
            </div>

            <!-- Current stock items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Huidige voorraad</h3>

                @if ($variant->stockItems->count() > 0)
                    <div class="space-y-3">
                        @foreach ($variant->stockItems->sortByDesc('length_mm') as $item)
                            <form method="POST" action="{{ route('admin.stock.update', $variant) }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="correct">
                                <input type="hidden" name="stock_item_id" value="{{ $item->id }}">
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-500 mb-1">Lengte (mm)</label>
                                    <input type="number" name="length_mm" value="{{ $item->length_mm }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" min="1" />
                                </div>
                                <div class="w-24">
                                    <label class="block text-xs text-gray-500 mb-1">Aantal</label>
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" min="0" />
                                </div>
                                <div class="flex gap-2 pt-5">
                                    <button type="submit" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                        Opslaan
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between text-sm text-gray-500">
                        <span>Totaal: <strong class="text-gray-900">{{ $variant->stockItems->sum('quantity') }} stuks</strong></span>
                        <span>Totale lengte: <strong class="text-gray-900">{{ number_format($variant->stockItems->sum(fn($i) => $i->length_mm * $i->quantity) / 1000, 2, ',', '.') }} m</strong></span>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Geen voorraadregels gevonden.</p>
                @endif
            </div>

            <!-- Add new stock item -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Voorraadregel toevoegen</h3>

                <form method="POST" action="{{ route('admin.stock.update', $variant) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="add" />
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="new_length_mm" class="block text-sm font-medium text-gray-700 mb-1">Lengte (mm)</label>
                            <input type="number" name="length_mm" id="new_length_mm" placeholder="bijv. 6000" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" min="1" required />
                        </div>
                        <div>
                            <label for="new_quantity" class="block text-sm font-medium text-gray-700 mb-1">Aantal</label>
                            <input type="number" name="quantity" id="new_quantity" placeholder="1" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                        </div>
                        <div>
                            <label for="new_note" class="block text-sm font-medium text-gray-700 mb-1">Notitie</label>
                            <input type="text" name="note" id="new_note" placeholder="Optioneel" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Toevoegen
                    </button>
                </form>
            </div>

            <!-- Bulk re-inventory -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Volledige herinventarisatie</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Voer de volledige voorraad in. Dit vervangt alle huidige voorraadregels voor deze variant.<br>
                    Formaat: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">lengte_mm x aantal</code> per regel, bijv.:
                </p>
                <pre class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 mb-4 font-mono">6000 x 10
3000 x 5
1500 x 20</pre>

                <form method="POST" action="{{ route('admin.stock.inventory', $variant) }}" onsubmit="return confirm('Let op: dit vervangt de volledige voorraad voor deze variant. Weet je het zeker?')">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="inventory_data" class="block text-sm font-medium text-gray-700 mb-1">Voorraadgegevens</label>
                            <textarea name="inventory_data" id="inventory_data" rows="6" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono" placeholder="6000 x 10&#10;3000 x 5">{{ old('inventory_data') }}</textarea>
                        </div>
                        <div>
                            <label for="inventory_note" class="block text-sm font-medium text-gray-700 mb-1">Notitie</label>
                            <input type="text" name="note" id="inventory_note" placeholder="bijv. Jaarlijkse inventarisatie 2026" class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 transition">
                            Herinventarisatie uitvoeren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
