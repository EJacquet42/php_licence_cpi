<?php

namespace App\Services;

use App\Models\Log;

class RsyslogService
{
    /** @var array<string, int> */
    private array $facilityMap = [
        'kern' => 0, 'user' => 1, 'mail' => 2, 'daemon' => 3,
        'auth' => 4, 'syslog' => 5, 'lpr' => 6, 'news' => 7,
        'uucp' => 8, 'cron' => 9, 'authpriv' => 10, 'ftp' => 11,
        'local0' => 16, 'local1' => 17, 'local2' => 18, 'local3' => 19,
        'local4' => 20, 'local5' => 21, 'local6' => 22, 'local7' => 23,
    ];

    /** @var array<string, int> */
    private array $priorityMap = [
        'emerg' => 0, 'alert' => 1, 'crit' => 2, 'error' => 3,
        'warning' => 4, 'notice' => 5, 'info' => 6, 'debug' => 7,
    ];

    public function formatSyslogMessage(Log $log): string
    {
        $facility = $this->facilityMap[$log->facility] ?? 1;
        $severity = $this->priorityMap[$log->priority] ?? 6;
        $pri = $facility * 8 + $severity;

        return sprintf(
            "<%d>1 %s php laravel - - - %s",
            $pri,
            $log->created_at?->format('Y-m-d\TH:i:s.vP'),
            $log->message
        );
    }

    public function send(Log $log): void
    {
        $message = $this->formatSyslogMessage($log);

        try {
            $socket = @fsockopen('tcp://172.22.0.10', 514, $errno, $errstr, 2);
            if ($socket) {
                fwrite($socket, $message . "\n");
                fclose($socket);
            }
        } catch (\Exception $e) {
            // Fail silently
        }

        try {
            $payload = http_build_query([
                'user_id' => $log->user_id,
                'message' => $log->message,
                'facility' => $log->facility,
                'priority' => $log->priority,
                'type' => $log->type,
                'hostname' => 'laravel',
                'timestamp' => $log->created_at?->toIso8601String(),
                'score' => $log->score,
                'total' => $log->total,
                'questions_data' => $log->questions_data ? json_encode($log->questions_data) : null,
            ]);
            $ctx = stream_context_create(['http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 2,
            ]]);
            @file_get_contents('http://nginx:8081/api/logs', false, $ctx);
        } catch (\Exception $e) {
            // Fail silently
        }
    }
}
