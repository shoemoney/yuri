<?php

namespace App\Indodax\Helpers;

use GuzzleHttp\Client;

class Helper
{
   /**
   * api
   *
   * @param  mixed $method
   * @param  mixed $url
   * @param  mixed $form_data
   * @return void
   */
  public static function api($method, $url, $form_data = [])
  {
      $client = new Client(['verify' => false]);
      $response = $client->request($method, $url, $form_data);

      return json_decode($response->getBody()->getContents());
  }

  /**
   * @param mixed $endpoint
   * @param mixed $method
   * @param mixed $serverTime
   * @param null $apiKey
   * @param null $secretKey
   *
   * @return object
   */
  public static function curl($endpoint, $data, $apiKey = null, $secretKey = null)
  {
        $post_data = http_build_query($data, '', '&');
        $sign = hash_hmac('sha512', $post_data, $secretKey);
        $headers = ['Key:'.$apiKey,'Sign:'.$sign];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => $endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
  }
}
