<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string',
            'facility' => 'required|string',
            'priority' => 'required|string',
            'hostname' => 'nullable|string',
            'timestamp' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        Log::create([
            'user_id' => null,
            'type' => $data['type'] ?? 'system',
            'facility' => $data['facility'],
            'priority' => $data['priority'],
            'message' => $data['message'],
        ]);

        return response()->json(['success' => true]);
    }
}
