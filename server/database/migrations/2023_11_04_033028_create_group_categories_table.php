<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cat_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_visible')->default(0);
            $table->boolean('is_delete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_categories');
    }
};
