<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Statistieken -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Categorieën</dt>
                    <dd class="mt-1 text-2xl font-bold text-indigo-600">{{ $totalCategories }}</dd>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Producten</dt>
                    <dd class="mt-1 text-2xl font-bold text-indigo-600">{{ $totalProducts }}</dd>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Varianten</dt>
                    <dd class="mt-1 text-2xl font-bold text-indigo-600">{{ $totalVariants }}</dd>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Voorraadregels</dt>
                    <dd class="mt-1 text-2xl font-bold text-emerald-600">{{ $totalStockItems }}</dd>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Totaal stuks</dt>
                    <dd class="mt-1 text-2xl font-bold text-emerald-600">{{ $totalPieces }}</dd>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Totaal lengte</dt>
                    <dd class="mt-1 text-2xl font-bold text-emerald-600">{{ str_replace('.', ',', $totalLengthM) }} m</dd>
                </div>
            </div>

            <!-- Snelkoppelingen -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Snelkoppelingen</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.stock.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Voorraadoverzicht
                    </a>
                    <a href="{{ route('admin.stock.import') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition">
                        CSV Importeren
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                        Categorieën beheren
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                        Producten beheren
                    </a>
                    <a href="{{ route('admin.mutations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                        Mutatielog
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 transition">
                        Publieke pagina
                    </a>
                </div>
            </div>

            <!-- Lage voorraad waarschuwingen -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="inline-block w-3 h-3 bg-amber-500 rounded-full mr-2"></span>
                    Lage voorraad (minder dan 2 stuks)
                </h3>

                @if ($lowStockVariants->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categorie</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wanddikte</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kwaliteit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stuks</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actie</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($lowStockVariants as $variant)
                                    <tr class="{{ ($variant->stock_items_sum_quantity ?? 0) == 0 ? 'bg-red-50' : 'bg-amber-50' }}">
                                        <td class="px-4 py-3">{{ $variant->product->category->name }}</td>
                                        <td class="px-4 py-3 font-medium">{{ $variant->product->name }}</td>
                                        <td class="px-4 py-3">{{ str_replace('.', ',', $variant->wall_thickness) }} mm</td>
                                        <td class="px-4 py-3">{{ $variant->quality ?: '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ ($variant->stock_items_sum_quantity ?? 0) == 0 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $variant->stock_items_sum_quantity ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.stock.edit', $variant) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                Bewerken
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Geen varianten met lage voorraad.</p>
                @endif
            </div>

            <!-- Recente mutaties -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recente mutaties</h3>
                    <a href="{{ route('admin.mutations.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                        Alle mutaties bekijken
                    </a>
                </div>

                @if ($recentMutations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lengte</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aantal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notitie</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gebruiker</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($recentMutations as $mutation)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $mutation->created_at->format('d-m-Y H:i') }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $typeLabels = [
                                                    'addition' => ['Toevoeging', 'bg-green-100 text-green-800'],
                                                    'removal' => ['Afboeking', 'bg-red-100 text-red-800'],
                                                    'correction' => ['Correctie', 'bg-blue-100 text-blue-800'],
                                                    'inventory' => ['Inventarisatie', 'bg-amber-100 text-amber-800'],
                                                ];
                                                $label = $typeLabels[$mutation->type] ?? [$mutation->type, 'bg-gray-100 text-gray-800'];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $label[1] }}">
                                                {{ $label[0] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-medium">
                                            @if ($mutation->variant)
                                                {{ $mutation->variant->product->name }}
                                                <span class="text-gray-500">{{ str_replace('.', ',', $mutation->variant->wall_thickness) }}mm{{ $mutation->variant->quality ? ' / ' . $mutation->variant->quality : '' }}</span>
                                            @else
                                                <span class="text-gray-400">Verwijderd</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $mutation->length_mm > 0 ? $mutation->length_mm . ' mm' : '-' }}</td>
                                        <td class="px-4 py-3">{{ $mutation->quantity > 0 ? '+' . $mutation->quantity : $mutation->quantity }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $mutation->note ?: '-' }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $mutation->user?->name ?: 'Publiek' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Nog geen mutaties vastgelegd.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
