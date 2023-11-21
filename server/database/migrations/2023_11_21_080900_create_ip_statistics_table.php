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
        Schema::create('ip_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->nullable();
            $table->integer('ip_access')->nullable()->default(1);
            $table->string('controller')->nullable();
            $table->string('method')->nullable();
            $table->string('parameters')->nullable();
            $table->string('create_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_statistics');
    }
};
