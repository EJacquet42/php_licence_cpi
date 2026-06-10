<?php

namespace App\Providers;

use App\Models\Log;
use App\Services\RsyslogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(function (Registered $event): void {
            $this->logAuth('info', "Nouvel utilisateur inscrit", $event->user->id);
        });

        Event::listen(function (Login $event): void {
            $this->logAuth('info', "Connexion réussie", $event->user->id);
        });

        Event::listen(function (Logout $event): void {
            $this->logAuth('info', "Déconnexion", $event->user->id);
        });

        Event::listen(function (Failed $event): void {
            $this->logAuth('notice', "Tentative de connexion échouée", userId: null);
        });

        Event::listen(function (PasswordReset $event): void {
            $this->logAuth('info', "Mot de passe réinitialisé", $event->user->id);
        });
    }

    private function logAuth(string $priority, string $message, ?int $userId = null): void
    {
        $log = Log::create([
            'user_id' => $userId,
            'type' => 'auth',
            'facility' => 'auth',
            'priority' => $priority,
            'message' => $message,
        ]);

        try {
            app(RsyslogService::class)->send($log);
        } catch (\Throwable) {
        }
    }
}
