<!DOCTYPE html>

<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - نظام مسار</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <script>
        console.log('=== CLIENT DEBUG ===');
        console.log('Current URL:', window.location.href);
        console.log('Current host:', window.location.host);
        console.log('CSRF Token from meta:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        console.log('All cookies:', document.cookie);
        console.log('Session storage:', sessionStorage);
    </script>
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

        .login-section {
            flex: 1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            overflow-y: auto;
            position: relative;
        }

        .login-content {
            width: 100%;
            max-width: 480px;
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
            margin-bottom: 2.5rem;
        }

        .logo-container {
            width: 90px;
            height: 90px;
            margin: 0 auto 1.25rem;
            background: linear-gradient(135deg, #34d3a3 0%, #239d77 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            box-shadow: 0 8px 20px rgba(52, 211, 163, 0.3);
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
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 1.75rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.6rem;
            color: #333;
            font-weight: 600;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 0.95rem 1.2rem;
            color: #000;
            font-size: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: all 0.2s ease;
            background: #f8f9fa;
            box-sizing: border-box;
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
            gap: 0.6rem;
            margin-bottom: 1.75rem;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            cursor: pointer;
            accent-color: #34d3a3;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
            color: #666;
            font-size: 1rem;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #34d3a3 0%, #239d77 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 211, 163, 0.3);
        }

        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(52, 211, 163, 0.4);
            background: linear-gradient(135deg, #2ab88d 0%, #1c8261 100%);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.95rem;
            margin-top: 0.5rem;
            display: block;
            background: rgba(220, 53, 69, 0.1);
            padding: 0.6rem 1rem;
            border-radius: 8px;
            border-right: 4px solid #dc3545;
        }

        .info-section {
            flex: 1;
            background: linear-gradient(135deg, #34d3a3 0%, #1aa1c4 100%);
            background-size: 200% 200%;
            animation: gradient-shift 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: white;
            position: relative;
            overflow: hidden;
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

        .info-content {
            position: relative;
            z-index: 2;
            max-width: 550px;
            animation: slideInLeft 0.6s ease-out;
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
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            line-height: 1.2;
        }

        .info-description {
            font-size: 1.3rem;
            line-height: 1.8;
            margin-bottom: 3rem;
            opacity: 0.95;
        }

        .features-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .feature-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .feature-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .feature-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .feature-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        .feature-item:nth-child(5) {
            animation-delay: 0.5s;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .footer-text {
            position: absolute;
            bottom: 2rem;
            right: 3rem;
            font-size: 0.95rem;
            opacity: 0.8;
            z-index: 2;
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
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 1024px) {
            .login-wrapper {
                flex-direction: column-reverse;
            }

            .info-section,
            .login-section {
                min-height: 50vh;
            }
        }

        @media (max-width: 768px) {
            body {
                overflow: auto;
            }

            .login-wrapper {
                height: auto;
                min-height: 100vh;
            }

            .info-section,
            .login-section {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="info-section">
            <div class="info-content">
                <h1 class="info-title">مرحباً بك في نظام مسار</h1>
                <p class="info-description">
                    نظام إدارة شامل ومتكامل لإدارة جميع عمليات مشروعك بكفاءة وسهولة
                </p>

                <ul class="features-list">
                    <li class="feature-item">
                        <div class="feature-icon">📊</div>
                        <div>إدارة متقدمة للحسابات والمالية</div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon">📦</div>
                        <div>نظام مخزون ذكي ومتطور</div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon">👥</div>
                        <div>إدارة الموارد البشرية والرواتب</div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon">🚀</div>
                        <div>متابعة المشاريع والتقدم اليومي</div>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon">📈</div>
                        <div>تقارير تفصيلية وتحليلات دقيقة</div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="login-section">
            <div class="login-content">
                <div class="logo-section">
                    <div class="logo-container">
                        <img src="{{ asset('assets/images/masarlogo.jpg') }}" alt="Logo">
                    </div>
                    <h2 class="welcome-text">تسجيل الدخول</h2>
                    <p class="welcome-subtitle">أدخل بياناتك للوصول إلى لوحة التحكم</p>
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}" autocomplete="on">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="email">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com"
                            value="{{ old('email') }}" required autocomplete="email" dir="ltr">
                        @error('email')
                            <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">كلمة المرور</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required
                            autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            تذكرني
                        </label>
                    </div>

                    <button type="submit" class="btn-login" id="loginBtn">
                        <span id="loginText">تسجيل الدخول</span>
                        <span id="loadingText" class="loading-text" style="display: none;">
                            جارِ تسجيل الدخول
                            <span class="spinner-border-sm"></span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // لا تضع e.preventDefault() لأنك تريد للفورم أن يُرسل فعلياً
            const btn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loadingText = document.getElementById('loadingText');

            btn.disabled = true;
            loginText.style.display = 'none';
            loadingText.style.display = 'inline'; // استخدم inline أو block
        });
    </script>
</body>

</html>
