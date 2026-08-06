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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained(); // Link to Material Master [cite: 53]
            $table->string('batch_number'); // From BRN [cite: 77]
            $table->decimal('quantity', 10, 2); // Current available stock
            $table->enum('location', ['Raw', 'Intermediate', 'Kitchen']); // Inventory Levels [cite: 89]
            $table->date('expiry_date')->nullable(); // For FIFO tracking [cite: 78]
            $table->decimal('unit_cost', 10, 2); // For Cost Reporting [cite: 131]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
