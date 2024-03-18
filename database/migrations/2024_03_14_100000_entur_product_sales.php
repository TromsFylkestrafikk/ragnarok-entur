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
        Schema::create('entur_product_sales', function (Blueprint $table) 
        {
            $table->id();
            $table->uuid('sales_orderline_id');
            $table->uuid('sales_fare_product_id');
            $table->string('distribution_channel_ref');
            $table->string('sales_order_id');
            $table->integer('sales_order_version');
            $table->string('sales_payment_type');
            $table->date('sales_date');
            
            $table->string('sales_package_ref');
            $table->string('sales_package_name');
            
            $table->string('sales_user_profile_ref');
            $table->string('sales_user_profile_name');

            $table->dateTime('sales_start_time');
            
            $table->string('sales_from_stop_place');
            $table->string('sales_from_stop_name');
            $table->string('sales_top_stop_place');
            $table->string('sales_top_stop_place_name');

            $table->integer('sales_zone_count');
            $table->string('sales_zones_ref');

            $table->float('annex_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entur_product_sales');
    }
};
