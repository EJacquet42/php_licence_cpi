<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Log;
use App\Services\RsyslogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        }

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->wasChanged('email')) {
            $this->logAccount('info', "Adresse email modifiée — id {$user->getAuthIdentifier()}", $user);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user === null) {
            return Redirect::to('/');
        }

        $this->logAccount('alert', "Compte utilisateur supprimé — id {$user->getAuthIdentifier()}", $user);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function logAccount(string $priority, string $message, mixed $user): void
    {
        $log = Log::create([
            'user_id' => $user->getAuthIdentifier(),
            'type' => 'account',
            'facility' => 'authpriv',
            'priority' => $priority,
            'message' => $message,
        ]);

        try {
            app(RsyslogService::class)->send($log);
        } catch (\Throwable) {
        }
    }
}
