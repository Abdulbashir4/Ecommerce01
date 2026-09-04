<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'is_demo')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_demo')->default(false)->after('imported_at');
                $table->index('is_demo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'is_demo')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['is_demo']);
                $table->dropColumn('is_demo');
            });
        }
    }
};
