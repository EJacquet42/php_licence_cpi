<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
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

        $logs = $query->latest()->paginate(50);

        $types = Log::select('type')->distinct()->pluck('type');
        $priorities = Log::select('priority')->distinct()->pluck('priority');

        return view('event', [
            'logs' => $logs,
            'types' => $types,
            'priorities' => $priorities,
        ]);
    }
}
