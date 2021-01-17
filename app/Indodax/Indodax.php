<?php
namespace App\Indodax;

use App\Indodax\Helpers\Helper;

class Indodax
{
    protected $endpoint  = 'https://indodax.com';
    private $apiKey = null;
    private $secretKey = null;


    public function __construct()
    {
        $this->apiKey = env('INDODAX_API_KEY');
        $this->secretKey = env('INDODAX_SECRET_KEY');
    }

    /**
     * Provide server time on exchange
     *
     * @return object
     */
    public function serverTime()
    {
        return Helper::api('GET', $this->endpoint . '/api/server_time');
    }


    /**
     * Provide available pairs on exchange
     *
     * @return array
     */
    public function pairs()
    {
        return Helper::api('GET', $this->endpoint . '/api/pairs');
    }


    /**
     * Provide price increments of each pairs on exchange
     *
     * @return array
     */
    public function priceIncrements()
    {
        return Helper::api('GET', $this->endpoint . '/api/price_increments');
    }

    /**
     * Summaries
     *
     * @return array
     */
    public function summaries()
    {
        return Helper::api('GET', $this->endpoint . '/api/summaries');
    }


    /**
     * Provide Single Ticker Price on each pair in exchange
     *
     * @return array
     */
    public function ticker($pair_id)
    {
        return Helper::api('GET', $this->endpoint . '/api/ticker/' . $pair_id);
    }

    /**
     * Provide All Ticker Prices in exchange
     *
     * @return array
     */
    public function tickerAll()
    {
        return Helper::api('GET', $this->endpoint . '/api/ticker/ticker_all');
    }

    /**
     * Provide Order Book trade on each pair in exchange
     *
     * @return array
     */
    public function trades($pair_id)
    {
        return Helper::api('GET', $this->endpoint . '/api/trades/' . $pair_id);
    }

    /**
     * Provide Volume price Buy and Sell on each pair in exchange
     *
     * @return array
     */
    public function depth($pair_id)
    {
        return Helper::api('GET', $this->endpoint . '/api/depth/' . $pair_id);
    }

    /**
     * Get account info
     *
     * @return object
     */
    public function getInfo()
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'getInfo',
	        'timestamp' => $timestamp,
	        'recvWindow' => $timestamp
        ], $this->apiKey, $this->secretKey);
    }

    /**
     * Get account transaction history
     *
     * @return object
     */
    public function transactionHistory()
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'transHistory',
	        'timestamp' => $timestamp,
	        'recvWindow' => $timestamp
        ], $this->apiKey, $this->secretKey);
    }


    /**
     * @param mixed $type
     * @param mixed $pair_1
     * @param mixed $pair_2
     * @param mixed $price
     * @param string $price_pair_1
     * @param string $price_pair_2
     *
     * @return object
     */
    public function trade($type, $pair_1, $pair_2, $price, $price_pair_1 = '', $price_pair_2 = '')
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'trade',
            'timestamp' => $timestamp,
            'recvWindow' => $timestamp,
            'pair' => $pair_1 . '_' . $pair_2,
            'type' => $type,
            'price' => $price,
            $pair_1 => $price_pair_1,
            $pair_2 => $price_pair_2
        ], $this->apiKey, $this->secretKey);
    }

    /**
     * @param string $pair
     * @param string $since
     * @param string $end
     * @param string $order
     * @param string $count
     * @param string $from_id
     * @param string $end_id
     *
     * @return object
     */
    public function tradeHistory($pair = '', $since = '', $end = '', $order = '', $count = '', $from_id = '', $end_id = '')
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'tradeHistory',
            'timestamp' => $timestamp,
            'recvWindow' => $timestamp,
            'count' => $count,
            'from_id' => $from_id,
            'end_id' => $end_id,
            'order' => $order,
            'since' => $since,
            'end' => $end,
            'pair' => $pair
        ], $this->apiKey, $this->secretKey);
    }

    /**
     * @param mixed $pair
     *
     * @return object
     */
    public function openOrders($pair)
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'openOrders',
	        'timestamp' => $timestamp,
            'recvWindow' => $timestamp,
            'pair' => $pair
        ], $this->apiKey, $this->secretKey);
    }

    /**
     * @param mixed $pair
     * @param string $count
     * @param string $from
     *
     * @return object
     */
    public function orderHistory($pair, $count = '', $from = '')
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'orderHistory',
	        'timestamp' => $timestamp,
            'recvWindow' => $timestamp,
            'pair' => $pair,
            'count' => $count,
            'from' => $from
        ], $this->apiKey, $this->secretKey);
    }

    /**
     * @param mixed $pair
     * @param mixed $order_id
     *
     * @return object
     */
    public function getOrder($pair, $order_id)
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'getOrder',
	        'timestamp' => $timestamp,
            'recvWindow' => $timestamp,
            'pair' => $pair,
            'order_id' => $order_id
        ], $this->apiKey, $this->secretKey);
    }

    /**
     * @param mixed $pair
     * @param mixed $order_id
     * @param mixed $type
     *
     * @return object
     */
    public function cancleOrder($pair, $order_id, $type)
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'cancelOrder',
	        'timestamp' => $timestamp,
            'recvWindow' => $timestamp,
            'pair' => $pair,
            'order_id' => $order_id,
            'type' => $type
        ], $this->apiKey, $this->secretKey);
    }

    public function withdrawCoin($currency, $withdraw_address, $withdraw_amount, $request_id, $withdraw_memo = '')
    {
        $timestamp = $this->serverTime()->server_time;

        return Helper::curl($this->endpoint . '/tapi', [
            'method' => 'withdrawCoin',
	        'timestamp' => $timestamp,
            'recvWindow' => $timestamp,
            'currency' => $currency,
            'withdraw_address' => $withdraw_address,
            'withdraw_amount' => $withdraw_amount,
            'withdraw_memo' => $withdraw_memo,
            'request_id' =>  $request_id
        ], $this->apiKey, $this->secretKey);
    }
}
