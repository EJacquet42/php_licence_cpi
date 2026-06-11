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

it('validates message is a string', function () {
    $response = $this->postJson('/api/logs', [
        'message' => 12345,
        'facility' => 'user',
        'priority' => 'info',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['message']);
});

it('accepts optional hostname and timestamp', function () {
    $response = $this->postJson('/api/logs', [
        'message' => 'With optional fields',
        'facility' => 'auth',
        'priority' => 'crit',
        'hostname' => 'test-server',
        'timestamp' => '2026-06-11T10:00:00+00:00',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('logs', [
        'message' => 'With optional fields',
        'facility' => 'auth',
        'priority' => 'crit',
    ]);
});
