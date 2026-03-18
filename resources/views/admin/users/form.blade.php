<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($user) ? 'Gebruiker bewerken' : 'Nieuwe gebruiker' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
                    @csrf
                    @if (isset($user))
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Naam</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Wachtwoord
                            @if (isset($user))
                                <span class="text-gray-400 font-normal">(leeg laten om niet te wijzigen)</span>
                            @endif
                        </label>
                        <input type="password" name="password" id="password"
                               {{ isset($user) ? '' : 'required' }}
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Wachtwoord bevestigen</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Annuleren</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ isset($user) ? 'Opslaan' : 'Aanmaken' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
