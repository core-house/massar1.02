<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class DatabaseFreshSafeCommand extends Command
{
    protected $signature = 'db:fresh-safe
                            {--tables=* : جداول يُستثنى أخذ نسخة منها (مفصولة بفواصل)}
                            {--seed : تشغيل Seeders بعد الاستعادة}
                            {--compress : ضغط ملف النسخة الاحتياطية}
                            {--chunk-size=1000 : حجم الدفعة للجداول الكبيرة}
                            {--skip-large : تخطي الجداول الكبيرة (أكثر من 10000 صف)}';

    protected $description = 'عمل migrate:fresh مع الاحتفاظ ببيانات جداول معيّنة (محسّن للبيانات الكبيرة)';

    private array $excludedTables = ['migrations', 'failed_jobs', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks'];

    private int $chunkSize = 1000;

    private bool $compress = false;

    private bool $skipLarge = false;

    public function handle(): int
    {
        $this->excludedTables = array_merge($this->excludedTables, $this->option('tables'));
        $this->chunkSize = (int) $this->option('chunk-size');
        $this->compress = $this->option('compress');
        $this->skipLarge = $this->option('skip-large');

        $this->info('🔄 بدء عملية Fresh Safe للقاعدة...');
        $this->newLine();

        try {
            // 1. التحقق من الاتصال
            $this->info('📡 التحقق من اتصال قاعدة البيانات...');
            DB::connection()->getPdo();
            $this->info('✅ الاتصال ناجح');
            $this->newLine();

            // 2. أخذ نسخة احتياطية
            $backupPath = $this->createBackup();
            if (! $backupPath) {
                $this->error('❌ فشل في إنشاء النسخة الاحتياطية');

                return Command::FAILURE;
            }

            // 3. migrate:fresh
            $this->info('🗑️  تنفيذ migrate:fresh...');
            $this->call('migrate:fresh', ['--force' => true]);
            $this->info('✅ تم تنفيذ migrate:fresh بنجاح');
            $this->newLine();

            // 4. استعادة البيانات
            $this->info('📥 استعادة البيانات من النسخة الاحتياطية...');
            $this->restoreData($backupPath);
            $this->info('✅ تم استعادة البيانات بنجاح');
            $this->newLine();

            // 5. Seeders (اختياري)
            if ($this->option('seed')) {
                $this->info('🌱 تشغيل Seeders...');
                $this->call('db:seed', ['--force' => true]);
                $this->info('✅ تم تشغيل Seeders');
            }

            // 6. تنظيف الملفات المؤقتة
            if (File::exists($backupPath)) {
                File::delete($backupPath);
                $this->info('🧹 تم حذف الملفات المؤقتة');
            }

            $this->newLine();
            $this->info('✅ تمت العملية بنجاح!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ حدث خطأ: '.$e->getMessage());
            $this->error('📍 الملف: '.$e->getFile().':'.$e->getLine());

            return Command::FAILURE;
        }
    }

    private function createBackup(): ?string
    {
        $this->info('💾 إنشاء نسخة احتياطية...');

        // محاولة استخدام mysqldump أولاً (الأسرع والأفضل)
        $mysqldumpPath = $this->createBackupWithMysqldump();
        if ($mysqldumpPath) {
            return $mysqldumpPath;
        }

        // Fallback: استخدام Laravel (محسّن للبيانات الكبيرة)
        $this->warn('⚠️  mysqldump غير متاح، استخدام الطريقة البديلة (محسّنة للبيانات الكبيرة)...');

        return $this->createBackupWithLaravel();
    }

    private function createBackupWithMysqldump(): ?string
    {
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);

            $timestamp = now()->format('YmdHis');
            $backupDir = storage_path('app/backups');
            File::ensureDirectoryExists($backupDir);

            $backupFile = $backupDir.'/backup_'.$timestamp.'.sql';
            $compressedFile = $backupFile.'.gz';

            // بناء قائمة الجداول المستثناة
            $excludeTables = implode(' ', array_map(fn ($t) => "--ignore-table={$database}.{$t}", $this->excludedTables));

            // بناء أمر mysqldump (بدون shell redirection ليعمل على Windows و Linux)
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --quick --lock-tables=false --routines --triggers %s %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                $excludeTables,
                escapeshellarg($database)
            );

            $this->info('⏳ جاري النسخ الاحتياطي (قد يستغرق وقتاً مع البيانات الكبيرة)...');

            // تنفيذ mysqldump والحصول على المخرجات
            $process = Process::timeout(3600)->run($command);

            if (! $process->successful()) {
                $this->warn('⚠️  mysqldump فشل: '.$process->errorOutput());

                return null;
            }

            // حفظ المخرجات في الملف
            $output = $process->output();
            File::put($backupFile, $output);

            // ضغط الملف إذا طُلب
            if ($this->compress) {
                $this->info('🗜️  ضغط الملف...');
                $this->compressFile($backupFile, $compressedFile);
                File::delete($backupFile);
                $finalFile = $compressedFile;
            } else {
                $finalFile = $backupFile;
            }

            $size = File::size($finalFile);
            $this->info('✅ تم إنشاء النسخة الاحتياطية: '.$this->formatBytes($size));

            return $finalFile;
        } catch (\Exception $e) {
            $this->warn('⚠️  خطأ في mysqldump: '.$e->getMessage());

            return null;
        }
    }

    private function createBackupWithLaravel(): ?string
    {
        try {
            $timestamp = now()->format('YmdHis');
            $backupDir = storage_path('app/backups');
            File::ensureDirectoryExists($backupDir);

            $backupFile = $backupDir.'/backup_'.$timestamp.'.sql';
            $handle = fopen($backupFile, 'w');

            if (! $handle) {
                throw new \Exception('فشل في فتح ملف النسخة الاحتياطية');
            }

            // Header
            $this->writeSqlHeader($handle);

            // الحصول على قائمة الجداول
            $tables = $this->getTablesToBackup();
            $totalTables = count($tables);

            $this->info("📊 عدد الجداول للنسخ: {$totalTables}");

            $progressBar = $this->output->createProgressBar($totalTables);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
            $progressBar->setMessage('بدء النسخ...');
            $progressBar->start();

            foreach ($tables as $index => $tableName) {
                $progressBar->setMessage("جاري: {$tableName}");

                // التحقق من حجم الجدول
                $rowCount = $this->getTableRowCount($tableName);

                if ($this->skipLarge && $rowCount > 10000) {
                    $progressBar->setMessage("⏭️  تخطي {$tableName} (كبير: {$rowCount} صف)");
                    $progressBar->advance();

                    continue;
                }

                // نسخ البيانات (مع Chunking للجداول الكبيرة)
                $this->backupTableData($handle, $tableName, $rowCount);

                $progressBar->advance();
            }

            $progressBar->setMessage('اكتمل!');
            $progressBar->finish();
            $this->newLine(2);

            // Footer
            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            fwrite($handle, "COMMIT;\n");
            fclose($handle);

            // ضغط الملف إذا طُلب
            if ($this->compress) {
                $compressedFile = $backupFile.'.gz';
                $this->info('🗜️  ضغط الملف...');
                $this->compressFile($backupFile, $compressedFile);
                File::delete($backupFile);
                $backupFile = $compressedFile;
            }

            $size = File::size($backupFile);
            $this->info('✅ تم إنشاء النسخة الاحتياطية: '.$this->formatBytes($size));

            return $backupFile;
        } catch (\Exception $e) {
            $this->error('❌ خطأ في النسخ الاحتياطي: '.$e->getMessage());

            return null;
        }
    }

    private function getTablesToBackup(): array
    {
        $tables = DB::select('SHOW TABLES');
        $database = DB::getDatabaseName();
        $tableKey = 'Tables_in_'.$database;

        return collect($tables)
            ->pluck($tableKey)
            ->reject(fn ($table) => in_array($table, $this->excludedTables))
            ->values()
            ->toArray();
    }

    private function getTableRowCount(string $tableName): int
    {
        try {
            $result = DB::selectOne("SELECT COUNT(*) as count FROM `{$tableName}`");

            return (int) ($result->count ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function backupTableData($handle, string $tableName, int $rowCount): void
    {
        fwrite($handle, "\n-- Table: {$tableName} ({$rowCount} rows)\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");

        if ($rowCount === 0) {
            fwrite($handle, "-- Table is empty\n");

            return;
        }

        // للجداول الصغيرة: نسخ مباشر
        if ($rowCount <= $this->chunkSize) {
            $rows = DB::table($tableName)->get();
            $this->writeTableData($handle, $tableName, $rows);

            return;
        }

        // للجداول الكبيرة: Chunking
        $this->info("  📦 معالجة {$tableName} على دفعات ({$rowCount} صف)...");

        $totalChunks = (int) ceil($rowCount / $this->chunkSize);
        $chunkProgress = $this->output->createProgressBar($totalChunks);
        $chunkProgress->setFormat('    %current%/%max% [%bar%] %percent:3s%%');
        $chunkProgress->start();

        DB::table($tableName)
            ->orderBy('id')
            ->chunkById($this->chunkSize, function ($rows) use ($handle, $tableName, $chunkProgress) {
                $this->writeTableData($handle, $tableName, $rows);
                $chunkProgress->advance();
            });

        $chunkProgress->finish();
        $this->newLine();
    }

    private function writeTableData($handle, string $tableName, $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $columns = array_keys((array) $rows->first());

        foreach ($rows as $row) {
            $values = array_map(function ($value) {
                if (is_null($value)) {
                    return 'NULL';
                }
                if (is_bool($value)) {
                    return $value ? '1' : '0';
                }
                if (is_string($value)) {
                    return "'".addslashes($value)."'";
                }

                return $value;
            }, array_values((array) $row));

            $sql = "INSERT INTO `{$tableName}` (`".
                implode('`, `', $columns).
                '`) VALUES ('.
                implode(', ', $values).
                ");\n";

            fwrite($handle, $sql);
        }
    }

    private function writeSqlHeader($handle): void
    {
        $database = config('database.connections.mysql.database');
        $exportDate = now()->format('Y-m-d H:i:s');

        $header = "-- =============================================\n".
            "-- Database Backup (Fresh Safe)\n".
            "-- =============================================\n".
            "-- Database: {$database}\n".
            "-- Export Date: {$exportDate}\n".
            '-- Laravel Version: '.app()->version()."\n".
            '-- PHP Version: '.PHP_VERSION."\n".
            "-- =============================================\n\n".
            "SET FOREIGN_KEY_CHECKS=0;\n".
            "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n".
            "START TRANSACTION;\n".
            "SET time_zone = '+00:00';\n\n";

        fwrite($handle, $header);
    }

    private function restoreData(string $backupPath): void
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        // إذا كان الملف مضغوطاً، فك الضغط أولاً
        if (str_ends_with($backupPath, '.gz')) {
            $this->info('📦 فك ضغط الملف...');
            $uncompressedPath = str_replace('.gz', '', $backupPath);
            $this->decompressFile($backupPath, $uncompressedPath);
            $backupPath = $uncompressedPath;
        }

        $this->info('⏳ استعادة البيانات (قد يستغرق وقتاً)...');

        // التحقق من وجود الملف
        if (! File::exists($backupPath)) {
            throw new \Exception("ملف النسخة الاحتياطية غير موجود: {$backupPath}");
        }

        // استخدام طريقة موحدة تعمل على Windows و Linux
        // قراءة محتوى الملف وتمريره عبر stdin (يعمل على كلا النظامين)
        $sqlContent = File::get($backupPath);

        // بناء أمر mysql
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database)
        );

        // تنفيذ الأمر مع تمرير محتوى SQL عبر stdin
        $process = Process::timeout(3600)
            ->input($sqlContent)
            ->run($command);

        if (! $process->successful()) {
            $errorOutput = $process->errorOutput();
            $output = $process->output();
            $errorMessage = $errorOutput ?: $output ?: 'خطأ غير معروف';

            // إظهار معلومات إضافية للمساعدة في التشخيص
            $this->error('❌ تفاصيل الخطأ:');
            if ($errorOutput) {
                $this->error('   Error Output: '.$errorOutput);
            }
            if ($output) {
                $this->error('   Output: '.$output);
            }

            throw new \Exception('فشل في استعادة البيانات: '.$errorMessage);
        }

        // حذف الملف غير المضغوط إذا كان مؤقتاً
        if (str_contains($backupPath, 'backup_') && File::exists($backupPath)) {
            File::delete($backupPath);
        }
    }

    private function compressFile(string $source, string $destination): void
    {
        $handle = fopen($source, 'rb');
        $gzHandle = gzopen($destination, 'wb9');

        if (! $handle || ! $gzHandle) {
            throw new \Exception('فشل في فتح الملفات للضغط');
        }

        while (! feof($handle)) {
            $chunk = fread($handle, 8192);
            gzwrite($gzHandle, $chunk);
        }

        fclose($handle);
        gzclose($gzHandle);
    }

    private function decompressFile(string $source, string $destination): void
    {
        $gzHandle = gzopen($source, 'rb');
        $handle = fopen($destination, 'wb');

        if (! $handle || ! $gzHandle) {
            throw new \Exception('فشل في فتح الملفات لفك الضغط');
        }

        while (! gzeof($gzHandle)) {
            $chunk = gzread($gzHandle, 8192);
            fwrite($handle, $chunk);
        }

        gzclose($gzHandle);
        fclose($handle);
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }
}
