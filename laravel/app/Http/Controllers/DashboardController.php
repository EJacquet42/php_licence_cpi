<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Services\RsyslogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function submit(Request $request, RsyslogService $rsyslog): JsonResponse
    {
        $data = $request->validate([
            'questions' => 'required|array',
            'questions.*.index' => 'required|integer',
            'questions.*.selected' => 'nullable|integer',
        ]);

        $questions = config('quiz.questions');
        $total = count($questions);
        $score = 0;
        $user = $request->user();
        $userId = $user !== null ? $user->id : null;
        $userIdLabel = $userId !== null ? "id {$userId}" : 'inconnu';
        $results = [];

        foreach ($data['questions'] as $submission) {
            $index = $submission['index'];
            $question = $questions[$index];
            $selected = $submission['selected'] ?? null;
            $correct = $selected !== null && (int)$selected === $question['correct'];

            if ($correct) {
                $score++;
            }

            $result = [
                'question_index' => $index,
                'question' => $question['question'],
                'selected' => $selected !== null ? $question['answers'][$selected] : null,
                'correct_answer' => $question['answers'][$question['correct']],
                'is_correct' => $correct,
            ];

            $results[] = $result;

            $questionLog = Log::create([
                'user_id' => $userId,
                'type' => 'question',
                'facility' => 'user',
                'priority' => 'info',
                'message' => "Question {$index} — {$userIdLabel}: " . ($correct ? 'Correct' : 'Incorrect'),
                'questions_data' => $result,
                'score' => $correct ? 1 : 0,
                'total' => 1,
            ]);

            $rsyslog->send($questionLog);
        }

        $summaryLog = Log::create([
            'user_id' => $userId,
            'type' => 'quiz',
            'facility' => 'user',
            'priority' => 'info',
            'message' => "Quiz soumis — {$userIdLabel}: score {$score}/{$total}",
            'questions_data' => $results,
            'score' => $score,
            'total' => $total,
        ]);

        $rsyslog->send($summaryLog);

        return response()->json([
            'success' => true,
            'score' => $score,
            'total' => $total,
        ]);
    }
}
