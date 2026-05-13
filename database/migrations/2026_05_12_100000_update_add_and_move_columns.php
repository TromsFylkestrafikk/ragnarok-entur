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
            $table->bigInteger('row_id')->after('id')->comment("External row ID");
            $table->bigInteger('acct_month_id')->after('chunk_id')->comment("Accounting month ID");
            $table->string('agreement_owner_org_ref')->after('acct_month');
            $table->string('agreement_owner_org_name')->after('agreement_owner_org_ref');
            $table->string('agreement_id')->after('agreement_owner_org_name');
            $table->string('agreement_code')->after('agreement_name');
            $table->string('distribution_channel_name')->after('distribution_channel_ref');
            $table->string('pos_provider_name')->after('pos_provider_ref');
            $table->string('pos_supplier_name')->after('pos_supplier_ref');;
            $table->string('settlement_number')->after('pos_privatecode');
            $table->string('settlement_external_number')->after('settlement_number');
            $table->string('settlement_external_date')->after('settlement_external_number');
            $table->string('transaction_timestamp')->after('transaction_type');
            $table->string('fare_product_ref')->after('fare_product_id');
            $table->string('fare_product_name')->after('fare_product_ref');
            $table->string('group_json')->after('user_profile_name');
            $table->string('fare_product_authority_ref')->after('discount_right_name');
            $table->string('fare_product_authority_name')->after('fare_product_authority_ref');
            $table->string('usage_validity_ref')->after('fare_product_authority_name');
            $table->string('usage_validity_name')->after('usage_validity_ref');
            $table->string('entitlement_given_ref')->after('usage_validity_name');
            $table->string('entitlement_given_name')->after('entitlement_given_ref');
        });

        /*Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->string('chunk_id')->change()->after('id');
            $table->string('agreement_org_no')->change()->after('agreement_owner_org_name');
            $table->date('settlement_date')->change()->after('settlement_number');
            $table->string('payment_type')->change()->after('transaction_timestamp');
            $table->string('order_id')->change()->after('payment_type');
            $table->integer('order_version')->change()->after('order_id');
            $table->string('external_reference')->change()->after('order_version');
            $table->string('sales_package_ref')->change()->after('orderline_id');
            $table->string('sales_package_name')->change()->after('sales_package_ref');
            $table->string('sales_package_privatecode')->change()->after('sales_package_name');
            $table->string('discount_right_ref')->change()->after('group_json');
            $table->string('discount_right_name')->change()->after('discount_right_ref');
            $table->string('fare_product_authority_ref')->change()->after('discount_right_name');
            $table->string('fare_product_authority_name')->change()->after('fare_product_authority_ref');
            $table->string('usage_validity_ref')->change()->after('fare_product_authority_name');
            $table->string('usage_validity_name')->change()->after('usage_validity_ref');
            $table->string('entitlement_given_ref')->change()->after('usage_validity_name');
            $table->string('entitlement_given_name')->change()->after('entitlement_given_ref');
            $table->dateTime('journey_start_time')->change()->after('leg_to_name');
            $table->string('interval_zones')->change()->after('interval_distance');
            $table->integer('annex_tax_code')->change()->after('annex_tax_rate');
            $table->string('acct_amount')->change()->after('clearing_cancellation');
            $table->date('acct_date')->change()->after('acct_amount');
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};