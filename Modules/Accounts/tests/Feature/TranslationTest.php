<?php

declare(strict_types=1);

namespace Modules\Accounts\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Translation tests for the Accounts module.
 *
 * Key convention: __('accounts::file.key')
 *   - 'accounts' = namespace (registered by AccountsServiceProvider)
 *   - 'file'     = PHP filename under lang/{locale}/ (e.g. sidebar, accounts)
 *   - 'key'      = array key inside that file
 */
class TranslationTest extends TestCase
{
    // ─── Unit Tests ───────────────────────────────────────────────────────────

    #[Test]
    public function sidebar_clients_resolves_in_arabic(): void
    {
        app()->setLocale('ar');
        $this->assertSame('العملاء', __('accounts::sidebar.clients'));
    }

    #[Test]
    public function sidebar_clients_resolves_in_english(): void
    {
        app()->setLocale('en');
        $this->assertSame('Clients', __('accounts::sidebar.clients'));
    }

    #[Test]
    public function accounts_list_resolves_in_arabic(): void
    {
        app()->setLocale('ar');
        $result = __('accounts::accounts.accounts_list');
        $this->assertNotSame('accounts::accounts.accounts_list', $result, 'Key was not resolved — returned raw key string');
    }

    #[Test]
    public function accounts_list_resolves_in_english(): void
    {
        app()->setLocale('en');
        $result = __('accounts::accounts.accounts_list');
        $this->assertNotSame('accounts::accounts.accounts_list', $result, 'Key was not resolved — returned raw key string');
    }

    #[Test]
    public function common_save_still_resolves_after_migration(): void
    {
        app()->setLocale('ar');
        $result = __('common.save');
        $this->assertNotSame('common.save', $result, 'common.save regression: key not resolved');
    }

    #[Test]
    public function missing_key_returns_raw_key_without_exception(): void
    {
        app()->setLocale('ar');
        $result = __('accounts::accounts.this_key_does_not_exist_xyz');
        $this->assertSame('accounts::accounts.this_key_does_not_exist_xyz', $result);
    }

    // ─── Property 1: PHP files return valid arrays with snake_case keys ───────
    // Validates: Requirements 1.2, 1.4

    #[Test]
    public function all_php_translation_files_return_valid_arrays_with_snake_case_keys(): void
    {
        $langPath = base_path('Modules/Accounts/lang');
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($langPath));

        $filesChecked = 0;

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $translations = require $file->getPathname();

            $this->assertIsArray($translations, "File {$file->getFilename()} must return an array");
            $this->assertNotEmpty($translations, "File {$file->getFilename()} must not be empty");

            foreach (array_keys($translations) as $key) {
                $this->assertMatchesRegularExpression(
                    '/^[a-z][a-z0-9_]*$/',
                    $key,
                    "Key '{$key}' in {$file->getFilename()} is not snake_case"
                );
            }

            $filesChecked++;
        }

        $this->assertGreaterThan(0, $filesChecked, 'No PHP translation files were found to check');
    }

    // ─── Property 2: No key coverage lost vs original JSON ────────────────────
    // Validates: Requirements 4.1, 4.2, 7.1

    public static function jsonKeyProvider(): array
    {
        // Fixture: snake_case keys migrated from ar.json (excluding common.php and sidebar.php keys)
        return [
            ['accounts_list'],
            ['manage_accounts'],
            ['list'],
            ['add_new_account'],
            ['search_by_account'],
            ['export_excel'],
            ['export_pdf'],
            ['please_select_account_type'],
            ['no_results_for'],
            ['no_data'],
            ['account'],
            ['account_added_with_partner'],
            ['home'],
            ['create_account'],
            ['accounts'],
            ['account_type'],
            ['basic'],
            ['regular_account'],
            ['parent_account'],
            ['default_currency'],
            ['trade_name_zatca'],
            ['trade_name'],
            ['vat_number'],
            ['national_id'],
            ['national_address_zatca'],
            ['national_address'],
            ['client_type'],
            ['select_type'],
            ['credit_limit'],
            ['leave_empty_no_limit'],
            ['edit_account'],
            ['start_balance'],
            ['balance_sheet'],
            ['account_movement'],
            ['stock'],
        ];
    }

    #[DataProvider('jsonKeyProvider')]
    public function test_json_key_is_resolvable_after_migration(string $snakeKey): void
    {
        app()->setLocale('ar');
        $result = __("accounts::accounts.{$snakeKey}");
        $this->assertNotSame(
            "accounts::accounts.{$snakeKey}",
            $result,
            "Key 'accounts::accounts.{$snakeKey}' was not resolved — migration may have missed it"
        );
    }

    // ─── Property 3: No duplication with common.php ───────────────────────────
    // Validates: Requirements 4.4

    #[Test]
    public function accounts_php_does_not_duplicate_common_keys(): void
    {
        $commonKeys = array_keys(require base_path('resources/lang/ar/common.php'));
        $accountsKeys = array_keys(require base_path('Modules/Accounts/lang/ar/accounts.php'));

        $duplicates = array_intersect($commonKeys, $accountsKeys);

        $this->assertEmpty(
            $duplicates,
            'These keys are duplicated in both common.php and accounts.php: ' . implode(', ', $duplicates)
        );
    }

    // ─── Property 4: All accounts.php keys resolve correctly ─────────────────
    // Validates: Requirements 3.1, 3.2, 7.3

    public static function accountsKeyProvider(): array
    {
        $cases = [];
        $langBase = dirname(__DIR__, 2) . '/lang';
        foreach (['ar', 'en'] as $locale) {
            $file = "{$langBase}/{$locale}/accounts.php";
            if (file_exists($file)) {
                foreach (array_keys(require $file) as $key) {
                    $cases["{$locale}:{$key}"] = [$locale, $key];
                }
            }
        }

        return $cases;
    }

    #[DataProvider('accountsKeyProvider')]
    public function test_accounts_key_resolves_correctly(string $locale, string $key): void
    {
        app()->setLocale($locale);
        $result = __("accounts::accounts.{$key}");
        $this->assertNotSame(
            "accounts::accounts.{$key}",
            $result,
            "Key 'accounts::accounts.{$key}' (locale: {$locale}) was not resolved"
        );
    }

    // ─── Property 5: Sidebar keys still resolve (regression guard) ────────────
    // Validates: Requirements 1.6, 7.1

    public static function sidebarKeyProvider(): array
    {
        $cases = [];
        $file = dirname(__DIR__, 2) . '/lang/ar/sidebar.php';
        if (file_exists($file)) {
            foreach (array_keys(require $file) as $key) {
                $cases[$key] = [$key];
            }
        }

        return $cases;
    }

    #[DataProvider('sidebarKeyProvider')]
    public function test_sidebar_key_resolves_correctly(string $key): void
    {
        app()->setLocale('ar');
        $result = __("accounts::sidebar.{$key}");
        $this->assertNotSame(
            "accounts::sidebar.{$key}",
            $result,
            "Sidebar key 'accounts::sidebar.{$key}' was not resolved — regression detected"
        );
    }
}
