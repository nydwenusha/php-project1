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
        Schema::create('kitchen_issues', function (Blueprint $table) {
            $table->id();
            $table->date('issue_date');
            $table->foreignId('raw_material_id')->constrained();
            $table->string('batch_number');
            $table->decimal('quantity', 10, 2);
            $table->string('source_level'); // 'Raw' or 'Intermediate'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_issues');
    }
};
