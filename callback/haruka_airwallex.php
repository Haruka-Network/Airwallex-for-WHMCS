<?php

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

$gatewayModuleName = basename(__FILE__, '.php');
$gatewayParams = getGatewayVariables($gatewayModuleName);

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

$payload = file_get_contents('php://input');
$signature = hash_hmac('sha256', $_SERVER['HTTP_X_TIMESTAMP'] . $payload,  $gatewayParams['AirwallexWebhook']);

if (!hash_equals($signature, $_SERVER['HTTP_X_SIGNATURE'])) {
    http_response_code(400);
    exit();
}

try {
    if (isset($payload)) {
        $data = json_decode($payload, true);
        
        if ($data['name'] == 'payment_link.paid') {
            $object = $data['data']['object'];
            $invoiceId = $object['metadata']['invoice_id'];
            $invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);
            checkCbTransID($data['id']);
            echo "Pass the checkCbTransID check\n";
            logTransaction($gatewayParams['name'], $data, 'Callback successful');
            
            addInvoicePayment(
                $invoiceId,
                $data['id'],
                $object['amount'],
                0,
                $gatewayModuleName
            );
        } else {
            logTransaction($gatewayParams['name'], $data, 'Unknown Event');
            http_response_code(400);
            exit();
        }
    }

} catch (Exception $e) {
    logTransaction($gatewayParams['name'], $e, 'error-callback');
    http_response_code(400);
    echo $e;
}