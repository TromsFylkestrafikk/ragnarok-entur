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
            $table->bigInteger('group_id'); //check
            $table->string('chunk_id'); //check

            $table->string('accounting_month'); //check
            $table->string('organisation'); //check
            $table->string('agreement_ref'); //check
            $table->string('agreement_description'); //check

            $table->string('distribution_channel_ref'); //check

            $table->integer('pos_provider_ref'); //check
            $table->integer('pos_supplier_ref'); //check
            $table->string('pos_ref'); //check
            $table->string('pos_name')->nullable(); //check
            $table->string('pos_location_ref')->nullable(); //check
            $table->string('pos_location_name')->nullable(); //check
            $table->string('pos_privatecode')->nullable(); //check
            $table->string('transaction_type'); //check

            $table->uuid('sales_orderline_id'); //check
            $table->uuid('sales_fare_product_id'); //check
            $table->string('sales_order_id'); //check
            $table->integer('sales_order_version'); //check
            $table->string('sales_payment_type'); //check
            $table->string('sales_external_reference')->nullable(); //check
            $table->date('sales_date'); //check
            $table->string('sales_privatecode')->nullable();  //check //UNSURE of type since always empty in csv
            $table->string('sales_package_ref'); //check
            $table->string('sales_package_name'); //Check
            $table->string('sales_discount_right_ref')->nullable(); //check //UNSURE of type since always empty in csv
            $table->string('sales_discount_right_name')->nullable(); //check  //UNSURE of type since always empty in csv
            $table->string('sales_user_profile_ref');
            $table->string('sales_user_profile_name');
            $table->dateTime('sales_start_time');
            $table->string('sales_from_stop_place')->nullable(); //might be null
            $table->string('sales_from_stop_place_name')->nullable(); //might be null
            $table->string('sales_to_stop_place')->nullable(); //might be null
            $table->string('sales_to_stop_place_name')->nullable(); // might be null
            $table->integer('sales_zone_count')->nullable();
            $table->string('sales_zones_ref')->nullable();
            $table->string('sales_interval_distance')->nullable();  //Unsure what this is all about!
            $table->string('sales_leg_servicejourney_ref')->nullable();
            $table->integer('sales_leg_servicejourney_pcode')->nullable();
            $table->string('sales_leg_line_publiccode')->nullable(); //Unsure, since all values are null
            $table->string('sales_leg_line_ref')->nullable();
            $table->string('sales_leg_line_name')->nullable(); // missing in data
            $table->uuid('annex_transient_guid')->nullable();

            $table->string('annex_description')->nullable(); //missing data
            $table->string('annex_occurs')->nullable(); //missing data
            $table->float('annex_amount')->nullable(); //might be integer
            $table->float('annex_tax_code')->nullable(); //might be integer
            $table->float('annex_tax_rate')->nullable(); //might be integer

            $table->biginteger('line_id')->nullable();
            $table->date('line_accounting_date')->nullable();
            $table->string('line_category_ref')->nullable();
            $table->string('line_category_description')->nullable();
            $table->integer('line_amount')->nullable();
            $table->integer('line_cancellation')->nullable();
            $table->integer('line_standard_tax_code')->nullable();
            $table->integer('line_local_tax_code')->nullable();
            $table->integer('line_local_tax_rate')->nullable();
            $table->float('line_tax_amount')->nullable();


            

            $table->primary(['group_id', 'sales_orderline_id', 'sales_fare_product_id']);

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
