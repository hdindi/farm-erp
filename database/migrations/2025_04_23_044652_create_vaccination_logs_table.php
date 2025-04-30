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
        Schema::create('vaccination_logs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('daily_record_id')->constrained('daily_records')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vaccine_id')->constrained('vaccines')->restrictOnDelete()->cascadeOnUpdate();
            $table->integer('birds_vaccinated')->unsigned();
            $table->string('administered_by', 100)->nullable();
            $table->date('next_due_date')->nullable();
            $table->string('notes', 1000)->nullable();
            $table->timestamps();

            $table->index('next_due_date');

            // Add CHECK constraint
            // DB::statement('ALTER TABLE vaccination_logs ADD CONSTRAINT vaccination_logs_check_count CHECK (birds_vaccinated >= 0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consider dropping CHECK constraints here if added in up()
        Schema::dropIfExists('vaccination_logs');
    }
};
