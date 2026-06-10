<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Services\RsyslogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $log = Log::create([
            'user_id' => $request->user()->id,
            'type' => 'auth',
            'facility' => 'auth',
            'priority' => 'info',
            'message' => "Mot de passe modifié — {$request->user()->email}",
        ]);

        try {
            app(RsyslogService::class)->send($log);
        } catch (\Throwable) {
        }

        return back()->with('status', 'password-updated');
    }
}
