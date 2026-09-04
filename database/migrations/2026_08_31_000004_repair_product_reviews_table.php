<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('product_reviews')) {
            DB::statement("CREATE TABLE IF NOT EXISTS product_reviews (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                order_id INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                rating TINYINT UNSIGNED NOT NULL,
                review TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                PRIMARY KEY (id),
                UNIQUE KEY product_reviews_user_order_product_unique (user_id, order_id, product_id)
            ) ENGINE=InnoDB");
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive.
    }
};
