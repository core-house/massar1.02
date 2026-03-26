<?php

namespace App\Services\AnomalyDetection\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AnomalyRule
{
    /**
     * A stable code used for storage/deduping.
     */
    public function code(): string;

    /**
     * Return true if the rule can evaluate the given model instance.
     */
    public function supports(Model $model): bool;

    /**
     * @return array<int, array{
     *   severity: string,
     *   title: string,
     *   description?: string|null,
     *   meta?: array<string, mixed>
     * }>
     */
    public function evaluate(Model $model): array;
}

