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
        // Drop all existing tables
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('mix_design_compositions');
        Schema::dropIfExists('mix_designs');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('user_invitations');
        Schema::dropIfExists('user_modules');
        Schema::dropIfExists('module_recommendations');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('users');
        Schema::dropIfExists('business_types');

        // Create business_types table with UUID
        Schema::create('business_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Create users table with UUID
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->uuid('business_type_id');
            $table->string('company_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('business_type_id')->references('id')->on('business_types')->onDelete('cascade');
        });

        // Create modules table with UUID
        Schema::create('modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('icon')->nullable();
            $table->string('version')->default('1.0.0');
            $table->boolean('is_core')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Create module_recommendations table with UUID
        Schema::create('module_recommendations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('business_type_id');
            $table->uuid('module_id');
            $table->integer('priority')->default(1);
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->foreign('business_type_id')->references('id')->on('business_types')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->unique(['business_type_id', 'module_id']);
        });

        // Create user_modules table with UUID
        Schema::create('user_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('module_id');
            $table->boolean('is_active')->default(true);
            $table->timestamp('activated_at')->nullable();
            $table->json('configuration')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->unique(['user_id', 'module_id']);
        });

        // Create user_invitations table with UUID
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->string('name');
            $table->string('company_name');
            $table->uuid('business_type_id');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('role')->default('user');
            $table->text('message')->nullable();
            $table->string('token')->unique();
            $table->uuid('invited_by');
            $table->timestamp('expires_at');
            $table->enum('status', ['pending', 'accepted', 'cancelled'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->uuid('user_id')->nullable();
            $table->timestamps();

            $table->foreign('business_type_id')->references('id')->on('business_types')->onDelete('cascade');
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Create activity_logs table with UUID
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('model_type')->nullable();
            $table->uuid('model_id')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['model_type', 'model_id']);
        });

        // Create sessions table with UUID
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create mix_designs table with UUID
        Schema::create('mix_designs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('mix_id')->unique();
            $table->string('name');
            $table->string('class_strength');
            $table->decimal('target_strength', 10, 2);
            $table->decimal('slump', 10, 2);
            $table->string('durability_class')->nullable();
            $table->string('exposure_class')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('version')->default('1.0');
            $table->uuid('created_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Create mix_design_compositions table with UUID
        Schema::create('mix_design_compositions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mix_design_id');
            $table->string('material_type');
            $table->string('material_name');
            $table->decimal('percentage', 8, 4)->nullable();
            $table->decimal('weight_per_m3', 10, 2);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->boolean('is_admixture')->default(false);
            $table->boolean('is_water')->default(false);
            $table->boolean('is_cement')->default(false);
            $table->boolean('is_aggregate')->default(false);
            $table->string('aggregate_size')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('mix_design_id')->references('id')->on('mix_designs')->onDelete('cascade');
        });

        // Create accounts table with UUID
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('sub_type')->nullable();
            $table->uuid('parent_id')->nullable();
            $table->integer('level')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        // Create journal_entries table with UUID
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entry_number')->unique();
            $table->date('entry_date');
            $table->text('description');
            $table->string('reference')->nullable();
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->uuid('created_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Create journal_entry_lines table with UUID
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_entry_id');
            $table->uuid('account_id');
            $table->text('description')->nullable();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->integer('line_number');
            $table->timestamps();

            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all tables in reverse order
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('mix_design_compositions');
        Schema::dropIfExists('mix_designs');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('user_invitations');
        Schema::dropIfExists('user_modules');
        Schema::dropIfExists('module_recommendations');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('users');
        Schema::dropIfExists('business_types');
    }
};
