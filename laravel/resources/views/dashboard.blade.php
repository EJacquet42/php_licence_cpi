@php
    $questions = config('quiz.questions');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Questionnaire : fonctionnement des logs avec rsyslog
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-10 px-4">
        <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-6 border border-gray-200 dark:border-gray-800">
            <p class="text-gray-600 dark:text-gray-300 mb-8">
                Réponds aux questions ci-dessous, puis clique sur le bouton de correction pour afficher ton score.
            </p>

            <form id="rsyslogQuiz" class="space-y-8">
                @csrf
                @foreach ($questions as $index => $question)
                    <div class="question-block p-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        <h2 class="font-semibold text-lg text-gray-900 dark:text-white mb-4">
                            {{ $index + 1 }}. {{ $question['question'] }}
                        </h2>

                        <div class="space-y-3">
                            @foreach ($question['answers'] as $answerIndex => $answer)
                                <label class="flex items-start gap-3 cursor-pointer text-gray-700 dark:text-gray-200">
                                    <input
                                        type="radio"
                                        name="question_{{ $index }}"
                                        value="{{ $answerIndex }}"
                                        class="mt-1"
                                    >
                                    <span>{{ $answer }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="correction hidden mt-4 p-4 rounded-lg text-sm"></div>
                    </div>
                @endforeach

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <button
                        type="button"
                        onclick="correctQuiz()"
                        class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition"
                    >
                        Corriger le questionnaire
                    </button>

                    <button
                        type="button"
                        onclick="resetQuiz()"
                        class="px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold transition"
                    >
                        Réinitialiser
                    </button>

                    <button
                        type="button"
                        id="submitBtn"
                        onclick="submitQuiz()"
                        class="px-6 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold transition hidden"
                    >
                        Envoyer les logs
                    </button>
                </div>
            </form>

            <div id="quizResult" class="hidden mt-8 p-5 rounded-xl font-semibold text-lg"></div>
        </div>
    </div>

    <script>
        const questions = @json($questions);
        let lastScore = null;
        let lastTotal = null;

        function correctQuiz() {
            let score = 0;
            const total = questions.length;

            questions.forEach((question, index) => {
                const selected = document.querySelector(`input[name="question_${index}"]:checked`);
                const correction = document.querySelectorAll('.correction')[index];

                correction.classList.remove('hidden');

                if (!selected) {
                    correction.className = 'correction mt-4 p-4 rounded-lg text-sm bg-yellow-100 text-yellow-900 border border-yellow-300';
                    correction.innerHTML = `
                        <strong>Non répondu.</strong><br>
                        Bonne réponse : ${question.answers[question.correct]}<br>
                        <em>${question.explanation}</em>
                    `;
                    return;
                }

                const selectedValue = parseInt(selected.value);

                if (selectedValue === question.correct) {
                    score++;
                    correction.className = 'correction mt-4 p-4 rounded-lg text-sm bg-green-100 text-green-900 border border-green-300';
                    correction.innerHTML = `
                        <strong>Bonne réponse.</strong><br>
                        ${question.explanation}
                    `;
                } else {
                    correction.className = 'correction mt-4 p-4 rounded-lg text-sm bg-red-100 text-red-900 border border-red-300';
                    correction.innerHTML = `
                        <strong>Mauvaise réponse.</strong><br>
                        Ta réponse : ${question.answers[selectedValue]}<br>
                        Bonne réponse : ${question.answers[question.correct]}<br>
                        <em>${question.explanation}</em>
                    `;
                }
            });

            const result = document.getElementById('quizResult');
            result.classList.remove('hidden');

            const percentage = Math.round((score / total) * 100);

            let message = '';

            if (percentage >= 80) {
                message = 'Très bon résultat !';
                result.className = 'mt-8 p-5 rounded-xl font-semibold text-lg bg-green-100 text-green-900 border border-green-300';
            } else if (percentage >= 50) {
                message = 'Résultat correct, mais quelques notions sont à revoir.';
                result.className = 'mt-8 p-5 rounded-xl font-semibold text-lg bg-yellow-100 text-yellow-900 border border-yellow-300';
            } else {
                message = 'Il faut revoir le fonctionnement de rsyslog et des logs système.';
                result.className = 'mt-8 p-5 rounded-xl font-semibold text-lg bg-red-100 text-red-900 border border-red-300';
            }

            result.innerHTML = `
                Score : ${score} / ${total} — ${percentage}%<br>
                ${message}
            `;

            result.scrollIntoView({ behavior: 'smooth', block: 'center' });

            lastScore = score;
            lastTotal = total;

            document.getElementById('submitBtn').classList.remove('hidden');
        }

        function resetQuiz() {
            document.getElementById('rsyslogQuiz').reset();

            document.querySelectorAll('.correction').forEach(correction => {
                correction.className = 'correction hidden mt-4 p-4 rounded-lg text-sm';
                correction.innerHTML = '';
            });

            const result = document.getElementById('quizResult');
            result.className = 'hidden mt-8 p-5 rounded-xl font-semibold text-lg';
            result.innerHTML = '';

            document.getElementById('submitBtn').classList.add('hidden');
            lastScore = null;
            lastTotal = null;
        }

        function submitQuiz() {
            const questionsData = [];
            questions.forEach((question, index) => {
                const selected = document.querySelector(`input[name="question_${index}"]:checked`);
                questionsData.push({
                    index: index,
                    selected: selected ? parseInt(selected.value) : null,
                });
            });

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Envoi en cours...';

            fetch('{{ route('dashboard.submit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ questions: questionsData }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.textContent = 'Envoyé ✓';
                    btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    btn.classList.add('bg-gray-400', 'cursor-not-allowed');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.textContent = 'Erreur - Réessayer';
            });
        }
    </script>
</x-app-layout>
