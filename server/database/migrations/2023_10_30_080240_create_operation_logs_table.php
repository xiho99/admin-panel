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

        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('controller');
            $table->string('method');
            $table->text('parameters');
            $table->string('end_time')->nullable();
            $table->string('start_time')->nullable();
            $table->string('nickname');
            $table->integer('admin_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_logs');
    }
};
