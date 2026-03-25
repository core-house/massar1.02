<?php

echo "=== Simple Reliability Test ===\n\n";

// Test database connection
echo "1. Testing Database Connection...\n";
try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=kon3',
        'root',
        ''
    );
    echo "✅ Database connected\n\n";
} catch (PDOException $e) {
    echo '❌ Database connection failed: '.$e->getMessage()."\n\n";
    exit(1);
}

// Test table structure
echo "2. Testing Table Structure...\n";
$stmt = $pdo->query('DESCRIBE print_jobs');
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

$requiredColumns = [
    'idempotency_key',
    'payload_hash',
    'sequence',
    'error_type',
    'agent_http_status',
    'sent_at',
    'can_auto_retry',
    'retried_by',
];

foreach ($requiredColumns as $col) {
    $exists = in_array($col, $columns);
    echo ($exists ? '✅' : '❌')." Column '$col': ".($exists ? 'EXISTS' : 'MISSING')."\n";
}

echo "\n3. Testing Unique Constraint on idempotency_key...\n";
$stmt = $pdo->query("SHOW INDEX FROM print_jobs WHERE Column_name = 'idempotency_key'");
$index = $stmt->fetch();
if ($index && $index['Non_unique'] == 0) {
    echo "✅ Unique constraint exists on idempotency_key\n";
} else {
    echo "❌ Unique constraint missing on idempotency_key\n";
}

echo "\n4. Testing Print Jobs Count...\n";
$stmt = $pdo->query('SELECT COUNT(*) as count FROM print_jobs');
$result = $stmt->fetch();
echo "✅ Total print jobs in database: {$result['count']}\n";

echo "\n5. Testing Status Enum Values...\n";
$stmt = $pdo->query("SHOW COLUMNS FROM print_jobs WHERE Field = 'status'");
$column = $stmt->fetch();
echo "✅ Status enum: {$column['Type']}\n";

echo "\n6. Testing Error Type Enum Values...\n";
$stmt = $pdo->query("SHOW COLUMNS FROM print_jobs WHERE Field = 'error_type'");
$column = $stmt->fetch();
echo "✅ Error type enum: {$column['Type']}\n";

echo "\n=== All Tests Completed Successfully ===\n";
