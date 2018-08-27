# REST API SDK for PHP V2

![Home Image](homepage.jpg)

__Welcome to PayPal PHP SDK__. This repository contains PayPal's PHP SDK and samples for REST API.


This is a part of the next major PayPal SDK. It includes a simplified interface to only provide simple model objects and blueprints for HTTP calls. This repo currently contains functionality for PayPal Checkout APIs which includes Orders V2 and Payments V2.

## Please Note
> **The Payment Card Industry (PCI) Council has [mandated](http://blog.pcisecuritystandards.org/migrating-from-ssl-and-early-tls) that early versions of TLS be retired from service.  All organizations that handle credit card information are required to comply with this standard. As part of this obligation, PayPal is updating its services to require TLS 1.2 for all HTTPS connections. At this time, PayPal will also require HTTP/1.1 for all connections. [Click here](https://github.com/paypal/tls-update) for more information. Connections to the sandbox environment use only TLS 1.2.**

## Direct Credit Card Support
> **Important: The PayPal REST API no longer supports new direct credit card integrations.**  Please instead consider [Braintree Direct](https://www.braintreepayments.com/products/braintree-direct); which is, PayPal's preferred integration solution for accepting direct credit card payments in your mobile app or website. Braintree, a PayPal service, is the easiest way to accept credit cards, PayPal, and many other payment methods.

## Prerequisites

PHP 5.6 and above

An environment which supports TLS 1.2 (see the TLS-update site for more information)

## Usage

### Setting up credentials
Get client ID and client secret by going to https://developer.paypal.com/developer/applications and generating a REST API app. Get <b>Client ID</b> and <b>Secret</b> from there.

```php
use CheckoutPhpsdk\Core\PayPalHttpClient;
use CheckoutPhpsdk\Core\SandboxEnvironment;
// Creating an environment
$clientId = "AVNCVvV9oQ7qee5O8OW4LSngEeU1dI7lJAGCk91E_bjrXF2LXB2TK2ICXQuGtpcYSqs4mz1BMNQWuso1";
$clientSecret = "EDQzd81k-1z2thZw6typSPOTEjxC_QbJh6IithFQuXdRFc7BjVht5rQapPiTaFt5RC-HCa1ir6mi-H5l";

$environment = new SandBoxEnvironment($clientUd, $clientSecret);
$client = new PayPalHttpClient($environment);
```

## Examples
### Creating an Order
#### Code:
```php
// Construct a request object and set desired parameters
// Here, OrdersCreateRequest() creates a POST request to /v2/checkout/orders
$request = new OrdersCreateRequest();
$request->prefer('return=representation');
$request->body = [
                     "intent" => "CAPTURE",
                     "purchase_units" => [[
                         "reference_id" => "test_ref_id1",
                         "amount" => [
                             "value" => "100.00",
                             "currency_code" => "USD"
                         ]
                     ]],
                     "redirect_urls" => [
                          "cancel_url" => "https://example.com/cancel",
                          "return_url" => "https://example.com/return"
                     ] 
                 ];

try {
    // Call API with your client and get a response for your call
    $response = $client->execute($request);
    
    // If call returns body in response, you can get the deserialized version from the result attribute of the response
    print_r($response);
}catch (HttpException $ex) {
    echo $ex->statusCode;
    print_r($ex->getMessage());
}
```
#### Example Output:
```
Status Code: 201
Id: 8GB67279RC051624C
Intent: CAPTURE
Gross_amount:
	Currency_code: USD
	Value: 100.00
Purchase_units:
	1:
		Amount:
			Currency_code: USD
			Value: 100.00
Create_time: 2018-08-06T23:34:31Z
Links:
	1:
		Href: https://api.sandbox.paypal.com/v2/checkout/orders/8GB67279RC051624C
		Rel: self
		Method: GET
	2:
		Href: https://www.sandbox.paypal.com/checkoutnow?token=8GB67279RC051624C
		Rel: approve
		Method: GET
	3:
		Href: https://api.sandbox.paypal.com/v2/checkout/orders/8GB67279RC051624C/capture
		Rel: capture
		Method: POST
Status: CREATED
```

## Capturing an Order

### Code to Execute:
```php
// Here, OrdersCaptureRequest() creates a POST request to /v2/checkout/orders
// $response->result->id gives the orderId of the order created above
$request = new OrdersCaptureRequest($response->result->id);
$request->prefer('return=representation');
$request.requestBody({});

try {
    // Call API with your client and get a response for your call
    $response = $client->execute($request);
    
    // If call returns body in response, you can get the deserialized version from the result attribute of the response
    print_r($response);
}catch (HttpException $ex) {
    echo $ex->statusCode;
    print_r($ex->getMessage());
}
```

#### Example Output:
```
Status Code: 201
Id: 8GB67279RC051624C
Purchase_units:
	1:
		Shipping:
			Name:
				Full_name: test buyer
			Address:
				Address_line_1: 123 Townsend St
				Address_line_2: Floor 6
				Admin_area_2: San Francisco
				Admin_area_1: CA
				Postal_code: 94107
				Country_code: US
		Payments:
			Captures:
				1:
					Id: 1FH396049P053021B
					Status: COMPLETED
					Amount:
						Currency_code: USD
						Value: 100.00
					Final_capture: true
					Seller_protection:
						Status: ELIGIBLE
						Dispute_categories:
							1: ITEM_NOT_RECEIVED
							2: UNAUTHORIZED_TRANSACTION
					Seller_receivable_breakdown:
						Gross_amount:
							Currency_code: USD
							Value: 100.00
						Paypal_fee:
							Currency_code: USD
							Value: 3.20
						Net_amount:
							Currency_code: USD
							Value: 96.80
					Links:
						1:
							Href: https://api.sandbox.paypal.com/v2/payments/captures/1FH396049P053021B
							Rel: self
							Method: GET
						2:
							Href: https://api.sandbox.paypal.com/v2/payments/captures/1FH396049P053021B/refund
							Rel: refund
							Method: POST
						3:
							Href: https://api.sandbox.paypal.com/v2/checkout/orders/3L848818A2897925Y
							Rel: up
							Method: GET
					Create_time: 2018-08-06T23:39:11Z
					Update_time: 2018-08-06T23:39:11Z
Payer:
	Name:
		Given_name: test
		Surname: buyer
	Email_address: test-buyer@paypal.com
	Payer_id: KWADC7LXRRWCE
	Phone:
		Phone_number:
			National_number: 408-411-2134
	Address:
		Country_code: US
Links:
	1:
		Href: https://api.sandbox.paypal.com/v2/checkout/orders/3L848818A2897925Y
		Rel: self
		Method: GET
Status: COMPLETED
```

## Running tests

To run integration tests using your client id and secret, clone this repository and run the following command:
```sh
$ composer install
$ composer integration
```

*NOTE*: This API is still in beta, is subject to change, and should not be used in production.

## Samples

You can start off by trying out [creating and capturing an order](/samples/CaptureIntentExamples/RunAll.php)

To try out different samples for both create and authorize intent check [this link](/samples)


