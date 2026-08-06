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
        Schema::create('final_production_intake_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_item_id')->constrained('final_items')->onDelete('cascade');
            $table->foreignId('handler_id')->constrained('production_handlers')->onDelete('cascade');
            $table->integer('quantity');
            $table->foreignId('system_user_id')->constrained('users')->onDelete('cascade'); // Entry එක දාපු Admin/User
            $table->timestamps(); // මෙතනින් Date සහ Time auto handle වෙනවා
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_production_intake_logs');
    }
};
