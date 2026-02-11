<?php
/**
 * Simple SQL migration runner.
 * Usage (CLI): php migrations/run.php
 */

require_once __DIR__ . '/../application/config/Database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

// Create migration history table if it doesn't exist
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS migration_history (
        id INT PRIMARY KEY AUTO_INCREMENT,
        filename VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
);

$migrationsDir = __DIR__;
$files = glob($migrationsDir . '/*.sql');
if (!$files) {
    echo "No migration files found.\n";
    exit(0);
}

sort($files, SORT_NATURAL);

// Fetch applied migrations
$applied = $pdo->query('SELECT filename FROM migration_history')->fetchAll(PDO::FETCH_COLUMN);
$applied = $applied ? array_flip($applied) : [];

$pending = array_filter($files, function ($file) use ($applied) {
    return !isset($applied[basename($file)]);
});

if (empty($pending)) {
    echo "No pending migrations.\n";
    exit(0);
}

$pdo->beginTransaction();
try {
    foreach ($pending as $file) {
        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new RuntimeException('Failed to read ' . $file);
        }

        $statements = splitSqlStatements($sql);
        foreach ($statements as $statement) {
            $trimmed = trim($statement);
            if ($trimmed === '') {
                continue;
            }
            $pdo->exec($trimmed);
        }

        $stmt = $pdo->prepare('INSERT INTO migration_history (filename) VALUES (?)');
        $stmt->execute([basename($file)]);

        echo 'Applied: ' . basename($file) . "\n";
    }

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo 'Migration failed: ' . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Split SQL into executable statements, ignoring semicolons in strings.
 */
function splitSqlStatements(string $sql): array {
    $statements = [];
    $buffer = '';
    $inSingle = false;
    $inDouble = false;
    $inBacktick = false;
    $len = strlen($sql);

    for ($i = 0; $i < $len; $i++) {
        $char = $sql[$i];
        $prev = $i > 0 ? $sql[$i - 1] : '';

        if ($char === "'" && !$inDouble && !$inBacktick && $prev !== '\\') {
            $inSingle = !$inSingle;
        } elseif ($char === '"' && !$inSingle && !$inBacktick && $prev !== '\\') {
            $inDouble = !$inDouble;
        } elseif ($char === '`' && !$inSingle && !$inDouble && $prev !== '\\') {
            $inBacktick = !$inBacktick;
        }

        if ($char === ';' && !$inSingle && !$inDouble && !$inBacktick) {
            $statements[] = $buffer;
            $buffer = '';
            continue;
        }

        $buffer .= $char;
    }

    if (trim($buffer) !== '') {
        $statements[] = $buffer;
    }

    return $statements;
}
