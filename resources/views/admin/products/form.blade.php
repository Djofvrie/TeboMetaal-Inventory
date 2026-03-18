<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('admin.products.index') }}" class="hover:text-gray-700">Producten</a>
            <span>/</span>
            <span class="text-gray-900 font-semibold text-xl">{{ isset($product) ? 'Product bewerken' : 'Nieuw product' }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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
                <form method="POST" action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
                      x-data="{
                          variants: {{ json_encode(
                              isset($product)
                                  ? $product->variants->map(fn($v) => ['id' => $v->id, 'wall_thickness' => $v->wall_thickness, 'quality' => $v->quality, 'low_stock_threshold' => $v->low_stock_threshold])->values()
                                  : old('variants', [['id' => null, 'wall_thickness' => '', 'quality' => '', 'low_stock_threshold' => 0]])
                          ) }}
                      }">
                    @csrf
                    @if (isset($product))
                        @method('PUT')
                    @endif

                    <div class="space-y-6">
                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Categorie</label>
                            <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecteer een categorie</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Naam</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                        </div>

                        <!-- Dimension -->
                        <div>
                            <label for="dimension" class="block text-sm font-medium text-gray-700">Afmeting</label>
                            <input type="text" name="dimension" id="dimension" value="{{ old('dimension', $product->dimension ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                            <p class="mt-1 text-xs text-gray-500">Bijv. 40x40, 60x40, 100x50</p>
                        </div>

                        <!-- Sort order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700">Sorteervolgorde</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" min="0" />
                        </div>

                        <!-- Variants -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-gray-700">Varianten</label>
                                <button type="button" @click="variants.push({ id: null, wall_thickness: '', quality: '', low_stock_threshold: 0 })" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                                    + Variant toevoegen
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(variant, index) in variants" :key="index">
                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-md">
                                        <input type="hidden" :name="'variants[' + index + '][id]'" :value="variant.id" />
                                        <div class="flex-1">
                                            <label :for="'variant_wall_thickness_' + index" class="block text-xs font-medium text-gray-500">Wanddikte (mm)</label>
                                            <input type="text" :name="'variants[' + index + '][wall_thickness]'" :id="'variant_wall_thickness_' + index" x-model="variant.wall_thickness" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="bijv. 2,0" required />
                                        </div>
                                        <div class="flex-1">
                                            <label :for="'variant_quality_' + index" class="block text-xs font-medium text-gray-500">Kwaliteit</label>
                                            <input type="text" :name="'variants[' + index + '][quality]'" :id="'variant_quality_' + index" x-model="variant.quality" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="bijv. S235" />
                                        </div>
                                        <div class="w-24">
                                            <label :for="'variant_threshold_' + index" class="block text-xs font-medium text-gray-500">Min. voorraad</label>
                                            <input type="number" :name="'variants[' + index + '][low_stock_threshold]'" :id="'variant_threshold_' + index" x-model="variant.low_stock_threshold" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="0" />
                                        </div>
                                        <div class="pt-5">
                                            <button type="button" @click="variants.splice(index, 1)" x-show="variants.length > 1" class="text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Annuleren
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ isset($product) ? 'Bijwerken' : 'Aanmaken' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
