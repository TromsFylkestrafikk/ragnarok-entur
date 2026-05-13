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
            $table->string('chunk_id')->change()->after('id');
            $table->string('agreement_org_no')->change()->after('agreement_owner_org_name');
            $table->date('settlement_date')->change()->after('settlement_number');
            $table->string('payment_type')->change()->nullable()->after('transaction_timestamp');
            $table->string('order_id')->change()->after('payment_type');
            $table->integer('order_version')->change()->after('order_id');
            $table->string('external_reference')->change()->nullable()->after('order_version');
            $table->string('sales_package_ref')->change()->after('orderline_id');
            $table->string('sales_package_name')->change()->after('sales_package_ref');
            $table->string('sales_package_privatecode')->change()->nullable()->after('sales_package_name');
            $table->string('discount_right_ref')->change()->nullable()->after('group_json');
            $table->string('discount_right_name')->change()->nullable()->after('discount_right_ref');
            $table->string('fare_product_authority_ref')->change()->after('discount_right_name');
            $table->string('fare_product_authority_name')->change()->after('fare_product_authority_ref');
            $table->string('usage_validity_ref')->change()->after('fare_product_authority_name');
            $table->string('usage_validity_name')->change()->after('usage_validity_ref');
            $table->string('entitlement_given_ref')->change()->nullable()->after('usage_validity_name');
            $table->string('entitlement_given_name')->change()->nullable()->after('entitlement_given_ref');
            $table->dateTime('journey_start_time')->change()->after('leg_to_name');
            $table->string('interval_zones')->change()->nullable()->after('interval_distance');
            $table->integer('annex_tax_code')->change()->nullable()->after('annex_tax_rate');
            $table->string('acct_amount')->change()->after('clearing_cancellation');
            $table->date('acct_date')->change()->after('acct_amount');
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