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
            $table->renameColumn('group_id', 'gl_batch_id');
            $table->renameColumn('sales_orderline_id', 'orderline_id');
            $table->renameColumn('sales_fare_product_id', 'fare_product_id');


            $table->renameColumn('accounting_month', 'acct_month');
            $table->renameColumn('organisation', 'agreement_org_no');
            $table->renameColumn('agreement_description', 'agreement_name');
            $table->renameColumn('sales_external_reference', 'external_reference');
            $table->renameColumn('sales_date', 'settlement_date');
            $table->renameColumn('sales_privatecode', 'sales_package_privatecode');
            $table->renameColumn('sales_discount_right_ref', 'discount_right_ref');
            $table->renameColumn('sales_discount_right_name', 'discount_right_name');
            $table->renameColumn('sales_user_profile_ref', 'user_profile_ref');
            $table->renameColumn('sales_user_profile_name', 'user_profile_name');
            $table->renameColumn('sales_start_time', 'journey_start_time');
            $table->renameColumn('sales_from_stop_place', 'leg_from_ref');
            $table->renameColumn('sales_from_stop_place_name', 'leg_from_name');
            $table->renameColumn('sales_to_stop_place', 'leg_to_place');
            $table->renameColumn('sales_to_stop_place_name', 'leg_to_name');
            $table->renameColumn('sales_zone_count', 'interval_zone_count');
            $table->renameColumn('sales_zones_ref', 'interval_zones');
            $table->renameColumn('sales_interval_distance', 'interval_distance');
            $table->renameColumn('sales_leg_servicejourney_ref', 'leg_servicejourney');
            $table->renameColumn('sales_leg_servicejourney_pcode', 'leg_servicejourney_pcode');
            $table->renameColumn('sales_leg_line_publiccode', 'leg_line_publiccode');
            $table->renameColumn('sales_leg_line_ref', 'acct_leg_line_ref');
            $table->renameColumn('sales_leg_line_name', 'acct_leg_line_name');
            $table->renameColumn('line_accounting_date', 'acct_date');
            $table->renameColumn('line_category_ref', 'clearing_mapping_ref');
            $table->renameColumn('line_category_description', 'clearing_mapping_name');

            $table->renameColumn('line_cancellation', 'clearing_cancellation');
            $table->renameColumn('line_standard_tax_code', 'acct_standard_tax_code');
            $table->renameColumn('line_local_tax_code', 'acct_local_tax_code');
            $table->renameColumn('line_local_tax_rate', 'acct_local_tax_rate');
            $table->renameColumn('line_tax_amount', 'est_tax_amount');

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