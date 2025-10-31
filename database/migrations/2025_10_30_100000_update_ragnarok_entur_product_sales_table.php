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
        // Drop existing compound primary key
        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->dropPrimary(['group_id', 'sales_orderline_id', 'sales_fare_product_id']);
        });

        // Add new auto-incrementing primary key
        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->increments('id')->first();
        });

        // Modify columns to be nullable
        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->uuid('sales_fare_product_id')->nullable()->change();
            $table->uuid('sales_orderline_id')->nullable()->change();
            $table->string('sales_package_name')->nullable()->change();
            $table->string('sales_package_ref')->nullable()->change();
            $table->string('sales_payment_type')->nullable()->change();
            $table->dateTime('sales_start_time')->nullable()->change();
            $table->string('sales_user_profile_name')->nullable()->change();
            $table->string('sales_user_profile_ref')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->uuid('sales_fare_product_id')->nullable(false)->change();
            $table->uuid('sales_orderline_id')->nullable(false)->change();
            $table->string('sales_package_name')->nullable(false)->change();
            $table->string('sales_package_ref')->nullable(false)->change();
            $table->string('sales_payment_type')->nullable(false)->change();
            $table->dateTime('sales_start_time')->nullable(false)->change();
            $table->string('sales_user_profile_name')->nullable(false)->change();
            $table->string('sales_user_profile_ref')->nullable(false)->change();
        });

        Schema::table('entur_product_sales', function (Blueprint $table) {
            $table->primary(['group_id', 'sales_orderline_id', 'sales_fare_product_id']);
        });
    }
};
