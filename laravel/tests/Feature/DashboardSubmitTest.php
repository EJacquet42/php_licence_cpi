<?php

use App\Models\Log;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('scores a perfect quiz correctly', function () {
    $questions = config('quiz.questions');
    $answers = [];

    foreach ($questions as $index => $question) {
        $answers[] = [
            'index' => $index,
            'selected' => $question['correct'],
        ];
    }

    $response = $this->actingAs($this->user)->postJson('/dashboard/submit', [
        'questions' => $answers,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'score' => count($questions),
            'total' => count($questions),
        ]);
});

it('scores a failing quiz correctly', function () {
    $questions = config('quiz.questions');
    $answers = [];

    foreach ($questions as $index => $question) {
        $wrongAnswer = ($question['correct'] + 1) % count($question['answers']);
        $answers[] = [
            'index' => $index,
            'selected' => $wrongAnswer,
        ];
    }

    $response = $this->actingAs($this->user)->postJson('/dashboard/submit', [
        'questions' => $answers,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'score' => 0,
            'total' => count($questions),
        ]);
});

it('scores a mixed quiz correctly', function () {
    $questions = config('quiz.questions');
    $answers = [];

    foreach ($questions as $index => $question) {
        $selected = $index % 2 === 0 ? $question['correct'] : ($question['correct'] + 1) % count($question['answers']);
        $answers[] = [
            'index' => $index,
            'selected' => $selected,
        ];
    }

    $response = $this->actingAs($this->user)->postJson('/dashboard/submit', [
        'questions' => $answers,
    ]);

    $expectedScore = intdiv(count($questions), 2);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'score' => $expectedScore,
            'total' => count($questions),
        ]);
});

it('handles unanswered questions as incorrect', function () {
    $questions = config('quiz.questions');
    $answers = [];

    foreach ($questions as $index => $question) {
        $answers[] = [
            'index' => $index,
            'selected' => null,
        ];
    }

    $response = $this->actingAs($this->user)->postJson('/dashboard/submit', [
        'questions' => $answers,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'score' => 0,
            'total' => count($questions),
        ]);
});

it('creates log records for each question and a summary', function () {
    $questions = config('quiz.questions');
    $answers = [];

    foreach ($questions as $index => $question) {
        $answers[] = [
            'index' => $index,
            'selected' => $question['correct'],
        ];
    }

    $this->actingAs($this->user)->postJson('/dashboard/submit', [
        'questions' => $answers,
    ]);

    $questionLogs = Log::where('type', 'question')->get();
    expect($questionLogs)->toHaveCount(count($questions));

    $summaryLog = Log::where('type', 'quiz')->first();
    expect($summaryLog)->not->toBeNull()
        ->and($summaryLog->score)->toBe(count($questions))
        ->and($summaryLog->total)->toBe(count($questions));
});

it('validates the questions field must be an array', function () {
    $this->actingAs($this->user);

    $response = $this->postJson('/dashboard/submit', [
        'questions' => 'not-an-array',
    ]);

    $response->assertSessionHasErrors('questions');
});

it('validates questions.*.index is required', function () {
    $response = $this->actingAs($this->user)->postJson('/dashboard/submit', [
        'questions' => [
            ['selected' => 0],
        ],
    ]);

    $response->assertSessionHasErrors('questions.0.index');
});

it('requires authentication', function () {
    $response = $this->postJson('/dashboard/submit', ['questions' => []]);

    $response->assertRedirect('/login');
});
