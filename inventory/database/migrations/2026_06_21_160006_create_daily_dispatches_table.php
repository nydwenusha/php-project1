<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('daily_dispatches')->onDelete('cascade');
            $table->unsignedBigInteger('final_item_id');
            
            // Core distribution mathematics metrics
            $table->integer('carried_forward_qty')->default(0); // Leftovers from yesterday
            $table->integer('new_loaded_qty')->default(0);      // Added fresh this morning
            $table->integer('total_qty')->default(0);           // Total inventory ready for sale
            
            // Evening reconciliation parameters
            $table->integer('actual_sales')->nullable();
            $table->integer('damaged_qty')->nullable();
            $table->integer('remaining_qty')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('daily_dispatch_items'); }
};