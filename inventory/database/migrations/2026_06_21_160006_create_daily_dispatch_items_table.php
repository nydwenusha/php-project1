<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_dispatches', function (Blueprint $table) {
            $table->id();
            $table->date('dispatch_date');
            $table->string('gate_pass_no')->unique();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('sales_rep_id')->constrained('sales_reps');
            $table->enum('status', ['loaded', 'reconciled', 'unloaded'])->default('loaded');
            $table->unsignedBigInteger('user_id'); // Track which admin authorized the transaction
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('daily_dispatches'); }
};