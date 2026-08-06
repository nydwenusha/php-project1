<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('blending_records', function (Blueprint $table) {
            $table->id();
            $table->date('blending_date'); // Date of processing [cite: 102]
            $table->foreignId('raw_material_id')->constrained(); // Material being processed [cite: 103]
            $table->string('input_batch_number'); // Batch taken from Raw Stock [cite: 103]
            $table->decimal('input_weight', 10, 2); // Original weight [cite: 104]
            $table->decimal('output_weight', 10, 2); // Weight after processing [cite: 104]
            $table->string('new_batch_number'); // New batch for Intermediate Stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blending_records');
    }
};
