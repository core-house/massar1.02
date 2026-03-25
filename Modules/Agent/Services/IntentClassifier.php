<?php

declare(strict_types=1);

namespace Modules\Agent\Services;

use Modules\Agent\DTOs\ClassificationResult;

class IntentClassifier
{
    public function __construct(
        private DomainConfigRegistry $configRegistry
    ) {}

    /**
     * تصنيف نية السؤال وتحديد الـ domain المناسب
     */
    public function classify(string $questionText): ClassificationResult
    {
        $keywords = $this->detectKeywords($questionText);
        $isMultiIntent = $this->detectMultipleIntents($keywords);
        $domain = $isMultiIntent ? null : $this->mapKeywordsToDomain($keywords);
        $confidence = $this->calculateConfidence($keywords, $domain);

        return new ClassificationResult(
            domain: $domain,
            detectedKeywords: $keywords,
            isMultiIntent: $isMultiIntent,
            confidence: $confidence,
        );
    }

    /**
     * استخراج الكلمات المفتاحية من السؤال باستخدام regex patterns عربية
     */
    private function detectKeywords(string $text): array
    {
        $detectedKeywords = [];
        $patterns = $this->configRegistry->getKeywordPatterns();

        foreach ($patterns as $domain => $keywords) {
            foreach ($keywords as $keyword) {
                // استخدام regex للبحث عن الكلمة المفتاحية مع دعم الحروف العربية
                $pattern = '/\b'.preg_quote($keyword, '/').'\b/u';

                if (preg_match($pattern, $text)) {
                    $detectedKeywords[] = [
                        'domain' => $domain,
                        'keyword' => $keyword,
                    ];
                }
            }
        }

        return $detectedKeywords;
    }

    /**
     * الكشف عن أسئلة متعددة النوايا
     */
    private function detectMultipleIntents(array $keywords): bool
    {
        if (empty($keywords)) {
            return false;
        }

        $domains = array_unique(array_column($keywords, 'domain'));

        return count($domains) > 1;
    }

    /**
     * تحديد الـ domain بناءً على الكلمات المفتاحية
     */
    private function mapKeywordsToDomain(array $keywords): ?string
    {
        if (empty($keywords)) {
            return null;
        }

        // حساب عدد الكلمات المفتاحية لكل domain
        $domainCounts = [];
        foreach ($keywords as $keyword) {
            $domain = $keyword['domain'];
            $domainCounts[$domain] = ($domainCounts[$domain] ?? 0) + 1;
        }

        // إرجاع الـ domain الذي يحتوي على أكبر عدد من الكلمات المفتاحية
        arsort($domainCounts);

        return array_key_first($domainCounts);
    }

    /**
     * حساب مستوى الثقة في التصنيف
     */
    private function calculateConfidence(array $keywords, ?string $domain): float
    {
        if (empty($keywords) || $domain === null) {
            return 0.0;
        }

        // عدد الكلمات المفتاحية للـ domain المحدد
        $domainKeywordCount = 0;
        foreach ($keywords as $keyword) {
            if ($keyword['domain'] === $domain) {
                $domainKeywordCount++;
            }
        }

        // الثقة = (عدد كلمات الـ domain / إجمالي الكلمات المفتاحية)
        $confidence = $domainKeywordCount / count($keywords);

        return round($confidence, 2);
    }
}
