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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->string('purchase_order_no', 25)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('feed_type_id')->constrained('feed_types')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('purchase_unit_id')->constrained('purchase_units')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2); // Added
            $table->decimal('total_price', 12, 2); // Added
            $table->date('order_date')->useCurrent(); // Added
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->foreignId('purchase_order_status_id')->constrained('purchase_order_statuses')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('notes', 1000)->nullable();
            $table->timestamps();

            $table->index('order_date');

            // Add CHECK constraints
            // DB::statement('ALTER TABLE purchase_orders ADD CONSTRAINT po_check_quantity CHECK (quantity > 0)');
            // DB::statement('ALTER TABLE purchase_orders ADD CONSTRAINT po_check_prices CHECK (unit_price >= 0 AND total_price >= 0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consider dropping CHECK constraints here if added in up()
        Schema::dropIfExists('purchase_orders');
    }
};
