<?php

use App\Models\Log;

it('creates a log via the API', function () {
    $response = $this->postJson('/api/logs', [
        'message' => 'Test log message',
        'facility' => 'user',
        'priority' => 'info',
        'type' => 'system',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('logs', [
        'message' => 'Test log message',
        'facility' => 'user',
        'priority' => 'info',
        'type' => 'system',
    ]);
});

it('creates a log with default type when not provided', function () {
    $response = $this->postJson('/api/logs', [
        'message' => 'Default type test',
        'facility' => 'daemon',
        'priority' => 'warning',
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('logs', [
        'message' => 'Default type test',
        'type' => 'system',
    ]);
});

it('validates required fields for log creation', function () {
    $response = $this->postJson('/api/logs', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['message', 'facility', 'priority']);
});

it('accepts optional user_id, score, total, and questions_data', function () {
    $response = $this->postJson('/api/logs', [
        'message' => 'With quiz data',
        'facility' => 'user',
        'priority' => 'info',
        'type' => 'quiz',
        'user_id' => 1,
        'score' => 15,
        'total' => 20,
        'questions_data' => json_encode([['question_index' => 0, 'is_correct' => true]]),
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('logs', [
        'message' => 'With quiz data',
        'type' => 'quiz',
        'score' => 15,
        'total' => 20,
    ]);
});

it('validates questions_data must be valid JSON', function () {
    $response = $this->postJson('/api/logs', [
        'message' => 'Bad JSON',
        'facility' => 'user',
        'priority' => 'info',
        'questions_data' => 'not-valid-json',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['questions_data']);
});
