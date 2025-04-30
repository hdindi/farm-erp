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
        Schema::create('supplier_feed_prices', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('feed_type_id')->constrained('feed_types')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('supplier_price', 10, 2);
            $table->date('effective_date')->useCurrent();
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->index('effective_date');

            // Add CHECK constraint
            // DB::statement('ALTER TABLE supplier_feed_prices ADD CONSTRAINT supplier_feed_prices_check_price CHECK (supplier_price >= 0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consider dropping CHECK constraints here if added in up()
        Schema::dropIfExists('supplier_feed_prices');
    }
};
