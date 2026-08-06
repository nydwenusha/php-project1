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
        Schema::create('brn_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brn_header_id')->constrained()->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained(); // [cite: 74]
            $table->decimal('purchase_price', 10, 2); // [cite: 75]
            $table->decimal('quantity', 10, 2); // Weight or Quantity 
            $table->string('batch_number'); // Unique for the receipt [cite: 77]
            $table->date('expiry_date')->nullable(); // [cite: 78]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brn_items');
    }
};
