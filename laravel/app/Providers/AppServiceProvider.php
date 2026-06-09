<?php

namespace App\Providers;

use App\Models\Log;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
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
            Log::create([
                'type' => 'auth',
                'facility' => 'auth',
                'priority' => 'info',
                'message' => "Nouvel utilisateur inscrit : {$event->user->email}",
                'user_id' => $event->user->id,
            ]);
        });

        Event::listen(function (Login $event): void {
            Log::create([
                'type' => 'auth',
                'facility' => 'auth',
                'priority' => 'info',
                'message' => "Connexion utilisateur : {$event->user->email}",
                'user_id' => $event->user->id,
            ]);
        });

        Event::listen(function (Failed $event): void {
            $email = $event->credentials['email'] ?? 'inconnu';
            Log::create([
                'type' => 'auth',
                'facility' => 'auth',
                'priority' => 'notice',
                'message' => "Tentative de connexion échouée : {$email}",
            ]);
        });
    }
}
