<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('admin.categories.index') }}" class="hover:text-gray-700">Categorieën</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold text-xl">{{ isset($category) ? 'Categorie bewerken' : 'Nieuwe categorie' }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-4">
                    <ul class="list-disc list-inside text-sm text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
                    @csrf
                    @if (isset($category))
                        @method('PUT')
                    @endif

                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Naam</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Wordt automatisch gegenereerd" />
                            <p class="mt-1 text-xs text-gray-500">Laat leeg om automatisch te genereren uit de naam.</p>
                        </div>

                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700">Icoon</label>
                            <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon ?? '') }}" class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            <p class="mt-1 text-xs text-gray-500">Optioneel. Emoji of icoon-teken.</p>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700">Sorteervolgorde</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" min="0" />
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Annuleren
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ isset($category) ? 'Bijwerken' : 'Aanmaken' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
