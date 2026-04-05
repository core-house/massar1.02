<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('portal.client_portal') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    @endif
    <style>
        body { background: #f0f2f5; }
        .login-card { max-width: 420px; margin: 100px auto; }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card card shadow-sm">
        <div class="card-body p-4">
            <h4 class="card-title text-center mb-4">{{ __('portal.client_portal') }}</h4>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('portal.authenticate') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">{{ __('portal.phone') }}</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('portal.password') }}</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('portal.login') }}</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
