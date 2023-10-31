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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('role_ids');
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('username')->nullable();
            $table->string('nickname')->nullable();
            $table->string('describe')->nullable();
            $table->boolean('status')->default(0);
            $table->date('overdue_time')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
