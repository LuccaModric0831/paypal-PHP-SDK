<?php
/**
 * Created by PhpStorm.
 * User: gchockalingam
 * Date: 8/2/18
 * Time: 2:42 PM
 */

namespace Sample\AuthorizeIntentExamples;

require __DIR__ . '/../../vendor/autoload.php';
use CheckoutPhpsdk\Orders\OrdersAuthorizeRequest;
use Sample\Skeleton;


class AuthorizeOrder
{
    public static function buildRequestBody()
    {
        return "{}";
    }
    public static function authorizeOrder($orderId, $debug=false)
    {
        $request = new OrdersAuthorizeRequest($orderId);
        $request->authorization("Bearer " . Skeleton::authToken());
        $request->body = self::buildRequestBody();

        $client = Skeleton::client();
        $response = $client->execute($request);
        if ($debug)
        {
            print "Status Code: {$response->statusCode}\n";
            print "Status: {$response->result->status}\n";
            print "Order ID: {$response->result->id}\n";
            print "Authorization ID: {$response->result->purchase_units[0]->payments->authorizations[0]->id}\n";
            print "Links:\n";
            foreach($response->result->links as $link)
            {
                print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
            }
            print "Authorization Links:\n";
            foreach($response->result->purchase_units[0]->payments->authorizations[0]->links as $link)
            {
                print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
            }
            print "\nActual Response Body:\n";
            echo json_encode($response->result, JSON_PRETTY_PRINT);
        }
        return $response;
    }
}

if (!count(debug_backtrace()))
{
    AuthorizeOrder::authorizeOrder('14K47665XF157221P', true);
}