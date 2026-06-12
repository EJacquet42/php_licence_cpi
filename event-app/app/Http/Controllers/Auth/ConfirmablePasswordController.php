<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Services\RsyslogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $confirmed = Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ]);

        $user = $request->user();
        $log = Log::create([
            'user_id' => $user->getAuthIdentifier(),
            'type' => 'auth',
            'facility' => 'auth',
            'priority' => $confirmed ? 'info' : 'notice',
            'message' => $confirmed
                ? "Mot de passe confirmé pour action sensible — id {$user->getAuthIdentifier()}"
                : "Échec de confirmation du mot de passe — id {$user->getAuthIdentifier()}",
        ]);

        try {
            app(RsyslogService::class)->send($log);
        } catch (\Throwable) {
        }

        if (! $confirmed) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
