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
            $table->renameColumn('sales_order_id', 'order_id');
            $table->renameColumn('sales_order_version', 'order_version');
            $table->renameColumn('sales_fare_product_id', 'fare_product_id');
            $table->renameColumn('sales_payment_type', 'payment_type');
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
            $table->renameColumn('sales_to_stop_place', 'leg_to_ref');
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

        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->bigInteger('row_id')->after('id')->comment("External row ID");
            $table->bigInteger('acct_month_id')->after('chunk_id')->comment("Accounting month ID");
            $table->string('agreement_owner_org_ref')->nullable()->after('acct_month');
            $table->string('agreement_owner_org_name')->nullable()->after('agreement_owner_org_ref');
            $table->string('agreement_id')->nullable()->after('agreement_owner_org_name');
            $table->string('agreement_code')->after('agreement_name');
            $table->string('distribution_channel_name')->nullable()->after('distribution_channel_ref');
            $table->string('pos_provider_name')->nullable()->after('pos_provider_ref');
            $table->string('pos_supplier_name')->nullable()->after('pos_supplier_ref');;
            $table->string('settlement_number')->nullable()->after('pos_privatecode');
            $table->string('settlement_external_number')->nullable()->after('settlement_number');
            $table->string('settlement_external_date')->nullable()->after('settlement_external_number');
            $table->string('transaction_timestamp')->nullable()->after('transaction_type');
            $table->string('fare_product_ref')->nullable()->after('fare_product_id');
            $table->string('fare_product_name')->nullable()->after('fare_product_ref');
            $table->string('group_json')->nullable()->after('user_profile_name');
            $table->string('fare_product_authority_ref')->nullable()->after('discount_right_name');
            $table->string('fare_product_authority_name')->nullable()->after('fare_product_authority_ref');
            $table->string('usage_validity_ref')->nullable()->after('fare_product_authority_name');
            $table->string('usage_validity_name')->nullable()->after('usage_validity_ref');
            $table->string('entitlement_given_ref')->nullable()->after('usage_validity_name');
            $table->string('entitlement_given_name')->nullable()->after('entitlement_given_ref');
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