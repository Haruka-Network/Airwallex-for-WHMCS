<?php

use GuzzleHttp\Client;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function haruka_airwallex_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Haruka Airwallex',
        ),
        'AirwallexClientID' => array(
            'FriendlyName' => 'CLIENT ID',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '填写从 Airwallex 获取到的 CLIENT ID',
        ),
        'AirwallexAPIToken' => array(
            'FriendlyName' => 'API 密钥',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '填写从 Airwallex 获取到的 API 密钥',
        ),
        'AirwallexWebhook' => array(
            'FriendlyName' => 'Webhook 密钥',
            'Type' => 'text',
            'Size' => 30,
            'Description' => '填写从 Airwallex 获取到的 Webhook 密钥',
        ),
    );
}

function haruka_airwallex_link($params)
{
    try {
        $client = new Client(['base_uri' => 'https://api.airwallex.com/api/v1/']);

        $loginResponse = $client->request('POST', 'authentication/login', [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-client-id' => $params['AirwallexClientID'],
                'x-api-key' => $params['AirwallexAPIToken']
            ]
        ]);
        $loginData = json_decode($loginResponse->getBody(), true);
        
        $orderResponse = $client->request('POST', 'pa/payment_links/create', [
            'json' => [
                'amount' => $params['amount'],
                'currency' => $params['currency'],
                'reusable' => false,
                'title' => 'Invoice #'.$params['invoiceid'],
                'description' => $params["description"],
                'metadata' => [
                    'invoice_id' => $params['invoiceid'],
                    'original_amount' => $params['amount']
                ]
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $loginData['token']
            ]
        ]);
        $orderData = json_decode($orderResponse->getBody(), true);

        if ($orderData['active'] && $orderData['status'] == "UNPAID") {
            return '<form action="' . $orderData['url'] . '" method="get"><input type="submit" class="btn btn-primary" value="' . $params['langpaynow'] . '" /></form>';
        }
    } catch (Exception $e) {
        return '<div class="alert alert-danger text-center" role="alert">支付网关错误，请联系客服进行处理</div>';
    }
    return '<div class="alert alert-danger text-center" role="alert">发生错误，请创建工单联系客服处理</div>';
}