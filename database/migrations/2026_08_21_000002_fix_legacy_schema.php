<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'updated_at')) {
            Schema::table('users', fn (Blueprint $t) => $t->timestamp('updated_at')->nullable()->after('created_at'));
        }

        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->unsignedBigInteger('user_id')->nullable()->after('payment_status');
                $t->index('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'updated_at')) {
            Schema::table('users', fn (Blueprint $t) => $t->dropColumn('updated_at'));
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->dropIndex(['user_id']);
                $t->dropColumn('user_id');
            });
        }
    }
};
