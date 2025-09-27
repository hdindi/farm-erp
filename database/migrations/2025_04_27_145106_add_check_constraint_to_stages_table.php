<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_check_constraint_to_stages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Import the DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds a CHECK constraint to ensure max_age_days is not less than min_age_days.
     */
    public function up(): void
    {
        // SQLite doesn't support adding CHECK constraints via ALTER TABLE
        // Only add constraint for MySQL/PostgreSQL
        $driver = DB::getDriverName();
        
        if (in_array($driver, ['mysql', 'pgsql'])) {
            DB::statement('ALTER TABLE stages ADD CONSTRAINT stages_check_age CHECK (max_age_days >= min_age_days)');
        }
    }

    /**
     * Reverse the migrations.
     * Removes the CHECK constraint added in the up() method.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if (in_array($driver, ['mysql', 'pgsql'])) {
            Schema::table('stages', function (Blueprint $table) {
                DB::statement('ALTER TABLE stages DROP CONSTRAINT stages_check_age');
            });
        }
    }
};
