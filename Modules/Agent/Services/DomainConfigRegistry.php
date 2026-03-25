<?php

declare(strict_types=1);

namespace Modules\Agent\Services;

class DomainConfigRegistry
{
    private array $configs;

    public function __construct()
    {
        $this->configs = config('agent.domains', []);
    }

    public function getDomainConfig(string $domain): ?array
    {
        return $this->configs[$domain] ?? null;
    }

    public function getKeywordPatterns(): array
    {
        $patterns = [];

        foreach ($this->configs as $domain => $config) {
            if (isset($config['keywords'])) {
                $patterns[$domain] = $config['keywords'];
            }
        }

        return $patterns;
    }

    public function getAllowedColumns(string $domain, string $table): array
    {
        $config = $this->getDomainConfig($domain);

        if (! $config || ! isset($config['tables'][$table])) {
            return [];
        }

        return $config['tables'][$table]['allowed_columns'] ?? [];
    }

    public function getSearchableColumns(string $domain, string $table): array
    {
        $config = $this->getDomainConfig($domain);

        if (! $config || ! isset($config['tables'][$table])) {
            return [];
        }

        return $config['tables'][$table]['searchable_columns'] ?? [];
    }

    public function getForbiddenColumns(string $domain, string $table): array
    {
        $config = $this->getDomainConfig($domain);

        if (! $config || ! isset($config['tables'][$table])) {
            return [];
        }

        return $config['tables'][$table]['forbidden_columns'] ?? [];
    }

    public function getRequiredScopes(string $domain, string $table): array
    {
        $config = $this->getDomainConfig($domain);

        if (! $config || ! isset($config['tables'][$table])) {
            return [];
        }

        return $config['tables'][$table]['required_scopes'] ?? [];
    }
}
