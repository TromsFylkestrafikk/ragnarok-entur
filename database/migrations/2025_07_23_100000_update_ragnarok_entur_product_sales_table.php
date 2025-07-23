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
            //Update provider_ref and supplier_ref to store id with namespace
            $table->string('pos_provider_ref')->change();
            $table->string('pos_supplier_ref')->change();

            //Does not exist in new report
            if (Schema::hasColumn('entur_product_sales', 'line_id')) {
                $table->dropColumn('line_id');
            }

            //Does not exist in new report - might be acct_amount?
            if (Schema::hasColumn('entur_product_sales', 'line_amount')) {
                $table->dropColumn('line_amount');
            }

            $table->string('acct_amount');
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
