<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Services\RsyslogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneratorController extends Controller
{
    public function create()
    {
        $facilities = [
            'kern', 'user', 'mail', 'daemon', 'auth', 'syslog',
            'lpr', 'news', 'uucp', 'cron', 'authpriv', 'ftp',
            'local0', 'local1', 'local2', 'local3', 'local4', 'local5', 'local6', 'local7',
        ];

        $priorities = [
            'emerg', 'alert', 'crit', 'error', 'warning', 'notice', 'info', 'debug',
        ];

        return view('generator', compact('facilities', 'priorities'));
    }

    public function store(Request $request, RsyslogService $rsyslog)
    {
        $data = $request->validate([
            'facility' => 'required|string|in:kern,user,mail,daemon,auth,syslog,lpr,news,uucp,cron,authpriv,ftp,local0,local1,local2,local3,local4,local5,local6,local7',
            'priority' => 'required|string|in:emerg,alert,crit,error,warning,notice,info,debug',
            'message' => 'required|string|max:1000',
        ]);

        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => 'manual',
            'facility' => $data['facility'],
            'priority' => $data['priority'],
            'message' => $data['message'],
        ]);

        $rsyslog->send($log);

        return redirect()->route('generator.create')
            ->with('success', 'Log transmis à rsyslog avec succès.');
    }
}
