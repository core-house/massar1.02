<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.login')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    protected $rules = [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ];

    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // التحقق من الدومين والـ user
        $user = Auth::user();
        $host = request()->getHost();
        $isCentralDomain = in_array($host, config('tenancy.central_domains', []));

        // إذا كان admin user وجاي من central domain، redirect للـ admin dashboard
        if ($user && $user->isAdmin() && $isCentralDomain) {
            $this->redirectRoute('admin.dashboard');
        } else {
            // غير ذلك، redirect للـ tenant dashboard العادي
        $this->redirectRoute('admin.dashboard');
    }
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

@push('styles')
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
@endpush

<div>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'IBM Plex Sans Arabic', 'Inter', ui-sans-serif, system-ui, sans-serif;
            overflow: hidden;
            height: 100vh;
        }

        .login-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* العمود الأيسر - نموذج تسجيل الدخول */
        .login-section {
            flex: 0 0 33.333%;
            max-width: 33.333%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow-y: auto;
            position: relative;
        }

        .login-content {
            width: 100%;
            max-width: 380px;
            animation: slideInRight 0.6s ease-out;
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(-30px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo-container {
            width: 70px;
            height: 70px;
            margin: 0 auto 0.75rem;
            background: linear-gradient(135deg, #34d3a3 0%, #239d77 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            box-shadow: 0 6px 15px rgba(52, 211, 163, 0.3);
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(52, 211, 163, 0.4);
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            object-fit: cover;
        }

        .welcome-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.4rem;
        }

        .welcome-subtitle {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            color: #000;
            font-size: 0.95rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: #f8f9fa;
        }

        .form-control:hover {
            border-color: #34d3a3;
            background: #ffffff;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: #34d3a3;
            outline: none;
            box-shadow: 0 0 0 4px rgba(52, 211, 163, 0.1);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
            cursor: pointer;
            accent-color: #34d3a3;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
            color: #666;
            font-size: 0.9rem;
        }

        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #34d3a3 0%, #239d77 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 211, 163, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(52, 211, 163, 0.4);
            background: linear-gradient(135deg, #2ab88d 0%, #1c8261 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.4rem;
            display: block;
            background: rgba(220, 53, 69, 0.1);
            padding: 0.5rem 0.85rem;
            border-radius: 6px;
            border-right: 3px solid #dc3545;
        }

        /* العمود الأيمن - معلومات النظام */
        .info-section {
            flex: 0 0 66.666%;
            max-width: 66.666%;
            background: linear-gradient(135deg, rgba(52, 211, 163, 0.95) 0%, rgba(26, 161, 196, 0.95) 100%);
            background-size: 200% 200%;
            animation: gradient-shift 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        /* Background Image */
        .info-section::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            height: 80%;
            background-image: url('{{ asset('assets/images/login-wallpaper.jpg') }}');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 1;
            z-index: 1;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        @keyframes gradient-shift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        /* خلفية متحركة */
        .info-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
            animation: background-float 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes background-float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -50px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        .info-content {
            position: absolute;
            bottom: 3rem;
            right: 3rem;
            z-index: 3;
            max-width: 450px;
            animation: slideInLeft 0.6s ease-out;
            text-align: right;
        }

        @keyframes slideInLeft {
            0% {
                opacity: 0;
                transform: translateX(30px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .info-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 0;
            text-shadow: 3px 3px 12px rgba(0, 0, 0, 0.5);
            line-height: 1.2;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .footer-text {
            position: absolute;
            bottom: 1.5rem;
            right: 2rem;
            font-size: 0.85rem;
            opacity: 0.8;
            z-index: 2;
        }

        /* Floating shapes */
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .floating-shape:nth-child(1) {
            width: 150px;
            height: 150px;
            top: 10%;
            right: 10%;
            animation: float-1 20s ease-in-out infinite;
        }

        .floating-shape:nth-child(2) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            right: 15%;
            animation: float-2 18s ease-in-out infinite;
            animation-delay: -5s;
        }

        .floating-shape:nth-child(3) {
            width: 80px;
            height: 80px;
            top: 60%;
            right: 5%;
            animation: float-3 22s ease-in-out infinite;
            animation-delay: -10s;
        }

        @keyframes float-1 {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(-30px, 30px);
            }
        }

        @keyframes float-2 {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            50% {
                transform: translate(20px, -20px) scale(1.2);
            }
        }

        @keyframes float-3 {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(-20px, -30px);
            }
        }

        .loading-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */

        /* Large Tablets and Small Laptops (1200px and below) */
        @media (max-width: 1200px) {
            .info-section, .login-section {
                padding: 2.5rem;
            }

            .info-title {
                font-size: 2.75rem;
            }

            .info-description {
                font-size: 1.2rem;
            }

            .feature-item {
                font-size: 1.05rem;
            }
        }

        /* Tablets (1024px and below) */
        @media (max-width: 1024px) {
            .login-wrapper {
                flex-direction: column-reverse;
            }

            .info-section {
                flex: 0 0 auto;
                max-width: 100%;
                min-height: 45vh;
                padding: 2.5rem 2rem;
            }

            .login-section {
                flex: 0 0 auto;
                max-width: 100%;
                min-height: 55vh;
                padding: 2.5rem 2rem;
            }

            .info-title {
                font-size: 2.5rem;
            }

            .info-description {
                font-size: 1.15rem;
                margin-bottom: 2rem;
            }

            .feature-item {
                font-size: 1rem;
                margin-bottom: 1.25rem;
            }

            .feature-icon {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }

            .welcome-text {
                font-size: 1.85rem;
            }

            .login-content {
                max-width: 500px;
            }
        }

        /* Small Tablets (768px and below) */
        @media (max-width: 768px) {
            body {
                overflow: auto;
            }

            .login-wrapper {
                height: auto;
                min-height: 100vh;
            }

            .info-section {
                flex: 0 0 auto;
                max-width: 100%;
                min-height: auto;
                padding: 2rem 1.5rem;
            }

            .login-section {
                flex: 0 0 auto;
                max-width: 100%;
                min-height: auto;
                padding: 2rem 1.5rem;
            }

            .info-title {
                font-size: 2rem;
                margin-bottom: 1rem;
            }

            .info-description {
                font-size: 1rem;
                margin-bottom: 1.5rem;
                line-height: 1.6;
            }

            .features-list {
                margin-bottom: 1rem;
            }

            .feature-item {
                font-size: 0.95rem;
                margin-bottom: 1rem;
                gap: 0.75rem;
            }

            .feature-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .footer-text {
                font-size: 0.85rem;
                bottom: 1rem;
                right: 1.5rem;
                left: 1.5rem;
                text-align: center;
            }

            .logo-container {
                width: 80px;
                height: 80px;
                margin-bottom: 1rem;
            }

            .welcome-text {
                font-size: 1.65rem;
            }

            .welcome-subtitle {
                font-size: 1rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-control {
                padding: 0.85rem 1rem;
                font-size: 1rem;
            }

            .btn-login {
                padding: 0.95rem;
                font-size: 1.05rem;
            }

            .floating-shape {
                display: none;
            }
        }

        /* Mobile Devices (576px and below) */
        @media (max-width: 576px) {
            .login-section {
                padding: 1.5rem 1.25rem;
            }

            .info-section {
                padding: 1.75rem 1.25rem;
            }

            .logo-section {
                margin-bottom: 2rem;
            }

            .logo-container {
                width: 70px;
                height: 70px;
                padding: 12px;
                border-radius: 16px;
            }

            .welcome-text {
                font-size: 1.5rem;
                margin-bottom: 0.4rem;
            }

            .welcome-subtitle {
                font-size: 0.95rem;
            }

            .info-title {
                font-size: 1.75rem;
                margin-bottom: 0.75rem;
            }

            .info-description {
                font-size: 0.95rem;
                margin-bottom: 1.25rem;
            }

            .features-list {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.85rem;
            }

            .feature-item {
                font-size: 0.9rem;
                margin-bottom: 0;
                gap: 0.65rem;
            }

            .feature-icon {
                width: 36px;
                height: 36px;
                font-size: 1.1rem;
                border-radius: 10px;
            }

            .form-label {
                font-size: 0.95rem;
                margin-bottom: 0.5rem;
            }

            .form-control {
                padding: 0.8rem 1rem;
                font-size: 0.95rem;
                border-radius: 8px;
            }

            .form-check {
                gap: 0.5rem;
                margin-bottom: 1.5rem;
            }

            .form-check-input {
                width: 1.1rem;
                height: 1.1rem;
            }

            .form-check-label {
                font-size: 0.95rem;
            }

            .btn-login {
                padding: 0.9rem;
                font-size: 1rem;
                border-radius: 8px;
            }

            .invalid-feedback {
                font-size: 0.9rem;
                padding: 0.5rem 0.85rem;
                border-radius: 6px;
            }

            .footer-text {
                position: relative;
                bottom: auto;
                right: auto;
                left: auto;
                margin-top: 1.5rem;
                font-size: 0.8rem;
            }
        }

        /* Extra Small Mobile (400px and below) */
        @media (max-width: 400px) {
            .login-section, .info-section {
                padding: 1.25rem 1rem;
            }

            .logo-container {
                width: 60px;
                height: 60px;
                padding: 10px;
            }

            .welcome-text {
                font-size: 1.35rem;
            }

            .info-title {
                font-size: 1.5rem;
            }

            .info-description {
                font-size: 0.9rem;
            }

            .form-control {
                padding: 0.75rem 0.9rem;
                font-size: 0.9rem;
            }

            .btn-login {
                padding: 0.85rem;
                font-size: 0.95rem;
            }
        }

        /* Landscape Mode for Mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .login-wrapper {
                flex-direction: row;
            }

            .info-section {
                flex: 0 0 60%;
                max-width: 60%;
                min-height: 100vh;
                padding: 2rem 1.5rem;
            }

            .login-section {
                flex: 0 0 40%;
                max-width: 40%;
                min-height: 100vh;
            }

            .features-list {
                display: block;
            }

            .info-title {
                font-size: 1.75rem;
                margin-bottom: 0.75rem;
            }

            .info-description {
                font-size: 0.9rem;
                margin-bottom: 1.25rem;
            }

            .feature-item {
                font-size: 0.85rem;
                margin-bottom: 0.75rem;
            }

            .feature-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .footer-text {
                font-size: 0.75rem;
                bottom: 0.75rem;
            }

            .logo-section {
                margin-bottom: 1.5rem;
            }

            .logo-container {
                width: 60px;
                height: 60px;
                margin-bottom: 0.75rem;
            }

            .welcome-text {
                font-size: 1.35rem;
            }

            .form-group {
                margin-bottom: 1.25rem;
            }
        }
    </style>

    <div class="login-wrapper">
        <!-- العمود الأيمن - المعلومات -->
        <div class="info-section">
            <div class="floating-shape"></div>
            <div class="floating-shape"></div>
            <div class="floating-shape"></div>

            <div class="info-content">
                <h1 class="info-title">Your Business in One App</h1>
            </div>

            <div class="footer-text">
                نظام مسار © {{ date('Y') }}
            </div>
        </div>

        <!-- العمود الأيسر - تسجيل الدخول -->
        <div class="login-section">
            <div class="login-content">
                <div class="logo-section">
                    <div class="logo-container">
                        <img src="{{ asset('assets/images/masarlogo.jpg') }}" alt="Logo">
                    </div>
                    <h2 class="welcome-text">تسجيل الدخول</h2>
                    <p class="welcome-subtitle">أدخل بياناتك للوصول إلى لوحة التحكم</p>
                </div>

                <form wire:submit.prevent="login" autocomplete="on">
                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">البريد الإلكتروني</label>
                        <input
                            type="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="email@example.com"
                            required
                            wire:model="email"
                            autocomplete="email"
                            dir="ltr">
                        @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">كلمة المرور</label>
                        <input
                            type="password"
                            id="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                            wire:model="password"
                            autocomplete="current-password">
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="remember"
                            wire:model="remember">
                        <label class="form-check-label" for="remember">
                            تذكرني
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="login">
                            تسجيل الدخول
                        </span>
                        <span wire:loading wire:target="login" class="loading-text">
                            جارِ تسجيل الدخول
                            <span class="spinner-border-sm"></span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
