# Customer Statistic

Give you statistics about the articles bought by a customer.

## Screenshot

![Take Customer Account Screenchot 1](https://github.com/thelia-modules/CustomerStatistic/blob/master/Screenshots/screenshot1.png)

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is CustomerStatistic.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/customer-statistic-module:~1.0.1
```

## Usage

Activate the module in the module list. A new row will then appear on customer edit pages,
giving you statistics about the articles they bought.

## Hook

The customer.edit hook is used to attach the statistics to the customer edit page.

## Loop

[customer.statistic.article.statistic]

### Input arguments

|Argument |Description |
|---      |--- |
|**customer_id** | ID of the customer. |

### Output arguments

|Variable   |Description |
|---        |--- |
|$PRODUCT_ID    | ID of the product |
|$REFERENCE    | Reference of the product |
|$NAME    | Name of the product |
|$UNIT_PRICE    | Price for a single unit of the product |
|$QUANTITY    | Quantity of this product bought by the customer across all of his orders |
|$TOTAL_PRICE    | $QUANTITY * $UNIT_PRICE |

### Exemple

```{loop name="customer.statistic.article.statistic" type="article.statistic"}<!-- your template -->{/loop}```
