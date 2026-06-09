<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Services\RsyslogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function submit(Request $request, RsyslogService $rsyslog)
    {
        $data = $request->validate([
            'questions' => 'required|array',
            'questions.*.index' => 'required|integer',
            'questions.*.selected' => 'nullable|integer',
        ]);

        $questions = config('quiz.questions');
        $total = count($questions);
        $score = 0;
        $results = [];

        foreach ($data['questions'] as $submission) {
            $index = $submission['index'];
            $question = $questions[$index];
            $selected = $submission['selected'] ?? null;
            $correct = $selected !== null && (int)$selected === $question['correct'];

            if ($correct) {
                $score++;
            }

            $results[] = [
                'question' => $question['question'],
                'selected' => $selected !== null ? $question['answers'][$selected] : null,
                'correct_answer' => $question['answers'][$question['correct']],
                'is_correct' => $correct,
            ];
        }

        $priority = $score === $total ? 'info' : 'warning';
        $message = "Quiz soumis (user #{$request->user()->id}): score {$score}/{$total}";

        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => 'quiz',
            'facility' => 'user',
            'priority' => $priority,
            'message' => $message,
            'questions_data' => $results,
            'score' => $score,
            'total' => $total,
        ]);

        $rsyslog->send($log);

        return response()->json([
            'success' => true,
            'score' => $score,
            'total' => $total,
        ]);
    }
}
