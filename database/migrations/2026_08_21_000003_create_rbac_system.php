<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 100)->unique();
                $table->string('description')->nullable();
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('slug', 150)->unique();
                $table->string('group', 100)->index();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Role User
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->foreignId('role_id')
                    ->constrained('roles')
                    ->cascadeOnDelete();

                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->primary(['role_id', 'user_id']);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Permission Role
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->foreignId('permission_id')
                    ->constrained('permissions')
                    ->cascadeOnDelete();

                $table->foreignId('role_id')
                    ->constrained('roles')
                    ->cascadeOnDelete();

                $table->primary(['permission_id', 'role_id']);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Users - RBAC fields
        |--------------------------------------------------------------------------
        */
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {

                if (!Schema::hasColumn('users', 'status')) {
                    $table->string('status', 20)
                        ->default('active')
                        ->after('is_admin');
                }

                if (!Schema::hasColumn('users', 'last_login_at')) {
                    $table->timestamp('last_login_at')
                        ->nullable()
                        ->after('status');
                }

                if (!Schema::hasColumn('users', 'last_login_ip')) {
                    $table->string('last_login_ip', 45)
                        ->nullable()
                        ->after('last_login_at');
                }

                if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                    $table->unsignedInteger('failed_login_attempts')
                        ->default(0)
                        ->after('last_login_ip');
                }

                if (!Schema::hasColumn('users', 'locked_until')) {
                    $table->timestamp('locked_until')
                        ->nullable()
                        ->after('failed_login_attempts');
                }

                if (!Schema::hasColumn('users', 'force_password_change')) {
                    $table->boolean('force_password_change')
                        ->default(false)
                        ->after('locked_until');
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Admin Audit Logs
        |--------------------------------------------------------------------------
        */
        if (!Schema::hasTable('admin_audit_logs')) {
            Schema::create('admin_audit_logs', function (Blueprint $table) {
                $table->id();

                $table->foreignId('actor_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->foreignId('target_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->string('action', 100)->index();

                $table->string('subject_type')->nullable();

                $table->unsignedBigInteger('subject_id')->nullable();

                $table->string('ip_address', 45)->nullable();

                $table->text('user_agent')->nullable();

                $table->json('metadata')->nullable();

                $table->timestamps();

                $table->index([
                    'subject_type',
                    'subject_id'
                ]);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audit_logs');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {

                $columns = [];

                foreach ([
                    'status',
                    'last_login_at',
                    'last_login_ip',
                    'failed_login_attempts',
                    'locked_until',
                    'force_password_change',
                ] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $columns[] = $column;
                    }
                }

                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};