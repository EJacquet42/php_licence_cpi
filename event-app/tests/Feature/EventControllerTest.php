<?php

use App\Models\Log;

beforeEach(function () {
    Log::factory()->create([
        'type' => 'auth',
        'facility' => 'authpriv',
        'priority' => 'info',
        'message' => 'Connexion réussie',
        'score' => null,
        'total' => null,
        'questions_data' => null,
        'created_at' => now()->subDays(1),
    ]);

    Log::factory()->create([
        'type' => 'quiz',
        'facility' => 'user',
        'priority' => 'notice',
        'message' => 'Quiz soumis',
        'score' => 8,
        'total' => 10,
        'questions_data' => [['question_index' => 0, 'is_correct' => true]],
        'created_at' => now(),
    ]);

    Log::factory()->create([
        'type' => 'auth',
        'facility' => 'authpriv',
        'priority' => 'error',
        'message' => 'Tentative de connexion échouée',
        'score' => null,
        'total' => null,
        'questions_data' => null,
        'created_at' => now()->subDays(2),
    ]);
});

it('lists all logs on the event page', function () {
    $response = $this->get('/event');

    $response->assertOk()
        ->assertViewHas('logs');
});

it('filters logs by type', function () {
    $response = $this->get('/event?type=auth');

    $response->assertOk();
    $logs = $response->viewData('logs');
    expect($logs)->toHaveCount(2);

    foreach ($logs as $log) {
        expect($log->type)->toBe('auth');
    }
});

it('filters logs by priority', function () {
    $response = $this->get('/event?priority=error');

    $response->assertOk();
    $logs = $response->viewData('logs');
    expect($logs)->toHaveCount(1);
    expect($logs->first()->priority)->toBe('error');
});

it('filters logs by date_from', function () {
    $response = $this->get('/event?date_from=' . now()->format('Y-m-d'));

    $response->assertOk();
    $logs = $response->viewData('logs');
    expect($logs)->toHaveCount(1);
});

it('filters logs by date_to', function () {
    $response = $this->get('/event?date_to=' . now()->subDay()->format('Y-m-d'));

    $response->assertOk();
    $logs = $response->viewData('logs');
    expect($logs)->toHaveCount(2);
});

it('computes question statistics correctly', function () {
    Log::factory()->create([
        'type' => 'question',
        'facility' => 'user',
        'priority' => 'info',
        'message' => 'Question 0',
        'questions_data' => [
            'question_index' => 0,
            'question' => 'What is rsyslog?',
            'is_correct' => true,
        ],
        'score' => 1,
        'total' => 1,
    ]);

    Log::factory()->create([
        'type' => 'question',
        'facility' => 'user',
        'priority' => 'info',
        'message' => 'Question 0 again',
        'questions_data' => [
            'question_index' => 0,
            'question' => 'What is rsyslog?',
            'is_correct' => false,
        ],
        'score' => 0,
        'total' => 1,
    ]);

    Log::factory()->create([
        'type' => 'question',
        'facility' => 'user',
        'priority' => 'info',
        'message' => 'Question 1',
        'questions_data' => [
            'question_index' => 1,
            'question' => 'What port?',
            'is_correct' => true,
        ],
        'score' => 1,
        'total' => 1,
    ]);

    $response = $this->get('/questions-stats');

    $response->assertOk()
        ->assertViewHasAll(['questions', 'best', 'worst', 'stats']);
});

it('shows correct stats for total_questions_asked', function () {
    Log::query()->where('type', 'question')->delete();

    Log::factory()->create([
        'type' => 'question',
        'questions_data' => ['question_index' => 0, 'is_correct' => true],
    ]);

    $response = $this->get('/questions-stats');
    $stats = $response->viewData('stats');
    expect($stats['total_questions_asked'])->toBe(1);
});

it('returns empty questions stats when no question logs exist', function () {
    Log::where('type', 'question')->delete();

    $response = $this->get('/questions-stats');

    $response->assertOk();
    $questions = $response->viewData('questions');
    expect($questions)->toBe([]);
});

it('includes type and priority distribution in the view', function () {
    $response = $this->get('/event');

    $response->assertOk()
        ->assertViewHasAll([
            'types', 'priorities', 'stats',
            'priorityDistribution', 'typeDistribution',
            'logsPerDay', 'avgQuiz', 'avgScore20',
        ]);
});

it('calculates stats correctly', function () {
    $response = $this->get('/event');
    $stats = $response->viewData('stats');

    expect($stats['total_logs'])->toBe(3)
        ->and($stats['total_quizzes'])->toBe(1)
        ->and($stats['total_connections'])->toBe(1)
        ->and($stats['total_failed_logins'])->toBe(1);
});
