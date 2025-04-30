<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_check_constraint_to_stages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import the DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds a CHECK constraint to ensure max_age_days is not less than min_age_days.
     *
     * @return void
     */
    public function up(): void
    {
        // Use DB::statement for CHECK constraints as direct Blueprint support varies
        // Ensure the constraint name 'stages_check_age' is unique within the database schema
        DB::statement('ALTER TABLE stages ADD CONSTRAINT stages_check_age CHECK (max_age_days >= min_age_days)');
    }

    /**
     * Reverse the migrations.
     * Removes the CHECK constraint added in the up() method.
     *
     * @return void
     */
    public function down(): void
    {
        // Use Schema::table with a closure to ensure the table exists before attempting to modify it
        Schema::table('stages', function (Blueprint $table) {
            // Use DB::statement to drop the constraint
            // The exact syntax for dropping constraints can vary slightly between database systems.
            // This syntax is common for MySQL/MariaDB.
            // For PostgreSQL, it would typically be: DB::statement('ALTER TABLE stages DROP CONSTRAINT stages_check_age');
            // For SQLite, dropping CHECK constraints is often not directly supported via ALTER TABLE.
            DB::statement('ALTER TABLE stages DROP CONSTRAINT stages_check_age');
        });
    }
};
