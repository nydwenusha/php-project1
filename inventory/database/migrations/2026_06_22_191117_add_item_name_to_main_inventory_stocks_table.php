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
        Schema::table('main_inventory_stocks', function (Blueprint $table) {
            $table->string('item_name')->after('final_item_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('main_inventory_stocks', function (Blueprint $table) {
            $table->dropColumn('item_name');
        });
    }
};
