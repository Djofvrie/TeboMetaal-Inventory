<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Voorraadoverzicht
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="space-y-6">
                @forelse ($categories as $category)
                    <div x-data="{ open: true }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-3">
                                @if ($category->icon)
                                    <span class="text-2xl">{{ $category->icon }}</span>
                                @endif
                                <h3 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h3>
                                <span class="text-sm text-gray-500">({{ $category->products->count() }} producten)</span>
                            </div>
                            <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="border-t border-gray-200">
                            @forelse ($category->products as $product)
                                <div class="px-6 py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                    <h4 class="font-medium text-gray-900">
                                        {{ $product->name }}
                                        @if ($product->dimension)
                                            <span class="text-sm text-gray-500">{{ $product->dimension }}</span>
                                        @endif
                                    </h4>

                                    @if ($product->variants->count() > 0)
                                        <div class="mt-2 overflow-x-auto">
                                            <table class="min-w-full text-sm">
                                                <thead>
                                                    <tr class="text-left text-gray-500">
                                                        <th class="pr-4 py-1 font-medium">Wanddikte</th>
                                                        <th class="pr-4 py-1 font-medium">Kwaliteit</th>
                                                        <th class="pr-4 py-1 font-medium">Voorraadregels</th>
                                                        <th class="pr-4 py-1 font-medium">Totaal stuks</th>
                                                        <th class="py-1 font-medium">Acties</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($product->variants as $variant)
                                                        <tr class="border-t border-gray-50">
                                                            <td class="pr-4 py-2">{{ str_replace('.', ',', $variant->wall_thickness) }} mm</td>
                                                            <td class="pr-4 py-2">{{ $variant->quality ?: '-' }}</td>
                                                            <td class="pr-4 py-2">{{ $variant->stockItems->count() }}</td>
                                                            <td class="pr-4 py-2">{{ $variant->stockItems->sum('quantity') }}</td>
                                                            <td class="py-2">
                                                                <a href="{{ route('admin.stock.edit', $variant) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                                    Voorraad bewerken
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="mt-2 text-sm text-gray-500">Geen varianten beschikbaar.</p>
                                    @endif
                                </div>
                            @empty
                                <div class="px-6 py-4 text-sm text-gray-500">
                                    Geen producten in deze categorie.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-gray-500">Geen categorieën gevonden.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
