<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الحساب غير نشط - Massar</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'IBM Plex Sans Arabic', 'Inter', ui-sans-serif, system-ui, sans-serif;
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .inactive-container {
            max-width: 600px;
            width: 90%;
            background: #ffffff;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .icon-wrapper {
            width: 100px;
            height: 100px;
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            margin: 0 auto 2rem;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1rem;
        }

        .message {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn-support {
            background: linear-gradient(135deg, #34d3a3 0%, #239d77 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-support:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 211, 163, 0.3);
            color: white;
        }

        .btn-logout {
            background: transparent;
            color: #666;
            border: 1px solid #ddd;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #f8f9fa;
            border-color: #ccc;
        }
    </style>
</head>

<body>
    <div class="inactive-container">
        <div class="icon-wrapper">
            <i class="las la-exclamation-triangle"></i>
        </div>
        <h1 class="title">تم إيقاف الحساب مؤقتًا</h1>
        <p class="message">
            تم إيقاف حساب شركتك لأن الاشتراك غير نشط حاليًا أو منتهي الصلاحية.
            <br>
            برجاء التواصل مع الدعم لتجديد الاشتراك واستعادة الوصول للنظام.
        </p>

        <div class="d-flex flex-column align-items-center gap-2">
            <a href="mailto:admin@massar.com" class="btn-support">
                <i class="las la-envelope"></i> تواصل مع الدعم
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="las la-sign-out-alt"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </div>
</body>

</html>
