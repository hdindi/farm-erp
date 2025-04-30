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
        Schema::create('sales_records', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->foreignId('sales_person_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate(); // Assumed FK to users
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->foreignId('sales_price_id')->constrained('sales_prices')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('quantity', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->date('sale_date')->useCurrent();
            $table->string('notes', 1000)->nullable();
            $table->timestamps();

            $table->index('sale_date');

            // Add CHECK constraints
            // DB::statement('ALTER TABLE sales_records ADD CONSTRAINT sales_records_check_quantity CHECK (quantity > 0)');
            // DB::statement('ALTER TABLE sales_records ADD CONSTRAINT sales_records_check_amounts CHECK (total_amount >= 0 AND amount_paid >= 0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Consider dropping CHECK constraints here if added in up()
        Schema::dropIfExists('sales_records');
    }
};
