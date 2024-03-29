<?php
/*
 * Example 18 - Updating an existing customer via the Mollie API.
 */

try
{
	/*
	 * Initialize the Mollie API library with your API key or OAuth access token.
	 */
	require "initialize.php";

	/*
	 * Retrieve an existing customer by his customerId
	 */
	$customer = $mollie->customers->get("cst_zAQzfr3Raq");

	/**
	 * Customer fields that can be updated.
	 *
	 * @See https://docs.mollie.com/reference/v1/customers-api/update-customer
	 */
	$customer->name = "Luke Skywalker";
	$customer->email = "luke@example.org";
	$customer->locale = "en";
	$customer->metadata->isJedi = TRUE;

	$customer = $mollie->customers->update($customer);

	echo "<p>Customer updated: " . htmlspecialchars($customer->name) . "</p>";
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
