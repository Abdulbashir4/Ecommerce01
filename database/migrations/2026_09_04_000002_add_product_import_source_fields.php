<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('source_url')->nullable()->after('gallery_images');
            $table->string('source_platform', 50)->nullable()->after('source_url');
            $table->string('source_product_id', 150)->nullable()->after('source_platform');
            $table->timestamp('imported_at')->nullable()->after('source_product_id');
            $table->index('source_platform');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['source_platform']);
            $table->dropColumn(['source_url', 'source_platform', 'source_product_id', 'imported_at']);
        });
    }
};
