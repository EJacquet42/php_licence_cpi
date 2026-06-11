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
            $this->logAuth('info', "Nouvel utilisateur inscrit — {$event->user->getEmailForPasswordReset()}", $event->user);
        });

        Event::listen(function (Login $event): void {
            $this->logAuth('notice', "Connexion réussie — {$event->user->getEmailForPasswordReset()}", $event->user);
        });

        Event::listen(function (Logout $event): void {
            $this->logAuth('notice', "Déconnexion — {$event->user->getEmailForPasswordReset()}", $event->user);
        });

        Event::listen(function (Failed $event): void {
            $userEmail = $event->user ? $event->user->getEmailForPasswordReset() : ($event->credentials['email'] ?? 'inconnu');
            $this->logAuth('warning', "Tentative de connexion échouée — {$userEmail}");
        });

        Event::listen(function (PasswordReset $event): void {
            $this->logAuth('info', "Mot de passe réinitialisé — {$event->user->getEmailForPasswordReset()}", $event->user);
        });
    }

    private function logAuth(string $priority, string $message, mixed $user = null): void
    {
        $userId = $user ? $user->id : null;
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
