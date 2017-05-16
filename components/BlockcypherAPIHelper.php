<?php

use \BlockCypher\Client\PaymentForwardClient;
use \BlockCypher\Rest\ApiContext;

class BlockcypherAPIHelper
{
    /**
     * Generate forwarding address
     *
     * @param ApiContext $apiContext
     * @param string $wallet_address
     * @param array $params
     * @return BlockCypher\Api\PaymentForward
     */
    static public function generateForwardingAddress(ApiContext $apiContext, $wallet_address, $params = array())
    {
        $paymentForwardClient = new PaymentForwardClient($apiContext);
        $options = array(
            'callback_url' => $params['callbackUrl'] . '?' . http_build_query($params['data']),
            'enable_confirmations' => true
        );

        return $paymentForwardClient->createForwardingAddress($wallet_address, $options);
    }


    /**
     * Get address info
     * @param ApiContext $apiContext
     * @param string $address
     * @return \BlockCypher\Api\FullAddress
     * */
    static public function getFullAddress(ApiContext $apiContext, $address)
    {
        $addressClient = new \BlockCypher\Client\AddressClient($apiContext);
        $fullAddress = $addressClient->getFullAddress($address);

        return $fullAddress;
    }
}