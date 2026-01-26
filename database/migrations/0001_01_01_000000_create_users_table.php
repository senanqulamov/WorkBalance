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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Two-Factor Authentication
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            // Role flags - users can have multiple roles
            $table->boolean('is_buyer')->default(true);
            $table->boolean('is_seller')->default(false);
            $table->boolean('is_supplier')->default(false);

            // Business/Company Information
            $table->string('company_name')->nullable();
            $table->string('tax_id')->nullable(); // VAT/Tax ID
            $table->string('business_type')->nullable(); // Individual, Company, Corporation
            $table->text('business_description')->nullable();

            // Contact Information
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();

            // Address Information
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();

            // Supplier-Specific Fields (for Ariba integration)
            $table->string('supplier_code')->nullable()->unique(); // Unique supplier identifier
            $table->string('duns_number')->nullable(); // D-U-N-S Number (Ariba standard)
            $table->string('ariba_network_id')->nullable(); // Ariba Network ID (ANID)
            $table->json('payment_terms')->nullable(); // Net 30, Net 60, etc.
            $table->string('currency')->default('USD');
            $table->decimal('credit_limit', 15, 2)->nullable();
            $table->enum('supplier_status', ['pending', 'active', 'inactive', 'blocked'])->default('pending');
            $table->timestamp('supplier_approved_at')->nullable();

            // Seller-Specific Fields
            $table->decimal('commission_rate', 5, 2)->nullable(); // Commission %
            $table->boolean('verified_seller')->default(false);
            $table->timestamp('verified_at')->nullable();

            // Performance Metrics
            $table->decimal('rating', 3, 2)->nullable(); // Average rating (0.00 - 5.00)
            $table->integer('total_orders')->default(0);
            $table->integer('completed_orders')->default(0);
            $table->integer('cancelled_orders')->default(0);

            // Account Status
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable(); // Internal notes

            $table->rememberToken();
            $table->timestamps();

            // Indexes for performance
            $table->index(['is_supplier', 'supplier_status']);
            $table->index(['is_seller', 'verified_seller']);
            $table->index(['is_buyer', 'is_active']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
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
