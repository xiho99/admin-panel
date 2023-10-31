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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('roleName')->comment('Role Name');
            $table->string('roleSign')->comment('Role ID');
            $table->integer('sort')->comment('sort');
            $table->integer('status')->default(1)->comment('Whether to enable');
            $table->string('describe')->comment('describe');
            $table->text('menu_ids');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
