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
        if (!Schema::hasColumn('production_handlers', 'handler_code')) {
            Schema::table('production_handlers', function (Blueprint $table) {
                $table->string('handler_code', 100)->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('production_handlers', 'handler_code')) {
            Schema::table('production_handlers', function (Blueprint $table) {
                $table->dropColumn('handler_code');
            });
        }
    }
};