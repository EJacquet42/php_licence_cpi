<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;

class PurgeOldLogs extends Command
{
    protected $signature = 'logs:purge';
    protected $description = 'Supprime les logs de plus de 6 mois (conformité ANSSI R25)';

    public function handle(): void
    {
        $cutoff = now()->subMonths(6);
        $deleted = Log::where('created_at', '<', $cutoff)->delete();

        $this->info("{$deleted} log(s) supprimé(s) (antérieurs au {$cutoff->format('Y-m-d')}).");
    }
}
