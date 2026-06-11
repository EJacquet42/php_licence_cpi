<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;

class ImportRsyslogFiles extends Command
{
    protected $signature = 'logs:import-from-files';
    protected $description = 'Import rsyslog log files into the database';

    public function handle(): int
    {
        $basePath = '/var/log/remote';

        if (!is_dir($basePath)) {
            $this->warn("Directory {$basePath} does not exist.");
            return Command::SUCCESS;
        }

        $imported = 0;

        $files = glob("{$basePath}/*/*.log");
        if ($files === false) {
            return Command::SUCCESS;
        }

        foreach ($files as $filepath) {
            $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) {
                continue;
            }

            foreach ($lines as $line) {
                $parsed = $this->parseLine($line);

                $exists = Log::where('message', $parsed['message'])->exists();

                if ($exists) {
                    continue;
                }

                Log::create([
                    'type' => $parsed['type'],
                    'facility' => $parsed['facility'],
                    'priority' => $parsed['priority'],
                    'message' => $parsed['message'],
                    'created_at' => $parsed['timestamp'],
                    'updated_at' => $parsed['timestamp'],
                ]);

                $imported++;
            }
        }

        $this->info("Imported {$imported} log entries.");
        return Command::SUCCESS;
    }

    /** @return array<string, mixed> */
    private function parseLine(string $line): array
    {
        $decoded = json_decode($line, true);
        if (is_array($decoded) && isset($decoded['message'])) {
            return [
                'message' => $decoded['message'],
                'facility' => $decoded['facility'] ?? 'unknown',
                'priority' => $decoded['priority'] ?? 'info',
                'type' => $decoded['type'] ?? 'system',
                'timestamp' => $decoded['timestamp'] ?? now(),
            ];
        }

        if (preg_match('/^<\d+>.*$/', $line)) {
            $parts = explode(' ', $line, 6);
            if (count($parts) >= 6) {
                $pri = (int)trim($parts[0], '<>');
                return [
                    'message' => $parts[5],
                    'facility' => 'syslog',
                    'priority' => self::severityFromPri($pri),
                    'type' => 'system',
                    'timestamp' => $parts[1] . ' ' . $parts[2],
                ];
            }
        }

        if (preg_match('/^(\S+)\s+(\S+)\s+(\S+?)(?:\[\d+\])?:\s+(.*)$/', $line, $m)) {
            return [
                'message' => $m[4],
                'facility' => $m[3],
                'priority' => 'info',
                'type' => 'system',
                'timestamp' => $m[1],
            ];
        }

        return [
            'message' => $line,
            'facility' => 'syslog',
            'priority' => 'info',
            'type' => 'system',
            'timestamp' => now(),
        ];
    }

    private static function severityFromPri(int $pri): string
    {
        $severity = $pri & 0x07;
        return match ($severity) {
            0 => 'emerg',
            1 => 'alert',
            2 => 'crit',
            3 => 'error',
            4 => 'warning',
            5 => 'notice',
            6 => 'info',
            7 => 'debug',
        };
    }
}
