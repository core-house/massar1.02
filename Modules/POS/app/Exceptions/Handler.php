<?php

namespace Modules\POS\app\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\View\ViewException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // معالجة أخطاء Phiki Pattern Search
        if (str_contains($e->getMessage(), 'FailedToInitializePatternSearchException') ||
            str_contains($e->getMessage(), 'PatternSearcher') ||
            str_contains($e->getMessage(), 'syntax-highlight')) {

            Log::error('Phiki Pattern Search Error: '.$e->getMessage());

            // إرجاع صفحة خطأ بسيطة بدلاً من صفحة الخطأ المعقدة
            return response()->view('errors.simple', [
                'message' => 'حدث خطأ في البحث. يرجى المحاولة مرة أخرى.',
            ], 500);
        }

        // معالجة أخطاء View التي قد تحتوي على مشاكل regex
        if ($e instanceof ViewException && (
            str_contains($e->getMessage(), 'PatternSearcher') ||
            str_contains($e->getMessage(), 'syntax-highlight') ||
            str_contains($e->getMessage(), 'Phiki')
        )) {
            Log::error('View Pattern Search Error: '.$e->getMessage());

            return response()->view('errors.simple', [
                'message' => 'حدث خطأ في العرض. يرجى تحديث الصفحة.',
            ], 500);
        }

        return parent::render($request, $e);
    }
}
