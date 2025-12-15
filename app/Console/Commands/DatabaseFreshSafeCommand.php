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
                            {--tables=* : Tables to exclude from backup (comma-separated)}
                            {--seed : Run seeders after restore}
                            {--compress : Compress backup file}
                            {--chunk-size=1000 : Chunk size for large tables}
                            {--skip-large : Skip large tables (more than 10000 rows)}';

    protected $description = 'Run migrate:fresh while preserving data from specified tables (optimized for large data)';

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

        $this->info('🔄 Starting Fresh Safe process for database...');
        $this->newLine();

        try {
            // 1. Check connection
            $this->info('📡 Checking database connection...');
            DB::connection()->getPdo();
            $this->info('✅ Connection successful');
            $this->newLine();

            // 2. Create backup
            $backupPath = $this->createBackup();
            if (! $backupPath) {
                $this->error('❌ Failed to create backup');

                return Command::FAILURE;
            }

            // 3. migrate:fresh
            $this->info('🗑️  Running migrate:fresh...');
            $this->call('migrate:fresh', ['--force' => true]);
            $this->info('✅ migrate:fresh completed successfully');
            $this->newLine();

            // 4. Restore data
            $this->info('📥 Restoring data from backup...');
            $this->restoreData($backupPath);
            $this->info('✅ Data restored successfully');
            $this->newLine();

            // 5. Seeders (optional)
            if ($this->option('seed')) {
                $this->info('🌱 Running seeders...');
                $this->call('db:seed', ['--force' => true]);
                $this->info('✅ Seeders completed');
            }

            // 6. Clean temporary files
            if (File::exists($backupPath)) {
                File::delete($backupPath);
                $this->info('🧹 Temporary files deleted');
            }

            $this->newLine();
            $this->info('✅ Process completed successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ An error occurred: '.$e->getMessage());
            $this->error('📍 File: '.$e->getFile().':'.$e->getLine());

            return Command::FAILURE;
        }
    }

    private function createBackup(): ?string
    {
        $this->info('💾 Creating backup...');

        // Try using mysqldump first (fastest and best)
        $mysqldumpPath = $this->createBackupWithMysqldump();
        if ($mysqldumpPath) {
            return $mysqldumpPath;
        }

        // Fallback: Use Laravel (optimized for large data)
        $this->warn('⚠️  mysqldump not available, using alternative method (optimized for large data)...');

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

            $this->info('⏳ Creating backup (may take time with large data)...');

            // Execute mysqldump and get output
            $process = Process::timeout(3600)->run($command);

            if (! $process->successful()) {
                $this->warn('⚠️  mysqldump failed: '.$process->errorOutput());

                return null;
            }

            // Save output to file
            $output = $process->output();
            File::put($backupFile, $output);

            // Compress file if requested
            if ($this->compress) {
                $this->info('🗜️  Compressing file...');
                $this->compressFile($backupFile, $compressedFile);
                File::delete($backupFile);
                $finalFile = $compressedFile;
            } else {
                $finalFile = $backupFile;
            }

            $size = File::size($finalFile);
            $this->info('✅ Backup created: '.$this->formatBytes($size));

            return $finalFile;
        } catch (\Exception $e) {
            $this->warn('⚠️  Error in mysqldump: '.$e->getMessage());

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
                throw new \Exception('Failed to open backup file');
            }

            // Header
            $this->writeSqlHeader($handle);

            // Get list of tables
            $tables = $this->getTablesToBackup();
            $totalTables = count($tables);

            $this->info("📊 Number of tables to backup: {$totalTables}");

            $progressBar = $this->output->createProgressBar($totalTables);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
            $progressBar->setMessage('Starting backup...');
            $progressBar->start();

            foreach ($tables as $index => $tableName) {
                $progressBar->setMessage("Processing: {$tableName}");

                // Check table size
                $rowCount = $this->getTableRowCount($tableName);

                if ($this->skipLarge && $rowCount > 10000) {
                    $progressBar->setMessage("⏭️  Skipping {$tableName} (large: {$rowCount} rows)");
                    $progressBar->advance();

                    continue;
                }

                // Backup data (with Chunking for large tables)
                $this->backupTableData($handle, $tableName, $rowCount);

                $progressBar->advance();
            }

            $progressBar->setMessage('Completed!');
            $progressBar->finish();
            $this->newLine(2);

            // Footer
            fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
            fwrite($handle, "COMMIT;\n");
            fclose($handle);

            // Compress file if requested
            if ($this->compress) {
                $compressedFile = $backupFile.'.gz';
                $this->info('🗜️  Compressing file...');
                $this->compressFile($backupFile, $compressedFile);
                File::delete($backupFile);
                $backupFile = $compressedFile;
            }

            $size = File::size($backupFile);
            $this->info('✅ Backup created: '.$this->formatBytes($size));

            return $backupFile;
        } catch (\Exception $e) {
            $this->error('❌ Error in backup: '.$e->getMessage());

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

        // For large tables: Chunking
        $this->info("  📦 Processing {$tableName} in chunks ({$rowCount} rows)...");

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

        // If file is compressed, decompress first
        if (str_ends_with($backupPath, '.gz')) {
            $this->info('📦 Decompressing file...');
            $uncompressedPath = str_replace('.gz', '', $backupPath);
            $this->decompressFile($backupPath, $uncompressedPath);
            $backupPath = $uncompressedPath;
        }

        $this->info('⏳ Restoring data (may take time)...');

        // Check if file exists
        if (! File::exists($backupPath)) {
            throw new \Exception("Backup file not found: {$backupPath}");
        }

        // Use unified method that works on Windows and Linux
        // Read file content and pass it via stdin (works on both systems)
        $sqlContent = File::get($backupPath);

        // Build mysql command
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database)
        );

        // Execute command with SQL content passed via stdin
        $process = Process::timeout(3600)
            ->input($sqlContent)
            ->run($command);

        if (! $process->successful()) {
            $errorOutput = $process->errorOutput();
            $output = $process->output();
            $errorMessage = $errorOutput ?: $output ?: 'Unknown error';

            // Show additional information to help diagnose
            $this->error('❌ Error details:');
            if ($errorOutput) {
                $this->error('   Error Output: '.$errorOutput);
            }
            if ($output) {
                $this->error('   Output: '.$output);
            }

            throw new \Exception('Failed to restore data: '.$errorMessage);
        }

        // Delete uncompressed file if temporary
        if (str_contains($backupPath, 'backup_') && File::exists($backupPath)) {
            File::delete($backupPath);
        }
    }

    private function compressFile(string $source, string $destination): void
    {
        $handle = fopen($source, 'rb');
        $gzHandle = gzopen($destination, 'wb9');

        if (! $handle || ! $gzHandle) {
            throw new \Exception('Failed to open files for compression');
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
            throw new \Exception('Failed to open files for decompression');
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
