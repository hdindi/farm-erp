<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Needed for complex checks

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('egg_production', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('daily_record_id')->constrained('daily_records')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('total_eggs')->unsigned()->default(0);
            $table->integer('good_eggs')->unsigned()->default(0);
            $table->integer('cracked_eggs')->unsigned()->default(0); // Renamed
            $table->integer('damaged_eggs')->unsigned()->default(0); // Added
            $table->time('collection_time')->nullable();
            $table->string('notes', 1000)->nullable();
            $table->timestamps();

            // Add CHECK constraint
            // DB::statement('ALTER TABLE egg_production ADD CONSTRAINT egg_production_check_totals CHECK (total_eggs >= (good_eggs + cracked_eggs + damaged_eggs))');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consider dropping CHECK constraints here if added in up()
        Schema::dropIfExists('egg_production');
    }
};
