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
        Schema::create('mix_designs', function (Blueprint $table) {
            $table->id();
            $table->string('mix_id')->unique();
            $table->string('name');
            $table->string('class_strength');
            $table->decimal('target_strength', 8, 2);
            $table->decimal('slump', 5, 2);
            $table->string('durability_class')->nullable();
            $table->string('exposure_class')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('version')->default('1.0');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->index(['class_strength', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mix_designs');
    }
};
