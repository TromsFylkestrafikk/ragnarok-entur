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
            //$table->id('id');
            $table->uuid('sales_orderline_id');
            $table->uuid('sales_fare_product_id');
            $table->string('distribution_channel_ref');
            $table->string('sales_order_id')->nullable();
            $table->integer('sales_order_version')->nullable();
            $table->string('sales_payment_type')->nullable();
            $table->date('sales_date')->nullable();
            
            $table->string('sales_package_ref')->nullable();
            $table->string('sales_package_name')->nullable();
            
            $table->string('sales_user_profile_ref')->nullable();
            $table->string('sales_user_profile_name')->nullable();

            $table->dateTime('sales_start_time')->nullable();
            
            $table->string('sales_from_stop_place')->nullable();
            $table->string('sales_from_stop_name')->nullable();
            $table->string('sales_top_stop_place')->nullable();
            $table->string('sales_top_stop_place_name')->nullable();

            $table->integer('sales_zone_count')->nullable();
            $table->string('sales_zones_ref')->nullable();

            $table->float('annex_amount')->nullable();

            $table->primary(['sales_orderline_id', 'sales_fare_product_id']);
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
