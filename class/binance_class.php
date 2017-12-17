<?php
class Binance
{
    public $btc_value = 0.00;
    protected $base = "https://www.binance.com/api/", $api_key, $api_secret;
    public function __construct($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }
    public function buy($symbol, $quantity, $price, $type = "LIMIT")
    {
        return $this->order("BUY", $symbol, $quantity, $price, $type);
    }
    public function sell($symbol, $quantity, $price, $type = "LIMIT")
    {
        return $this->order("SELL", $symbol, $quantity, $price, $type);
    }
    public function cancel($symbol, $orderid)
    {
        return $this->signedRequest("v3/order", ["symbol" => $symbol, "orderId" => $orderid], "DELETE");
    }
    public function orderStatus($symbol, $orderid)
    {
        return $this->signedRequest("v3/order", ["symbol" => $symbol, "orderId" => $orderid]);
    }
    public function openOrders($symbol)
    {
        return $this->signedRequest("v3/openOrders", ["symbol" => $symbol]);
    }
    public function orders($symbol, $limit = 500)
    {
        return $this->signedRequest("v3/allOrders", ["symbol" => $symbol, "limit" => $limit]);
    }
    public function trades($symbol)
    {
        return $this->signedRequest("v3/myTrades", ["symbol" => $symbol]);
    }
    public function prices()
    {
        return $this->priceData($this->request("v1/ticker/allPrices"));
    }
    public function bookPrices()
    {
        return $this->bookPriceData($this->request("v1/ticker/allBookTickers"));
    }
    public function account()
    {
        return $this->signedRequest("v3/account");
    }
    public function depth($symbol)
    {
        return $this->request("v1/depth", ["symbol" => $symbol]);
    }
    public function balances($priceData = false)
    {
        $balance = $this->signedRequest("v3/account");
        if(empty($balance['balances'])){
            exit(json_encode($balance));
        }
        return $this->balanceData($balance, $priceData);
    }
    private function request($url, $params = [])
    {
        $headers[] = "User-Agent: Mozilla/4.0 (compatible; PHP Binance API)\r\n";
        $query = http_build_query($params, '', '&');
        return json_decode($this->http_request($this->base . $url . '?' . $query, $headers), true);
    }
    public function http_request($url, $headers, $data = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        $content = curl_exec($ch);
        if (curl_errno($ch)) {
            $content = false;
        }
        curl_close($ch);
        return $content;
    }
    private function signedRequest($url, $params = [])
    {
        $headers[] = "User-Agent: Mozilla/4.0 (compatible; PHP Binance API)\r\nX-MBX-APIKEY: {$this->api_key}\r\n";
        $params['timestamp'] = number_format(microtime(true) * 1000, 0, '.', '');
        $query = http_build_query($params, '', '&');
        $signature = hash_hmac('sha256', $query, $this->api_secret);
        $endpoint = "{$this->base}{$url}?{$query}&signature={$signature}";
        return json_decode($this->http_request($endpoint, $headers), true);
    }
    private function order($side, $symbol, $quantity, $price, $type = "LIMIT")
    {
        $opt = [
            "symbol" => $symbol,
            "side" => $side,
            "type" => $type,
            "price" => $price,
            "quantity" => $quantity,
            "timeInForce" => "GTC",
            "recvWindow" => 60000
        ];
        return $this->signedRequest("v3/order", $opt, "POST");
    }
    //1m,3m,5m,15m,30m,1h,2h,4h,6h,8h,12h,1d,3d,1w,1M
    public function candlesticks($symbol, $interval = "5m")
    {
        return $this->request("v1/klines", ["symbol" => $symbol, "interval" => $interval]);
    }
    private function balanceData($array, $priceData = false)
    {
        if ($priceData) $btc_value = 0.00;
        $balances = [];
        foreach ($array['balances'] as $obj) {
            $asset = $obj['asset'];
            $balances[$asset] = ["available" => $obj['free'], "onOrder" => $obj['locked'], "btcValue" => 0.00000000];
            if ($priceData) {
                if ($obj['free'] < 0.00000001) continue;
                if ($asset == 'BTC') {
                    $balances[$asset]['btcValue'] = $obj['free'];
                    $btc_value += $obj['free'];
                    continue;
                }
                $btcValue = number_format($obj['free'] * $priceData[$asset . 'BTC'], 8, '.', '');
                $balances[$asset]['btcValue'] = $btcValue;
                $btc_value += $btcValue;
            }
        }
        if ($priceData) {
            uasort($balances, function ($a, $b) {
                return $a['btcValue'] < $b['btcValue'];
            });
            $this->btc_value = $btc_value;
        }
        return $balances;
    }
    private function bookPriceData($array)
    {
        $bookprices = [];
        foreach ($array as $obj) {
            $bookprices[$obj['symbol']] = [
                "bid" => $obj['bidPrice'],
                "bids" => $obj['bidQty'],
                "ask" => $obj['askPrice'],
                "asks" => $obj['askQty']
            ];
        }
        return $bookprices;
    }
    private function priceData($array)
    {
        $prices = [];
        foreach ($array as $obj) {
            $prices[$obj['symbol']] = $obj['price'];
        }
        return $prices;
    }
}