<?php

namespace Database\Factories;

use App\Models\Log;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogFactory extends Factory
{
    protected $model = Log::class;

    public function definition(): array
    {
        return [
            'type' => 'system',
            'facility' => $this->faker->randomElement([
                'kern', 'user', 'mail', 'daemon', 'auth', 'syslog',
                'lpr', 'news', 'uucp', 'cron', 'authpriv', 'ftp',
                'local0', 'local1', 'local2', 'local3', 'local4', 'local5', 'local6', 'local7',
            ]),
            'priority' => $this->faker->randomElement([
                'emerg', 'alert', 'crit', 'error', 'warning', 'notice', 'info', 'debug',
            ]),
            'message' => $this->faker->sentence(),
            'questions_data' => null,
            'score' => null,
            'total' => null,
        ];
    }
}
