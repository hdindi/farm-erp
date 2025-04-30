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
        Schema::create('feed_records', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('daily_record_id')->constrained('daily_records')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('feed_type_id')->constrained('feed_types')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('quantity_kg', 10, 2);
            $table->decimal('cost_per_kg', 10, 2)->nullable();
            $table->time('feeding_time')->nullable();
            $table->string('notes', 1000)->nullable();
            $table->timestamps();

            // Add CHECK constraints
            // DB::statement('ALTER TABLE feed_records ADD CONSTRAINT feed_records_check_quantity CHECK (quantity_kg > 0)');
            // DB::statement('ALTER TABLE feed_records ADD CONSTRAINT feed_records_check_cost CHECK (cost_per_kg IS NULL OR cost_per_kg >= 0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consider dropping CHECK constraints here if added in up()
        Schema::dropIfExists('feed_records');
    }
};
