<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Needed for raw SQL check constraint if necessary

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->string('name', 100)->unique();
            $table->string('description', 1000)->nullable();
            $table->smallInteger('min_age_days')->unsigned();
            $table->smallInteger('max_age_days')->unsigned();
            $table->integer('target_weight_grams')->unsigned()->nullable();
            $table->timestamps();

            $table->index(['min_age_days', 'max_age_days'], 'stages_min_max_age_idx');

            // Add CHECK constraint (requires DB::statement as Blueprint doesn't directly support complex CHECK)
            // Note: SQLite doesn't enforce CHECK constraints by default before version 3.37.0
            // Consider conditional logic if supporting multiple DBs or older SQLite
            if (DB::connection()->getDriverName() !== 'sqlite') {
               // DB::statement('ALTER TABLE stages ADD CONSTRAINT stages_check_age CHECK (max_age_days >= min_age_days)');
            }
            // Alternatively, handle validation in application logic
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            // Drop CHECK constraint if it exists and DB supports it
            // Naming conventions for constraints vary between DBs, adjust if needed
            if (DB::connection()->getDriverName() === 'mysql') {
                $table->dropIndex('stages_min_max_age_idx'); // Drop index first
                DB::statement('ALTER TABLE stages DROP CONSTRAINT stages_check_age');
            } elseif (DB::connection()->getDriverName() === 'pgsql') {
                // PostgreSQL might name it differently, e.g., stages_max_age_days_check
                // Or use: DB::statement('ALTER TABLE stages DROP CONSTRAINT stages_check_age'); if named explicitly
            }
            // Add similar blocks for other DBs if needed
        });
        Schema::dropIfExists('stages');
    }
};
