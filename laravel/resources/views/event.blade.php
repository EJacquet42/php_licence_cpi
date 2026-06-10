<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Événements & Logs
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-200">
                <div class="mb-6">
                    <form method="GET" action="{{ route('event') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Tous</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                            <select name="priority" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Toutes</option>
                                @foreach ($priorities as $p)
                                    <option value="{{ $p }}" @selected(request('priority') === $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                                Filtrer
                            </button>
                            <a href="{{ route('event') }}"
                                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium transition">
                                Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>

                <div class="space-y-3">
                    @forelse ($logs as $log)
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold
                                            @if ($log->priority === 'emerg' || $log->priority === 'alert' || $log->priority === 'crit') bg-red-100 text-red-800
                                            @elseif ($log->priority === 'error') bg-orange-100 text-orange-800
                                            @elseif ($log->priority === 'warning') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ $log->priority }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-gray-200 text-gray-700">
                                            {{ $log->facility }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $log->type }}
                                        </span>
                                        @if ($log->user)
                                            <span class="text-xs text-gray-500">{{ $log->user->name }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">système</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-900 text-sm">{{ $log->message }}</p>
                                    @if ($log->type === 'question' && $log->questions_data && isset($log->questions_data['question']))
                                        @php $r = $log->questions_data; @endphp
                                        <div class="mt-2 p-2 rounded text-sm {{ $r['is_correct'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                                            <p class="font-medium">{{ $r['question'] }}</p>
                                            <p>Réponse : {{ $r['selected'] ?? 'Non répondu' }}</p>
                                            <p class="text-xs text-gray-500">Bonne réponse : {{ $r['correct_answer'] }}</p>
                                        </div>
                                    @elseif ($log->questions_data && is_array($log->questions_data) && isset($log->questions_data[0]))
                                        <details class="mt-2">
                                            <summary class="text-xs text-blue-600 cursor-pointer hover:text-blue-800">
                                                Voir le détail des réponses ({{ $log->score }}/{{ $log->total }})
                                            </summary>
                                            <div class="mt-2 space-y-2 text-sm">
                                                @foreach ($log->questions_data as $result)
                                                    <div class="p-2 rounded {{ $result['is_correct'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                                                        <p class="font-medium">{{ $result['question'] }}</p>
                                                        <p>Réponse : {{ $result['selected'] ?? 'Non répondu' }}</p>
                                                        <p class="text-xs text-gray-500">Bonne réponse : {{ $result['correct_answer'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">Aucun log pour le moment.</p>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        setInterval(function () {
            let params = new URLSearchParams(window.location.search);
            params.set('_t', Date.now());
            fetch('{{ route('event') }}?' + params.toString(), {
                headers: { 'Accept': 'text/html' }
            })
            .then(r => r.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newContent = doc.querySelector('.space-y-3');
                let currentContent = document.querySelector('.space-y-3');
                if (newContent && currentContent) {
                    currentContent.innerHTML = newContent.innerHTML;
                }
            })
            .catch(() => {});
        }, 10000);
    </script>
</x-app-layout>
