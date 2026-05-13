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