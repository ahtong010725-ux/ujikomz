<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Check if user is hard-banned
        if ($user->isHardBanned()) {
            $banMsg = '🚫 Akun kamu telah di-banned. Alasan: ' . ($user->ban_reason ?? 'Tidak ada alasan.');
            if ($user->ban_expires_at) {
                $banMsg .= ' Berlaku sampai: ' . $user->ban_expires_at->format('d-m-Y H:i');
            }
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors(['login' => $banMsg]);
        }

        // Set online status
        $user->update([
            'is_online' => true,
            'last_seen' => now()
        ]);

        $request->session()->regenerate();

        return redirect()->route('home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->update([
                'is_online' => false,
                'last_seen' => now()
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
