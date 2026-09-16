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
        Schema::table('entur_product_sales', function (Blueprint $table) {
            // Removed from reports, and should never have been imported anyways
            if (Schema::hasColumn('entur_product_sales', 'pos_name')) {
                $table->dropColumn('pos_name');
            }

            // Removed from reports, and should never have been imported anyways
            if (Schema::hasColumn('entur_product_sales', 'pos_privatecode')) {
                $table->dropColumn('pos_privatecode');
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
