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

            // DUPĂ login OK => mergem direct în HUB (fără intended pentru a evita redirect la API endpoints)
            return redirect()->route('hub');
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

    /**
     * Handle password reset email request
     */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Check if user exists
        $user = \App\Models\User::where('email', $request->email)->first();

        // Always show success message (security: don't reveal if email exists)
        // In production, you would send an actual email here
        
        if ($user) {
            // TODO: Implement actual email sending
            // Password::sendResetLink($request->only('email'));
            
            // For now, log the request
            \Log::info('Password reset requested for: ' . $request->email);
        }

        return back()->with('status', 'If an account exists with that email, you will receive password reset instructions shortly.');
    }
}