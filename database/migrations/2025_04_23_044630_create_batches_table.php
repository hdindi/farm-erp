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
        Schema::create('batches', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->string('batch_code', 20)->unique();
            $table->foreignId('bird_type_id')->constrained('bird_types')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('breed_id')->constrained('breeds')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('source_farm')->nullable();
            $table->smallInteger('bird_age_days')->unsigned();
            $table->integer('initial_population')->unsigned();
            $table->integer('current_population')->unsigned();
            $table->date('date_received');
            $table->date('hatch_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'culled'])->default('active');
            $table->timestamps();

            // Indexes are automatically created for foreign keys by Laravel
            // Add specific indexes if needed beyond PK and FKs
            $table->index('status');
            $table->index(['date_received', 'expected_end_date'], 'batches_dates_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches'); // Drops table and associated FKs/indexes
    }
};
