<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\LoginLog;
use App\Models\UserDevice;

class WebAuthController extends Controller
{
    /**
     * Show the login view.
     */
    public function showLogin(Request $request)
    {
        $rememberEmail = $request->cookie('remember_email');
        return view('auth.login', compact('rememberEmail'));
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in '.$seconds.' seconds.',
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            RateLimiter::clear($throttleKey);
            
            $user = Auth::user();
            
            // Check status
            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your account is ' . $user->status . '.',
                ]);
            }

            // Log login
            LoginLog::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
                'login_at' => now(),
            ]);

            // Update last login
            $user->update(['last_login_at' => now()]);

            $response = redirect()->intended('/dashboard');

            if ($remember) {
                $response->withCookie(cookie()->make('remember_email', $user->email, 43200)); // 30 days
            } else {
                $response->withCookie(cookie()->forget('remember_email'));
            }

            return $response;
        }

        RateLimiter::hit($throttleKey);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
