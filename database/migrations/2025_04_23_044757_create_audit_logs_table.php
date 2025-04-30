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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PK AI
            $table->timestamp('event_time')->useCurrent();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate(); // Nullable, SET NULL on user delete
            $table->string('table_name', 64);
            $table->unsignedBigInteger('record_id')->nullable(); // Allow NULL if not record specific
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE', 'SOFT_DELETE', 'LOGIN', 'LOGOUT', 'SYSTEM']); // Added options
            $table->longText('old_values')->nullable();
            $table->longText('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps(); // <-- ADD THIS LINE

            // Note: `timestamps()` not typically needed if event_time covers creation time
            // $table->timestamps();

            $table->index(['table_name', 'record_id']);
            $table->index('event_time');
            $table->index('action');
            // FK index for user_id is created automatically
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
