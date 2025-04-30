<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the 'users' table does NOT already exist
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                // ---> ADD phone_number HERE <---
                $table->string('phone_number', 20)->nullable()->unique(); // Add length if needed, make nullable and unique
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                // ---> ADD is_active HERE <---
                $table->boolean('is_active')->default(true); // Add is_active, defaulting to true
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            // Optional: If the table *might* already exist but lack columns
            // This block ensures the columns are added if missing.
            // You can remove this 'else' block if you ALWAYS run migrate:fresh
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'phone_number')) {
                    // Ensure position matches the create block if desired (e.g., ->after('email'))
                    $table->string('phone_number', 20)->nullable()->unique()->after('email');
                }
                if (!Schema::hasColumn('users', 'is_active')) {
                    // Ensure position matches the create block if desired (e.g., ->after('remember_token'))
                    $table->boolean('is_active')->default(true)->after('remember_token');
                }
            });
        }

        // Keep these separate as they are independent tables
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
