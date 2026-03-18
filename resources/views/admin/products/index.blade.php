<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Producten
            </h2>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="toggleAll()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" x-data x-text="document.querySelectorAll('[data-category][open]').length > 0 ? 'Alles inklappen' : 'Alles uitklappen'">
                    Alles uitklappen
                </button>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Nieuw product
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="space-y-4">
                @forelse ($categories as $category)
                    <details data-category class="bg-white overflow-hidden shadow-sm sm:rounded-lg group">
                        <summary class="px-6 py-4 bg-gray-50 border-b border-gray-200 cursor-pointer list-none flex items-center justify-between hover:bg-gray-100 transition-colors">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $category->name }}
                                <span class="text-sm font-normal text-gray-500">({{ $category->products->count() }})</span>
                            </h3>
                            <svg class="w-5 h-5 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Naam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Afmeting</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Varianten</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($category->products as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->dimension ?: '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->variants->count() }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Bewerken</a>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Verwijderen</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Geen producten in deze categorie.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </details>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-gray-500">Geen categorieën gevonden. <a href="{{ route('admin.categories.create') }}" class="text-indigo-600 hover:text-indigo-900">Maak eerst een categorie aan.</a></p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function toggleAll() {
            const details = document.querySelectorAll('[data-category]');
            const anyOpen = Array.from(details).some(d => d.open);
            details.forEach(d => d.open = !anyOpen);
        }
    </script>
</x-app-layout>
