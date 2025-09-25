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
        Schema::create('module_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_type_id')->constrained('business_types')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->integer('priority')->default(0); // 1=high, 2=medium, 3=low
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(['business_type_id', 'module_id'], 'unique_business_module');
            $table->index(['business_type_id']);
            $table->index(['module_id']);
            $table->index(['priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_recommendations');
    }
};
