<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('layout_settings')) {
            Schema::create('layout_settings', function (Blueprint $table) {
                $table->id();
                $table->string('product_layout', 20)->default('grid');
                $table->unsignedTinyInteger('mobile_columns')->default(1);
                $table->unsignedTinyInteger('tablet_columns')->default(2);
                $table->unsignedTinyInteger('desktop_columns')->default(4);
                $table->unsignedTinyInteger('gap')->default(5);
                $table->string('text_align', 20)->default('left');
                $table->string('sidebar', 20)->default('right');
                $table->string('container', 20)->default('7xl');
                $table->string('section_spacing', 20)->default('normal');
                $table->boolean('shop_header')->default(true);
                $table->boolean('breadcrumbs')->default(true);
                $table->boolean('pagination')->default(true);
                $table->timestamps();
            });
            return;
        }

        Schema::table('layout_settings', function (Blueprint $table) {
            $columns = [
                'product_layout' => fn() => $table->string('product_layout', 20)->default('grid'),
                'mobile_columns' => fn() => $table->unsignedTinyInteger('mobile_columns')->default(1),
                'tablet_columns' => fn() => $table->unsignedTinyInteger('tablet_columns')->default(2),
                'desktop_columns' => fn() => $table->unsignedTinyInteger('desktop_columns')->default(4),
                'gap' => fn() => $table->unsignedTinyInteger('gap')->default(5),
                'text_align' => fn() => $table->string('text_align', 20)->default('left'),
                'sidebar' => fn() => $table->string('sidebar', 20)->default('right'),
                'container' => fn() => $table->string('container', 20)->default('7xl'),
                'section_spacing' => fn() => $table->string('section_spacing', 20)->default('normal'),
                'shop_header' => fn() => $table->boolean('shop_header')->default(true),
                'breadcrumbs' => fn() => $table->boolean('breadcrumbs')->default(true),
                'pagination' => fn() => $table->boolean('pagination')->default(true),
            ];
            foreach ($columns as $name => $add) {
                if (!Schema::hasColumn('layout_settings', $name)) {
                    $add();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layout_settings');
    }
};
