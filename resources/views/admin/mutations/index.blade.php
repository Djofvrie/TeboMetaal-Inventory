<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mutatielog
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('admin.mutations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Datum van</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Datum tot</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Categorie</label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Alle categorieën</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Alle types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    @switch($type)
                                        @case('addition') Toevoeging @break
                                        @case('removal') Afname @break
                                        @case('correction') Correctie @break
                                        @case('inventory') Inventarisatie @break
                                        @default {{ $type }}
                                    @endswitch
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Filteren
                        </button>
                        <a href="{{ route('admin.mutations.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Mutations table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lengte (m)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aantal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notitie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gebruiker</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($mutations as $mutation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $mutation->created_at->format('d-m-Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $mutation->variant->product->name ?? '-' }}
                                        @if ($mutation->variant->product->dimension ?? false)
                                            <span class="text-gray-500">{{ $mutation->variant->product->dimension }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ str_replace('.', ',', $mutation->variant->wall_thickness ?? '') }} mm
                                        @if ($mutation->variant->quality ?? false)
                                            / {{ $mutation->variant->quality }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @switch($mutation->type)
                                            @case('addition')
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Toevoeging</span>
                                                @break
                                            @case('removal')
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Afname</span>
                                                @break
                                            @case('correction')
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">Correctie</span>
                                                @break
                                            @case('inventory')
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">Inventarisatie</span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">{{ $mutation->type }}</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ str_replace('.', ',', $mutation->length_mm / 1000) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $mutation->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ $mutation->note ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $mutation->user->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Geen mutaties gevonden.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($mutations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $mutations->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
