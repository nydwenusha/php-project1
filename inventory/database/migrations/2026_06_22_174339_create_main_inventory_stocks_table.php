<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('main_inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_item_id')->constrained('final_items')->onDelete('cascade');
            $table->integer('available_qty')->default(0); 
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('main_inventory_stocks'); }
};