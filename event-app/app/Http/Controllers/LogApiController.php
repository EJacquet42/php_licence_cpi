<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogApiController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string',
            'facility' => 'required|string',
            'priority' => 'required|string',
            'hostname' => 'nullable|string',
            'timestamp' => 'nullable|string',
            'type' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'score' => 'nullable|integer',
            'total' => 'nullable|integer',
            'questions_data' => 'nullable|json',
        ]);

        Log::create([
            'user_id' => $data['user_id'] ?? null,
            'type' => $data['type'] ?? 'system',
            'facility' => $data['facility'],
            'priority' => $data['priority'],
            'message' => $data['message'],
            'score' => $data['score'] ?? null,
            'total' => $data['total'] ?? null,
            'questions_data' => isset($data['questions_data']) ? json_decode($data['questions_data'], true) : null,
        ]);

        return response()->json(['success' => true]);
    }
}
