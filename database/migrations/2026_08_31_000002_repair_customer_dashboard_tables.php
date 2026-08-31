<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Repairs installations where the customer-dashboard migration was
     * recorded as run but one or more dashboard tables are missing.
     *
     * This is intentionally idempotent so it is safe when the original
     * customer-dashboard migration has already created the tables.
     */
    public function up(): void
    {
        if (!Schema::hasTable('customer_addresses')) {
            Schema::create('customer_addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('label', 50)->default('Home');
                $table->string('recipient_name', 150);
                $table->string('phone', 50);
                $table->text('address');
                $table->string('city', 100);
                $table->string('postal_code', 50)->nullable();
                $table->string('country', 100)->default('Bangladesh');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
                $table->index(['user_id', 'is_default']);
            });
        }

        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedInteger('product_id');
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }

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

    public function down(): void
    {
        // Do not drop customer data from a repair migration.
    }
};
