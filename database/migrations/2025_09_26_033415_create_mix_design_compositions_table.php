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
        Schema::create('mix_design_compositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mix_design_id')->constrained()->onDelete('cascade');
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

            $table->index(['mix_design_id', 'material_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mix_design_compositions');
    }
};
