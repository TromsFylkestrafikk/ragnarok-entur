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
            $table->bigInteger('group_id');
            $table->string('chunk_id');

            // ACCOUNTING_MONTH
            // ORGANISATION
            // AGREEMENT_REF
            // AGREEMENT_DESCRIPTION

            $table->string('distribution_channel_ref');

            // POS_PROVIDER_REF
            // POS_SUPPLIER_REF
            // POS_REF
            // POS_NAME
            // POS_LOCATION_REF
            // POS_LOCATION_NAME
            // POS_PRIVATECODE
            // TRANSACTION_TYPE

            $table->uuid('sales_orderline_id');
            $table->uuid('sales_fare_product_id');
            $table->string('sales_order_id')->nullable();
            $table->integer('sales_order_version')->nullable();
            $table->string('sales_payment_type')->nullable();
            $table->string('sales_external_reference')->nullable();
            $table->date('sales_date')->nullable();
            $table->string('sales_privatecode')->nullable();  //UNSURE of type since always empty in csv
            $table->string('sales_package_ref')->nullable();
            $table->string('sales_package_name')->nullable();
            $table->string('sales_discount_right_ref')->nullable(); //UNSURE of type since always empty in csv
            $table->string('sales_discount_right_name')->nullable(); //UNSURE of type since always empty in csv
            $table->string('sales_user_profile_ref')->nullable();
            $table->string('sales_user_profile_name')->nullable();
            $table->dateTime('sales_start_time')->nullable();           
            $table->string('sales_from_stop_place')->nullable();
            $table->string('sales_from_stop_name')->nullable();
            $table->string('sales_top_stop_place')->nullable();
            $table->string('sales_top_stop_place_name')->nullable();
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
