<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->unsignedBigInteger('final_item_id'); // Keeps live tracking of mobile store items
            $table->integer('quantity')->default(0);     // Current physical count left inside the truck
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('vehicle_stocks'); }
};