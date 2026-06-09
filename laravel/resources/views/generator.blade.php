<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Générateur manuel de logs
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-200">

                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('generator.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="facility" class="block text-sm font-medium text-gray-700 mb-1">Facility</label>
                        <select id="facility" name="facility" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Sélectionner une facility</option>
                            @foreach ($facilities as $facility)
                                <option value="{{ $facility }}" @selected(old('facility') === $facility)>{{ $facility }}</option>
                            @endforeach
                        </select>
                        @error('facility')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                        <select id="priority" name="priority" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Sélectionner une priorité</option>
                            @foreach ($priorities as $priority)
                                <option value="{{ $priority }}" @selected(old('priority') === $priority)>{{ $priority }}</option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea id="message" name="message" rows="4" required maxlength="1000"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Écrire le message du log...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                            Envoyer le log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
