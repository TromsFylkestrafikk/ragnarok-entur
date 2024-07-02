# Entur Sales

Sales documentation per agreement.

## Data

Entur sales provides daily sales data. Including agreement, distribution channel, type of ticket, tickets sold, stop places if indicated, 
sales time and sum paied. 

## Source

Entur is tne national registry for all public transport in Norway. The registry contains data about daily departures and routes

## Usage

Get an overview of of number of tickets and types sold on a daily basis. Ticket types can be found in `sales_package_name` 
like enkeltbillet, enkeltbillett hurtigbåt, 30-dagers billett and more, accompanied by `sales_user_profile_name` like voksen 30-66 år
Hinnør, Barn and more. Look at schema description for field documentation.


There is one table, `entur_product_sales`. 
