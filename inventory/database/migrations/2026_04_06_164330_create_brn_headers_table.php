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
        Schema::create('brn_headers', function (Blueprint $table) {
            $table->id();
            $table->string('brn_code')->unique(); // Auto-generated [cite: 69]
            $table->foreignId('supplier_id')->constrained(); // Selected manually [cite: 67, 70]
            $table->date('purchase_date'); // [cite: 71]
            $table->unsignedBigInteger('created_by')->nullable(); // [cite: 72]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brn_headers');
    }
};
