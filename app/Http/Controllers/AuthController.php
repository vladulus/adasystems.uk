<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        // If already logged in, redirect to HUB (2 carduri)
        if (Auth::check()) {
            return redirect()->route('hub');
        }

        return view('login');
    }

    /**
     * Handle login authentication
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check status - prioritate status > is_active
            if ($user->status !== 'active' || !$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = match ($user->status) {
                    'inactive' => 'Your account has been deactivated. Please contact support.',
                    'pending'  => 'Your account is pending approval.',
                    default    => 'Your account is not active.',
                };

                return back()
                    ->withErrors(['email' => $message])
                    ->onlyInput('email');
            }

            // Update last login
            $user->update([
                'last_login'    => now(),
                'last_login_at' => now(),
            ]);

            // DUPĂ login OK => mergem în HUB (pagina cu 2 carduri)
            return redirect()->intended(route('hub'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show password reset request form
     */
    public function showPasswordRequest()
    {
        return view('passwords.email');
    }
}
