<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // public function showLoginForm()
    // {
    //     $this->setDatabaseConnection();

    //     if (Auth::check()) {
    //         return $this->redirectAfterLogin();
    //     }

    //     return view('auth.login');
    // }

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


        return $this->redirectAfterLogin();
    }

    protected function redirectAfterLogin()
    {
        $user = Auth::user();

        if ($this->isCentralDomain()) {
            // نستخدم نفس منطق الـ Middleware هنا
            if ($user && $user->email === 'admin@admin.com') {
                return redirect('/tenancies'); // المسار المكتوب في ملف الروابط
            }

            Auth::logout();
            abort(403, 'Admins only on main domain');
        }

        return redirect('/admin/dashboard');
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
