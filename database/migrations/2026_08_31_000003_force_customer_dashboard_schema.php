<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Final safety migration for installations where an earlier dashboard
     * migration was recorded as completed but its tables are absent.
     * This migration is deliberately idempotent and only creates missing
     * tables; it never alters or deletes existing application data.
     */
    public function up(): void
    {
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedInteger('product_id');
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }

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

        // Some existing MySQL installations reject the Blueprint-based
        // CREATE TABLE for this legacy schema. Use explicit SQL without
        // foreign keys so the review table can be created independently.
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
        // Intentionally non-destructive: this safety migration must not
        // remove customer data during rollback.
    }
};
