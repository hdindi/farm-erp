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
        Schema::create('sales_prices', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('sales_unit_id')->constrained('sales_units')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('price', 10, 2);
            $table->enum('item_type', ['egg', 'bird', 'manure']); // Added type
            $table->foreignId('item_id')->nullable(); // Optional polymorphic/specific link
            $table->date('effective_date')->useCurrent();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['item_type', 'item_id']); // Index polymorphic/specific link
            $table->index(['status', 'effective_date']);

            // Add CHECK constraint
            // DB::statement('ALTER TABLE sales_prices ADD CONSTRAINT sales_prices_check_price CHECK (price >= 0)');

            // Consider adding FK constraints for item_id if it points to specific tables
            // $table->foreign('item_id')->references('id')->on('bird_types'); // Example if item_id is bird_type_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consider dropping CHECK constraints here if added in up()
        Schema::dropIfExists('sales_prices');
    }
};
