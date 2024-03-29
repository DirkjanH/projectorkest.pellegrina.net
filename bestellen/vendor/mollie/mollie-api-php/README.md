![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png) 

# Mollie API client for PHP #

Accepting [iDEAL](https://www.mollie.com/en/payments/ideal/), [Bancontact/Mister Cash](https://www.mollie.com/en/payments/bancontact/), [SOFORT Banking](https://www.mollie.com/en/payments/sofort/), [Creditcard](https://www.mollie.com/en/payments/credit-card/), [SEPA Bank transfer](https://www.mollie.com/en/payments/bank-transfer/), [SEPA Direct debit](https://www.mollie.com/en/payments/direct-debit/), [Bitcoin](https://www.mollie.com/en/payments/bitcoin/), [PayPal](https://www.mollie.com/en/payments/paypal/), [Belfius Direct Net](https://www.mollie.com/en/payments/belfius/), [KBC/CBC](https://www.mollie.com/en/payments/kbc-cbc/), [paysafecard](https://www.mollie.com/en/payments/paysafecard/), [ING Home'Pay](https://www.mollie.com/en/payments/ing-homepay/), [Giftcards](https://www.mollie.com/en/payments/gift-cards/), [Giropay](https://www.mollie.com/en/payments/giropay/) and [EPS](https://www.mollie.com/en/payments/eps/) online payments without fixed monthly costs or any punishing registration procedures. Just use the Mollie API to receive payments directly on your website or easily refund transactions to your customers.

[![Build Status](https://travis-ci.org/mollie/mollie-api-php.png)](https://travis-ci.org/mollie/mollie-api-php)
[![Latest Stable Version](https://poser.pugx.org/mollie/mollie-api-php/v/stable)](https://packagist.org/packages/mollie/mollie-api-php)
[![Total Downloads](https://poser.pugx.org/mollie/mollie-api-php/downloads)](https://packagist.org/packages/mollie/mollie-api-php)

## Requirements ##
To use the Mollie API client, the following things are required:

+ Get yourself a free [Mollie account](https://www.mollie.com/signup). No sign up costs.
+ Now you're ready to use the Mollie API client in test mode.
+ Follow [a few steps](https://www.mollie.com/dashboard/?modal=onboarding) to enable payment methods in live mode, and let us handle the rest.
+ PHP >= 5.3
+ PHP cURL extension
+ Up-to-date OpenSSL (or other SSL/TLS toolkit)
+ SSL v3 disabled. Mollie does not support SSL v3 anymore.

## Installation ##

By far the easiest way to install the Mollie API client is to require it with [Composer](http://getcomposer.org/doc/00-intro.md).

    $ composer require mollie/mollie-api-php:1.9.*

    {
        "require": {
            "mollie/mollie-api-php": "^1.9"
        }
    }

You may also git checkout or [download all the files](https://github.com/mollie/mollie-api-php/archive/master.zip), and include the Mollie API client manually.

## How to receive payments ##

To successfully receive a payment, these steps should be implemented:

1. Use the Mollie API client to create a payment with the requested amount, description and optionally, a payment method. It is important to specify a unique redirect URL where the customer is supposed to return to after the payment is completed.

2. Immediately after the payment is completed, our platform will send an asynchronous request to the configured webhook to allow the payment details to be retrieved, so you know when exactly to start processing the customer's order.

3. The customer returns, and should be satisfied to see that the order was paid and is now being processed.

## Getting started ##

Requiring the included autoloader. If you're using Composer, you can skip this step.

```php
require "Mollie/API/Autoloader.php";
```

Initializing the Mollie API client, and setting your API key.

```php
$mollie = new Mollie_API_Client;
$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
``` 

Creating a new payment.

```php
$payment = $mollie->payments->create(array(
    "amount"      => 10.00,
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
));
```

_After creation, the payment id is available in the `$payment->id` property. You should store this id with your order._

Retrieving a payment.

```php
$payment = $mollie->payments->get($payment->id);

if ($payment->isPaid())
{
    echo "Payment received.";
}
```

### Fully integrated iDEAL payments ###

If you want to fully integrate iDEAL payments in your web site, some additional steps are required. First, you need to
retrieve the list of issuers (banks) that support iDEAL and have your customer pick the issuer he/she wants to use for
the payment.

Retrieve the list of issuers:

```php
$issuers = $mollie->issuers->all();
```

_`$issuers` will be a list of `Mollie_API_Object_Issuer` objects. Use the property `$id` of this object in the
 API call, and the property `$name` for displaying the issuer to your customer. For a more in-depth example, see [Example 4](https://github.com/mollie/mollie-api-php/blob/master/examples/04-ideal-payment.php)._

Create a payment with the selected issuer:

```php
$payment = $mollie->payments->create(array(
    "amount"      => 10.00,
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
    "method"      => Mollie_API_Object_Method::IDEAL,
    "issuer"      => $selected_issuer_id, // e.g. "ideal_INGBNL2A"
));
```

_The `links` property of the `$payment` object will contain a string `paymentUrl`, which is a URL that points directly to the online banking environment of the selected issuer._

### Refunding payments ###

The API also supports refunding payments. Note that there is no confirmation and that all refunds are immediate and
definitive. Refunds are only supported for iDEAL, credit card, Bancontact/Mister Cash, SOFORT Banking, PayPal, Belfius Direct Net and bank transfer payments. Other types of payments cannot
be refunded through our API at the moment.

```php
$payment = $mollie->payments->get($payment->id);

// Refund € 15 of this payment
$refund = $mollie->payments->refund($payment, 15.00);
```

## How to use OAuth2 to connect Mollie accounts to your application? ##

The resources `permissions`, `organizations`, `refunds`, `profiles`, `settlements` and `invoices` are only available with an OAuth2 access token. This is because an API key is linked to a website profile, and those resources are linked to an Mollie account. Visit our [API documentation](https://docs.mollie.com/oauth/overview) for more information about how to get an OAuth2 access token. For an example of how to use those resources, see [Example 8](https://github.com/mollie/mollie-api-php/blob/master/examples/08-oauth-list-profiles.php), [Example 9](https://github.com/mollie/mollie-api-php/blob/master/examples/09-oauth-list-settlements.php) and [Example 10](https://github.com/mollie/mollie-api-php/blob/master/examples/10-oauth-new-payment.php).

## API documentation ##
If you wish to learn more about our API, please visit the [Mollie Developer Portal](https://www.mollie.com/en/developers/). API Documentation is available in both Dutch and English.

## Want to help us make our API client even better? ##

Want to help us make our API client even better? We take [pull requests](https://github.com/mollie/mollie-api-php/pulls?utf8=%E2%9C%93&q=is%3Apr), sure. But how would you like to contribute to a [technology oriented organization](https://www.mollie.com/nl/blog/post/werken-bij-mollie-als-developer/)? Mollie is hiring developers and system engineers. [Check out our vacancies](https://jobs.mollie.com/) or [get in touch](mailto:personeel@mollie.com).

## License ##
[BSD (Berkeley Software Distribution) License](https://opensource.org/licenses/bsd-license.php).
Copyright (c) 2013-2017, Mollie B.V.

## Support ##
Contact: [www.mollie.com](https://www.mollie.com) — info@mollie.com — +31 20 820 20 70

+ [More information about iDEAL via Mollie](https://www.mollie.com/en/payments/ideal/)
+ [More information about Credit card via Mollie](https://www.mollie.com/en/payments/credit-card/)
+ [More information about Bancontact/Mister Cash via Mollie](https://www.mollie.com/en/payments/bancontact/)
+ [More information about SOFORT Banking via Mollie](https://www.mollie.com/en/payments/sofort/)
+ [More information about SEPA Bank transfer via Mollie](https://www.mollie.com/en/payments/bank-transfer/)
+ [More information about SEPA Direct debit via Mollie](https://www.mollie.com/en/payments/direct-debit/)
+ [More information about Bitcoin via Mollie](https://www.mollie.com/en/payments/bitcoin/)
+ [More information about PayPal via Mollie](https://www.mollie.com/en/payments/paypal/)
+ [More information about Belfius Direct Net via Mollie](https://www.mollie.com/en/payments/belfius/)
+ [More information about KBC/CBC via Mollie](https://www.mollie.com/en/payments/kbc-cbc/)
+ [More information about paysafecard via Mollie](https://www.mollie.com/en/payments/paysafecard/)
+ [More information about ING Home’Pay via Mollie](https://www.mollie.com/en/payments/ing-homepay/)
+ [More information about Giftcards via Mollie](https://www.mollie.com/en/payments/gift-cards/)
+ [More information about Giropay via Mollie](https://www.mollie.com/en/payments/giropay/)
+ [More information about EPS via Mollie](https://www.mollie.com/en/payments/eps/)
