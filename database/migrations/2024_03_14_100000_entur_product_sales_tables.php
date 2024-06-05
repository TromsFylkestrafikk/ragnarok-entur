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
            $table->bigInteger('group_id')->comment("The GL Batch ID used to select the dataset");
            $table->string('chunk_id')->comment("Help identify the data associated with the chunk");

            $table->string('accounting_month')->comment("Accounting month and year");
            $table->string('organisation')->comment("The Organisation number specified on the Sales Agreement");
            $table->string('agreement_ref')->comment("The ID of the Agreement under which the line is produced");
            $table->string('agreement_description')->comment("The name of the Agreement");

            $table->string('distribution_channel_ref')->comment("ID of the Distribution Channel");

            $table->integer('pos_provider_ref')->comment("The Org ID that operates the POS on behalf of the supplier (normally an Operator)"); 
            $table->integer('pos_supplier_ref')->comment("The supplier of this POS, this is usually the Authority Org ID that the Operator has a transport
            agreement with"); 
            $table->string('pos_ref')->comment("ID of the Point Of Sale");
            $table->string('pos_name')->nullable()->comment("NOTE: No data in source");
            $table->string('pos_location_ref')->nullable()->comment("NOTE: No data in source. Unique ID of the POS Location");
            $table->string('pos_location_name')->nullable()->comment("NOTE: No data in source. Name of the POS location");
            $table->string('pos_privatecode')->nullable()->comment("NOTE: No data in source. External reference for the POS, unique for the Organisation");
            $table->string('transaction_type')->comment("The type of the transaction being cleared");

            $table->uuid('sales_orderline_id')->comment("The ID of the specific OrderLine in the summary for reference");
            $table->uuid('sales_fare_product_id')->comment("Unique ID of each FareProduct in the Order");
            $table->string('sales_order_id')->comment("The order ID of the sales transaction. Unique in combination with ORDER_VERSION");
            $table->integer('sales_order_version')->comment("The order version of the sales transaction");
            $table->string('sales_payment_type')->comment("The payment types used for payment. This may be a comma separated list for split payments");
            $table->string('sales_external_reference')->comment("NOTE: No data in source. The External Reference provided")->nullable();
            $table->date('sales_date')->comment("The Sales Date of the Transaction");
            $table->string('sales_privatecode')->comment("NOTE: no data in source. The Private Code provided (ticket number within the order)")->nullable();
            $table->string('sales_package_ref')->comment("The ID of the Sales Package");
            $table->string('sales_package_name')->comment("The name of the Sales Package");
            $table->string('sales_discount_right_ref')->comment("NOTE: No data in source. The ID of the Sales Discount if applied.")->nullable();
            $table->string('sales_discount_right_name')->comment("NOTE: No data in source. The name of the Sales Discount if applied")->nullable();
            $table->string('sales_user_profile_ref')->comment("The ID of any associated User Profile. (product ref?)");
            $table->string('sales_user_profile_name')->comment("The name of any associated User Profile. (product name?)");
            $table->dateTime('sales_start_time')->comment("The travel start DateTime, where relevant");
            $table->string('sales_from_stop_place')->comment("The departing stop place, where relevant (ref).")->nullable();
            $table->string('sales_from_stop_place_name')->comment("The departing stop place, where relevant (name).")->nullable();
            $table->string('sales_to_stop_place')->comment("The destination stop place, where relevant (ref)")->nullable();
            $table->string('sales_to_stop_place_name')->comment("The destination stop place, where relevant (name)")->nullable();
            $table->integer('sales_zone_count')->comment("The travel distance in zones, where relevant")->nullable();
            $table->string('sales_zones_ref')->comment("The list of traversed zones, where relevant")->nullable();
            $table->string('sales_interval_distance')->comment("NOTE: No data ion source. The travel interval distance, where Interval information is available")->nullable();
            $table->string('sales_leg_servicejourney_ref')->comment("Leg information, if available")->nullable();
            $table->integer('sales_leg_servicejourney_pcode')->comment("Leg information, if available")->nullable();
            $table->string('sales_leg_line_publiccode')->comment("NOTE: No data in sourceLeg information, if available")->nullable();
            $table->string('sales_leg_line_ref')->comment("Leg information, if available")->nullable();
            $table->string('sales_leg_line_name')->comment("NOTE: No data in source. Leg information, if available")->nullable();

            $table->uuid('annex_transient_guid')->comment("An internal transient GUID assigned to this specific Annex during clearing");
            $table->string('annex_description')->comment("NOTE: No data in source. Any description associated with the Annex")->nullable();
            $table->string('annex_occurs')->nullable("NOTE: No data in source. Any occurrence multiplicator already applied to the underlying values, eg Klippekort. NULL
            values imply an occurence of 1");
            $table->integer('annex_amount')->comment("The base amount split into Lines during clearing (the Price or PriceContribution, depending on
            the mapping)");
            $table->integer('annex_tax_code')->comment("The original Tax Code of the PriceContribution, if any")->nullable();
            $table->integer('annex_tax_rate')->comment("The Tax Rate of the PriceContribution for this Annex")->nullable();

            $table->biginteger('line_id')->comment("Line ID");
            $table->date('line_accounting_date')->comment("The Accounting Date of the line");
            $table->string('line_category_ref')->comment("The ID of the CLEOS Mapping used to produce the line");
            $table->string('line_category_description')->comment("The name of the CLEOS Mapping used to produce the line");
            $table->integer('line_amount')->comment("The General Ledger amount represented by the line");
            $table->integer('line_cancellation')->comment("TRUE if the line is a cancellation");
            $table->integer('line_standard_tax_code')->comment("The Standard Tax Code associated with the Local Tax Code used");
            $table->integer('line_local_tax_code')->comment("The Local Tax Code used during clearing");
            $table->integer('line_local_tax_rate')->comment("The Local Tax Rate used during clearing");
            $table->float('line_tax_amount')->comment("The Ad-Hoc calculated Tax Amount, LINE_AMOUNT * LINE_LOCAL_TAXRATE");

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
