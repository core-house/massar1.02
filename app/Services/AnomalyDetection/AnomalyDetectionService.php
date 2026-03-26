<?php

namespace App\Services\AnomalyDetection;

use App\Models\AnomalyEvent;
use App\Services\AnomalyDetection\Contracts\AnomalyRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AnomalyDetectionService
{
    /**
     * @return array<int, AnomalyEvent>
     */
    public function detectAndStore(Model $model): array
    {
        if (!config('anomaly.enabled')) {
            return [];
        }

        $rules = $this->instantiateRules(config('anomaly.rules', []));
        $events = [];

        DB::transaction(function () use ($model, $rules, &$events) {
            foreach ($rules as $rule) {
                if (!$rule->supports($model)) {
                    continue;
                }

                foreach ($rule->evaluate($model) as $result) {
                    $severity = (string) Arr::get($result, 'severity', 'warning');
                    $title = (string) Arr::get($result, 'title', $rule->code());
                    $description = Arr::get($result, 'description');
                    $meta = Arr::get($result, 'meta', []);

                    $fingerprint = $this->fingerprint($model, $rule->code(), $meta);

                    $created = AnomalyEvent::query()->firstOrCreate(
                        ['fingerprint' => $fingerprint],
                        [
                            'subject_type' => $model->getMorphClass(),
                            'subject_id' => $model->getKey(),
                            'rule_code' => $rule->code(),
                            'severity' => $severity,
                            'title' => $title,
                            'description' => $description,
                            'meta' => $meta,
                            'detected_at' => now(),
                        ]
                    );

                    $events[] = $created;
                }
            }
        });

        return $events;
    }

    /**
     * @param array<int, class-string> $ruleClasses
     * @return array<int, AnomalyRule>
     */
    private function instantiateRules(array $ruleClasses): array
    {
        $rules = [];
        foreach ($ruleClasses as $class) {
            if (is_string($class) && class_exists($class)) {
                $instance = app($class);
                if ($instance instanceof AnomalyRule) {
                    $rules[] = $instance;
                }
            }
        }

        return $rules;
    }

    /**
     * @param array<string, mixed> $meta
     */
    private function fingerprint(Model $model, string $ruleCode, array $meta): string
    {
        $payload = [
            'subject_type' => $model->getMorphClass(),
            'subject_id' => $model->getKey(),
            'rule' => $ruleCode,
            'meta' => $this->normalizeMeta($meta),
        ];

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Ensure stable hashing: sort keys recursively.
     *
     * @param array<string, mixed> $meta
     * @return array<string, mixed>
     */
    private function normalizeMeta(array $meta): array
    {
        ksort($meta);
        foreach ($meta as $k => $v) {
            if (is_array($v)) {
                $meta[$k] = $this->normalizeMeta($v);
            }
        }
        return $meta;
    }
}

