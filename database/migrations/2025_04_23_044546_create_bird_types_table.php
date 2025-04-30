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
        Schema::create('bird_types', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->string('name', 100)->unique();
            $table->string('description', 1000)->nullable();
            $table->smallInteger('egg_production_cycle')->unsigned()->nullable();
            $table->timestamps(); // created_at, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bird_types');
    }
};
