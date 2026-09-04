<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('subcategories', 'parent_subcategory_id')) {
            Schema::table('subcategories', function (Blueprint $table) {
                $table->unsignedInteger('parent_subcategory_id')->nullable()->after('category_id');
                $table->index('parent_subcategory_id');
                $table->foreign('parent_subcategory_id')
                    ->references('subcategory_id')
                    ->on('subcategories')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('subcategories', 'parent_subcategory_id')) {
            Schema::table('subcategories', function (Blueprint $table) {
                $table->dropForeign(['parent_subcategory_id']);
                $table->dropIndex(['parent_subcategory_id']);
                $table->dropColumn('parent_subcategory_id');
            });
        }
    }
};
