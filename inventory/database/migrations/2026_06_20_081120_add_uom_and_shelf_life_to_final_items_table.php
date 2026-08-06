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
        Schema::table('final_items', function (Blueprint $table) {
            $table->string('uom')->default('Pkt')->after('item_code'); // e.g., Pkt, G, Kg, Pcs
            $table->integer('shelf_life_days')->nullable()->after('selling_price'); // Optional field
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_items', function (Blueprint $table) {
            //
        });
    }
};
