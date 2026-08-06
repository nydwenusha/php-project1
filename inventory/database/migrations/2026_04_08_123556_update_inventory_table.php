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
    Schema::table('brn_items', function (Blueprint $table) {
        if (!Schema::hasColumn('brn_items', 'expiry_date')) {
            $table->date('expiry_date')->nullable()->after('batch_number');
        }
        
        if (!Schema::hasColumn('brn_items', 'avg_cost')) {
            $table->decimal('avg_cost', 15, 2)->default(0)->after('purchase_price');
        }
    });

    Schema::table('raw_materials', function (Blueprint $table) {
        if (!Schema::hasColumn('raw_materials', 'brand_id')) {
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
