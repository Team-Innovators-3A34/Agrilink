<?php

namespace App\Service;

use GuzzleHttp\Client;

class WeatherService
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = 'a133935176558b42031824782f9ea60d'; 
    }

    public function getWeather($city)
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$this->apiKey}&units=metric&lang=fr";

        $response = $this->client->request('GET', $url);
        return json_decode($response->getBody(), true);
    }
}
