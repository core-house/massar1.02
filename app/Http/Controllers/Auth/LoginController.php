<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        $this->setDatabaseConnection();

        if (Auth::check()) {
            return $this->redirectAfterLogin();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->setDatabaseConnection();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $this->ensureIsNotRateLimited($request);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();

        Log::info('Login successful', [
            'user_id' => Auth::id(),
            'is_central' => $this->isCentralDomain()
        ]);

        return $this->redirectAfterLogin();
    }

    protected function redirectAfterLogin()
    {
        $user = Auth::user();

        // لو على main domain و Admin
        if ($this->isCentralDomain()) {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                // الحل المباشر: redirect مباشر بدل route name
                return redirect('/tenancies');
            }

            Auth::logout();
            abort(403, 'Admins only on main domain');
        }

        // لو على tenant domain
        return redirect('/home'); // أو '/' لو عندك route للصفحة الرئيسية
    }

    protected function setDatabaseConnection(): void
    {
        if ($this->isCentralDomain()) {
            config(['database.default' => 'central']);
            DB::purge('tenant');
            DB::reconnect('central');
        }
    }

    protected function isCentralDomain(): bool
    {
        $host = request()->getHost();
        $hostWithoutPort = explode(':', $host)[0];

        return in_array($host, config('tenancy.central_domains', []))
            || in_array($hostWithoutPort, config('tenancy.central_domains', []));
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => RateLimiter::availableIn($this->throttleKey($request)),
                ]),
            ]);
        }
    }

    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
