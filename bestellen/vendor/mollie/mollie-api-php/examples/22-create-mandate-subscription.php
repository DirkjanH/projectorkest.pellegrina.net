<?php
try
{
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "initialize.php";
    /*
     * Customer creation parameters.
     *
     * See: https://docs.mollie.com/reference/v1/customers-api/create-customer
     */
    $membership_id = 1;

    $customer = $mollie->customers->create(array(
        "name"     => 'Example name',
        "email"    => 'info@example.com',
    ));
    echo "<p>Customer created with id ". $customer->id."</p>";

    // create mandate
    $mandate = $mollie->customers_mandates->withParentId($customer->id)->create(array(
        "method" => 'directdebit',
        "consumerAccount" => 'NL34ABNA0243341423',
        "consumerName" => 'B. A. Example',
    ));
    echo "<p>Mandate created with id ". $mandate->id."</p>";

    // set recurring
    $subscription = $mollie->customers_subscriptions->withParentId($customer->id)->create(array(
        "amount"      => 10.00,
        "times"       => 12, // recurring membership for 1 year
        "interval"    => "1 months", // every month
        "description" => "Subscription ".$membership_id,
        "webhookUrl"  => "https://example.com/webhook.php",
        "metadata" => array(
            "order_id" => $membership_id,
        ),
    ));

    echo "<p>Subscription created with id ". $subscription->id."</p>";
}
catch (Mollie_API_Exception $e)
{
    error_log( "API call failed: " . htmlspecialchars($e->getMessage()));
}
