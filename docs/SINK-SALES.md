# Entur Sales

Sales documentation per agreement.

## Data

Entur sales provides daily sales data. Including agreement, distribution channel, type of ticket, tickets sold, stop places if indicated, sales time and sum paied. 

## Source

Entur is the national registry for all public transport in Norway. The registry contains data about daily departures and routes

## Usage

Get an overview of the amount and type of tickets sold on a daily basis. Ticket types can be found in `sales_package_name` and include among others `enkeltbillett`, `enkeltbillett hurtigbåt` and `30-dagers billett`. `sales_user_profile_name` describes the type of users that purchased the ticket, such as  `voksen 30-66 år`, `honnør` or `barn`. The schema description describes the values in each field.

This sink consist of one table called `entur_product_sales`. 
