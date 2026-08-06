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
    Schema::create('packing_records', function (Blueprint $table) {
        $table->id();
        $table->string('product_name');
        $table->string('batch_reference')->nullable();
        $table->string('pack_size');
        $table->integer('quantity_packed');
        $table->date('packing_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_records');
    }
};
