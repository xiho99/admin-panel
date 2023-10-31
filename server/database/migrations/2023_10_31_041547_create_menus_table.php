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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('menuSuperior')->nullable()->comment('parent route');
            $table->string('menuSuperiorPath')->nullable();
            $table->integer('menuType')->default(1)->comment('Routing type, 1. Menu 2. Button');
            $table->string('name')->nullable()->comment('Route name');
            $table->string('component')->comment('component path');
            $table->integer('isLink')->default(0)->comment('Is it an external link?');
            $table->integer('menuSort')->comment('sort');
            $table->string('redirect')->nullable()->comment('Redirect');
            $table->string('path')->comment('routing path');
            $table->text('meta')->comment('Other parameters');
            $table->integer('is_parent')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
