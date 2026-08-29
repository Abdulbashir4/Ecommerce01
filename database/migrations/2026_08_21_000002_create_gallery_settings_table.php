<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_settings', function (Blueprint $table) {
            $table->id();
            $table->string('layout')->default('grid');
            $table->unsignedTinyInteger('columns')->default(3);
            $table->string('card_style')->default('rounded');
            $table->string('aspect_ratio')->default('4/3');
            $table->boolean('show_title')->default(true);
            $table->boolean('show_description')->default(true);
            $table->boolean('show_overlay')->default(true);
            $table->boolean('autoplay')->default(false);
            $table->string('section_title')->default('Our Gallery');
            $table->string('section_subtitle')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_settings');
    }
};
