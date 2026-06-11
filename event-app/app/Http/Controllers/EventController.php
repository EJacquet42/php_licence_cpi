<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Log::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(20);

        $types = Log::select('type')->distinct()->pluck('type');
        $priorities = Log::select('priority')->distinct()->pluck('priority');

        $stats = [
            'total_logs' => Log::count(),
            'total_quizzes' => Log::where('type', 'quiz')->count(),
            'total_connections' => Log::where('type', 'auth')
                ->where('message', 'like', 'Connexion réussie%')->count(),
            'total_failed_logins' => Log::where('type', 'auth')
                ->where('message', 'like', 'Tentative de connexion échouée%')->count(),
        ];

        $priorityDistribution = Log::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')->orderByDesc('count')->get();

        $typeDistribution = Log::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')->orderByDesc('count')->get();

        $logsPerDay = Log::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')->orderBy('date')->get();

        $avgQuiz = Log::where('type', 'quiz')
            ->selectRaw('AVG(score * 100.0 / NULLIF(total, 0)) as avg_percentage')
            ->value('avg_percentage');

        $avgScore20 = Log::where('type', 'quiz')
            ->whereNotNull('score')
            ->avg('score');

        return view('event', [
            'logs' => $logs,
            'types' => $types,
            'priorities' => $priorities,
            'stats' => $stats,
            'priorityDistribution' => $priorityDistribution,
            'typeDistribution' => $typeDistribution,
            'logsPerDay' => $logsPerDay,
            'avgQuiz' => $avgQuiz,
            'avgScore20' => $avgScore20,
        ]);
    }

    public function questionsStats(): View
    {
        $quizLogs = Log::where('type', 'question')
            ->whereNotNull('questions_data')
            ->get();

        $questions = [];

        foreach ($quizLogs as $log) {
            /** @var array<string, mixed>|null $data */
            $data = $log->questions_data;
            if ($data === null || !isset($data['question_index'])) {
                continue;
            }
            $idx = $data['question_index'];
            if (!isset($questions[$idx])) {
                $questions[$idx] = [
                    'question_index' => $idx,
                    'question' => $data['question'] ?? "Question {$idx}",
                    'total_answers' => 0,
                    'correct_count' => 0,
                    'percentage' => 0,
                ];
            }
            $questions[$idx]['total_answers']++;
            if ($data['is_correct'] ?? false) {
                $questions[$idx]['correct_count']++;
            }
        }

        if ($questions !== [] && $quizLogs->isNotEmpty()) {
            foreach ($questions as &$q) {
                $q['percentage'] = round(($q['correct_count'] / $q['total_answers']) * 100, 1);
            }
            unset($q);
        }

        $best = null;
        $worst = null;
        foreach ($questions as $q) {
            if ($best === null || $q['percentage'] > $best['percentage']) {
                $best = $q;
            }
            if ($worst === null || $q['percentage'] < $worst['percentage']) {
                $worst = $q;
            }
        }

        $stats = [
            'total_questions_asked' => $quizLogs->count(),
        ];

        ksort($questions);

        return view('questions-stats', [
            'questions' => $questions,
            'best' => $best,
            'worst' => $worst,
            'stats' => $stats,
        ]);
    }
}
