<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            CSV Importeren
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <!-- Instructies -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Instructies</h3>
                <div class="text-sm text-gray-700 space-y-3">
                    <p>Upload een CSV-bestand om voorraad in bulk toe te voegen. Het bestand moet de volgende kolommen bevatten in deze volgorde:</p>
                    <ol class="list-decimal list-inside space-y-1 ml-2">
                        <li><strong>categorie</strong> - Naam van de categorie (bijv. Koker)</li>
                        <li><strong>product</strong> - Naam van het product (bijv. Koker 60 x 60)</li>
                        <li><strong>afmeting</strong> - Afmeting van het product (bijv. 60x60)</li>
                        <li><strong>wanddikte</strong> - Wanddikte in mm (bijv. 4)</li>
                        <li><strong>kwaliteit</strong> - Kwaliteit (bijv. S235, mag leeg zijn)</li>
                        <li><strong>lengte_mm</strong> - Lengte in millimeters (bijv. 6000)</li>
                        <li><strong>aantal</strong> - Aantal stuks (bijv. 5)</li>
                    </ol>
                    <p class="text-gray-500">Categorieën, producten en varianten die nog niet bestaan worden automatisch aangemaakt. Bestaande voorraadregels met dezelfde lengte worden samengevoegd.</p>
                </div>
            </div>

            <!-- Voorbeeld CSV -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Voorbeeld CSV</h3>
                <pre class="bg-gray-50 rounded-md p-4 text-sm text-gray-700 overflow-x-auto font-mono">categorie,product,afmeting,wanddikte,kwaliteit,lengte_mm,aantal
Koker,Koker 60 x 60,60x60,4,S235,6000,5
Koker,Koker 60 x 60,60x60,4,S235,3000,3
Koker,Koker 80 x 80,80x80,5,S235,6000,10
Buis,Buis 42.4,42.4,3.2,S235,6000,8
Buis,Buis 42.4,42.4,3.2,,12000,4</pre>
                <div class="mt-3">
                    <a href="data:text/csv;charset=utf-8,categorie%2Cproduct%2Cafmeting%2Cwanddikte%2Ckwaliteit%2Clengte_mm%2Caantal%0AKoker%2CKoker%2060%20x%2060%2C60x60%2C4%2CS235%2C6000%2C5%0AKoker%2CKoker%2060%20x%2060%2C60x60%2C4%2CS235%2C3000%2C3"
                       download="voorbeeld_import.csv"
                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                        Voorbeeld CSV downloaden
                    </a>
                </div>
            </div>

            <!-- Upload formulier -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bestand uploaden</h3>

                <form method="POST" action="{{ route('admin.stock.import.process') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">CSV-bestand</label>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
                        <p class="mt-1 text-xs text-gray-500">Alleen .csv bestanden, maximaal 2 MB.</p>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Importeren
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
