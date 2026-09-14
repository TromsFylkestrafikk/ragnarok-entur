<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->string('special_order_cause')->nullable();
            $table->string('special_order_org_ref')->nullable();
            $table->string('special_order_org_name')->nullable();
            $table->string('special_order_related_id')->nullable();
        });

        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->renameColumn('est_tax_amount', 'tax_amount');
            $table->renameColumn('pos_ref', 'pos_internalref');
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
