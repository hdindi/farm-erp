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
        Schema::create('vaccine_schedule', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('vaccine_id')->constrained('vaccines')->restrictOnDelete()->cascadeOnUpdate();
            $table->date('date_due');
            $table->enum('status', ['administered', 'scheduled', 'missed'])->default('scheduled');
            $table->date('administered_date')->nullable();
            $table->foreignId('vaccination_log_id')->nullable()->constrained('vaccination_logs')->nullOnDelete()->cascadeOnUpdate(); // Optional link
            $table->timestamps();

            $table->index(['status', 'date_due']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_schedule');
    }
};
