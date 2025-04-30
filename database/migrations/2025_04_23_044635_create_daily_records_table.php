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
        Schema::create('daily_records', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('record_date');
            $table->foreignId('stage_id')->constrained('stages')->restrictOnDelete()->cascadeOnUpdate();
            $table->smallInteger('day_in_stage')->unsigned();
            $table->integer('alive_count')->unsigned();
            $table->integer('dead_count')->unsigned()->default(0);
            $table->integer('culls_count')->unsigned()->default(0);
            $table->decimal('mortality_rate', 5, 2)->nullable();
            $table->integer('average_weight_grams')->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'record_date']); // Unique constraint
            $table->index('record_date');

            // Add CHECK constraints if needed and supported
            // DB::statement('ALTER TABLE daily_records ADD CONSTRAINT daily_records_check_counts CHECK (alive_count >= 0 AND dead_count >= 0 AND culls_count >= 0)');
            // DB::statement('ALTER TABLE daily_records ADD CONSTRAINT daily_records_check_mortality CHECK (mortality_rate IS NULL OR (mortality_rate >= 0 AND mortality_rate <= 100))');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consider dropping CHECK constraints here if added in up()
        Schema::dropIfExists('daily_records');
    }
};
