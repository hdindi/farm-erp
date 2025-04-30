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
        Schema::create('disease_management', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('disease_id')->constrained('diseases')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('drug_id')->nullable()->constrained('drugs')->nullOnDelete()->cascadeOnUpdate(); // Nullable, SET NULL on drug delete
            $table->date('observation_date')->useCurrent(); // Defaults to current date
            $table->integer('affected_count')->unsigned()->nullable();
            $table->date('treatment_start_date')->nullable();
            $table->date('treatment_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes automatically created for FKs
            $table->index('observation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_management');
    }
};
