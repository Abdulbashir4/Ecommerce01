<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasColumn('products', 'gallery_images')) {
            return;
        }

        $driver = DB::connection()->getDriverName();
        $versionRow = DB::selectOne('SELECT VERSION() AS version');
        $version = strtolower((string) ($versionRow->version ?? ''));
        $isMariaDb = str_contains($version, 'mariadb');

        /*
         * MariaDB can create a CHECK constraint for a JSON column.
         * Depending on the MariaDB version, that constraint is not always
         * exposed consistently through information_schema.CHECK_CONSTRAINTS.
         *
         * Therefore first inspect SHOW CREATE TABLE and discover the real
         * constraint name instead of assuming that "gallery_images" exists.
         */
        $tableRow = DB::selectOne('SHOW CREATE TABLE `products`');
        $createTable = '';

        if ($tableRow) {
            $row = (array) $tableRow;

            foreach ($row as $key => $value) {
                if (str_contains(strtolower((string) $key), 'create table')) {
                    $createTable = (string) $value;
                    break;
                }
            }

            if ($createTable === '' && isset($row['Create Table'])) {
                $createTable = (string) $row['Create Table'];
            }
        }

        $constraintNames = [];

        /*
         * Match named CHECK constraints whose expression references
         * gallery_images.
         *
         * Examples:
         *   CONSTRAINT `gallery_images` CHECK (json_valid(`gallery_images`))
         *   CONSTRAINT `some_name` CHECK (`gallery_images` IS NULL ...)
         */
        if ($createTable !== '') {
            $patterns = [
                '/CONSTRAINT\s+`([^`]+)`\s+CHECK\s*\(([^;]+?)\)(?:,|\s*\n|\s*\r|\s*$)/is',
                '/CONSTRAINT\s+"([^"]+)"\s+CHECK\s*\(([^;]+?)\)(?:,|\s*\n|\s*\r|\s*$)/is',
            ];

            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $createTable, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $name = (string) $match[1];
                        $clause = strtolower((string) $match[2]);

                        if (str_contains($clause, 'gallery_images')) {
                            $constraintNames[$name] = true;
                        }
                    }
                }
            }
        }

        /*
         * Fallback to information_schema in case SHOW CREATE TABLE did not
         * expose a named constraint in a format matched above.
         */
        try {
            $database = DB::getDatabaseName();

            $constraints = DB::select(
                "SELECT tc.CONSTRAINT_NAME AS constraint_name,
                        cc.CHECK_CLAUSE AS check_clause
                 FROM information_schema.TABLE_CONSTRAINTS tc
                 LEFT JOIN information_schema.CHECK_CONSTRAINTS cc
                   ON cc.CONSTRAINT_SCHEMA = tc.CONSTRAINT_SCHEMA
                  AND cc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
                 WHERE tc.CONSTRAINT_SCHEMA = ?
                   AND tc.TABLE_NAME = ?
                   AND tc.CONSTRAINT_TYPE = 'CHECK'",
                [$database, 'products']
            );

            foreach ($constraints as $constraint) {
                $clause = strtolower((string) ($constraint->check_clause ?? ''));

                if (str_contains($clause, 'gallery_images')) {
                    $constraintNames[(string) $constraint->constraint_name] = true;
                }
            }
        } catch (\Throwable $e) {
            // SHOW CREATE TABLE is already our primary discovery method.
        }

        /*
         * Drop every discovered gallery_images CHECK constraint.
         * If a discovered name disappeared between inspection and DROP,
         * ignore that specific race-condition and continue.
         */
        foreach (array_keys($constraintNames) as $name) {
            $safeName = str_replace('`', '``', $name);

            try {
                if ($isMariaDb) {
                    DB::statement("ALTER TABLE `products` DROP CONSTRAINT `{$safeName}`");
                } elseif ($driver === 'mysql') {
                    DB::statement("ALTER TABLE `products` DROP CHECK `{$safeName}`");
                } else {
                    DB::statement("ALTER TABLE `products` DROP CONSTRAINT `{$safeName}`");
                }
            } catch (\Throwable $e) {
                /*
                 * If the constraint name is stale/not present, continue.
                 * The following column conversion is still safe to attempt.
                 */
            }
        }

        /*
         * LONGTEXT removes the JSON validation restriction while Laravel's
         * Product model can continue using:
         *
         *   'gallery_images' => 'array'
         *
         * so the application still stores/reads the gallery as an array.
         */
        Schema::table('products', function (Blueprint $table): void {
            $table->longText('gallery_images')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally leave LONGTEXT in place. Re-adding the legacy JSON
        // constraint could reintroduce the unlimited-gallery problem.
    }
};
