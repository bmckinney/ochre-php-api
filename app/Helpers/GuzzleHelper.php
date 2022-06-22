<?php
namespace App\Helpers;

class GuzzleHelper{


    public static function postJson($url,$json){

        $client = new \GuzzleHttp\Client([
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $response = $client->post($url,$json);
        $retorno = $response->getBody()->getContents();
        return json_decode($retorno,true);

    }

    public static function post($url,$body){

        $client = new \GuzzleHttp\Client();
        $reponse =  $client->request('POST', $url, [
            'form_params' => $body,
            'connect_timeout' => 15,
        ]);
        $result = $reponse->getBody();
        return $result;
    }

    public static function get($url,$timeout=10){
        try{
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url,['connect_timeout' => $timeout]);
            return $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
        }catch(\Exception $e){
            throw $e;
            // return null;

        }
    }



}
