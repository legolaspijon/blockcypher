<?php

class ExchangeCurrency
{
    /**
     * Get exchange rate using chain.so
     * @param string $isoCurrencyToExchannge
     * @param string $isoCurrencyFrom
     * @return int
     */
    static public function getExchangePrice($isoCurrencyToExchannge = 'USD', $isoCurrencyFrom = 'DASH')
    {
        $isoCurrencyToExchannge = strtoupper($isoCurrencyToExchannge);
        $isoCurrencyFrom = strtoupper($isoCurrencyFrom);

        $api_url = 'https://chain.so/api/v2/get_price/'. $isoCurrencyFrom . '/' .$isoCurrencyToExchannge;
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);

        if(curl_errno($ch)){
            return false;
        }

        curl_close($ch);

        $result = json_decode($result);
        $prices = $result->data->prices;
        foreach ($prices as $info) {
            if($info->exchange = 'cryptonator'){
                return round(1 / $info->price, 8);
            }
        }

        return false;
    }
}