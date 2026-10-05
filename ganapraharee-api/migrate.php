<?php

declare(strict_types=1);

require_once __DIR__ . '/config/bootstrap.php';

use Ganapraharee\Api\Helpers\Database;

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "   Ganapraharee Migration Runner" . PHP_EOL;
echo "========================================" . PHP_EOL;
echo PHP_EOL;

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

try {
    $pdo = Database::connection();

    echo "✓ Database connected successfully." . PHP_EOL;
    echo PHP_EOL;

} catch (Throwable $e) {

    echo "✗ Database connection failed." . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;

    exit(1);
}

/*
|--------------------------------------------------------------------------
| Migration Directory
|--------------------------------------------------------------------------
*/

$migrationsPath = __DIR__ . '/database/migrations';

if (!is_dir($migrationsPath)) {
    echo "✗ Migration directory not found." . PHP_EOL;
    exit(1);
}

/*
|--------------------------------------------------------------------------
| Get Migration Files
|--------------------------------------------------------------------------
*/

$migrationFiles = glob($migrationsPath . '/*.sql');

if ($migrationFiles === false) {
    echo "✗ Failed to read migration directory." . PHP_EOL;
    exit(1);
}

sort($migrationFiles, SORT_STRING);

echo "Migration files:" . PHP_EOL;

foreach ($migrationFiles as $migrationFile) {
    echo "  - " . basename($migrationFile) . PHP_EOL;
}

echo PHP_EOL;

/*
|--------------------------------------------------------------------------
| Get Executed Migrations
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query(
    'SELECT migration FROM migrations ORDER BY migration ASC'
);

$executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

$executedMigrations = array_flip($executedMigrations);

/*
|--------------------------------------------------------------------------
| Find Pending Migrations
|--------------------------------------------------------------------------
*/

$pendingMigrations = [];

foreach ($migrationFiles as $migrationFile) {

    $migrationName = basename($migrationFile);

    if (!isset($executedMigrations[$migrationName])) {
        $pendingMigrations[] = $migrationFile;
    }
}

/*
|--------------------------------------------------------------------------
| No Pending Migration
|--------------------------------------------------------------------------
*/

if (empty($pendingMigrations)) {

    echo "Migration status:" . PHP_EOL;
    echo PHP_EOL;
    echo "  ✓ No pending migrations." . PHP_EOL;
    echo PHP_EOL;

    exit(0);
}

/*
|--------------------------------------------------------------------------
| Determine Next Batch
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query(
    'SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations'
);

$nextBatch = (int) $stmt->fetchColumn();

echo "Migration status:" . PHP_EOL;
echo PHP_EOL;

foreach ($migrationFiles as $migrationFile) {

    $migrationName = basename($migrationFile);

    if (isset($executedMigrations[$migrationName])) {
        echo "  ✓ Already executed : {$migrationName}" . PHP_EOL;
    } else {
        echo "  → Pending          : {$migrationName}" . PHP_EOL;
    }
}

echo PHP_EOL;

echo "Running batch #{$nextBatch}..." . PHP_EOL;
echo PHP_EOL;

/*
|--------------------------------------------------------------------------
| Execute Pending Migrations
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    foreach ($pendingMigrations as $migrationFile) {

        $migrationName = basename($migrationFile);

        echo "  → Running : {$migrationName}" . PHP_EOL;

        $sql = file_get_contents($migrationFile);

        if ($sql === false) {
            throw new RuntimeException(
                "Unable to read migration file: {$migrationName}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Execute SQL
        |--------------------------------------------------------------------------
        */

        $pdo->exec($sql);

        /*
        |--------------------------------------------------------------------------
        | Record Migration
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare(
            'INSERT INTO migrations (migration, batch)
             VALUES (:migration, :batch)'
        );

        $stmt->execute([
            'migration' => $migrationName,
            'batch' => $nextBatch,
        ]);

        echo "    ✓ Migration completed." . PHP_EOL;
    }

    $pdo->commit();

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo PHP_EOL;
    echo "✗ Migration failed." . PHP_EOL;
    echo "  " . $e->getMessage() . PHP_EOL;
    echo PHP_EOL;
    echo "No migration history was committed." . PHP_EOL;

    exit(1);
}

echo PHP_EOL;
echo "========================================" . PHP_EOL;
echo "Migration completed successfully." . PHP_EOL;
echo "Batch #{$nextBatch}" . PHP_EOL;
echo "========================================" . PHP_EOL;
echo PHP_EOL;