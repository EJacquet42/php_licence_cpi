# Réponse — Purge et rétention des logs

## Date
10/06/2026

## Réponse brute

```
## Commande Artisan logs:purge

```php
<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as LogFacade;

class PurgeOldLogs extends Command
{
    protected $signature = 'logs:purge
        {--days=180 : Nombre de jours de conservation}
        {--dry-run : Simuler sans supprimer}';

    protected $description = 'Supprime les logs plus vieux que le seuil défini';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoff = now()->subDays($days);

        $count = Log::where('created_at', '<', $cutoff);

        if ($dryRun) {
            $this->info("{$count->count()} logs à purger (simulation)");
            return Command::SUCCESS;
        }

        $deleted = $count->delete();
        $this->info("{$deleted} logs purgés (rétention : {$days} jours)");

        LogFacade::info('logs.purge', [
            'deleted' => $deleted,
            'retention_days' => $days,
        ]);

        return Command::SUCCESS;
    }
}
```

## Test associé

```php
test('logs_older_than_retention_are_purged', function () {
    Log::factory()->create([
        'created_at' => now()->subDays(200),
    ]);
    Log::factory()->create([
        'created_at' => now()->subDays(10),
    ]);

    $this->artisan('logs:purge --days=180')
        ->assertExitCode(0);

    expect(Log::count())->toBe(1);
});
```
```
