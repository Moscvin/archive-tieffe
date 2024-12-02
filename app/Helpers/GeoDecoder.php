<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use App\CoreAdminOptions;
use Exception;

class GeoDecoder
{
    public function __construct($address)
    {
        $this->address = urlencode($address);
        $this->apiKey = CoreAdminOptions::where('description', 'GOOGLE_MAPS_API_KEY')->first()->value ?? '';
    }
    public function getCoors()
    {
        try {
            $client = new Client;
            $response = $client->get('https://maps.google.com/maps/api/geocode/json?address='.$this->address.'&sensor=false&key='.$this->apiKey);
            $response = json_decode($response->getBody()->getContents());
            if($response->error_message ?? false) {
                throw new Exception($response->error_message, 400);
            }
            return $response->results[0]->geometry->location ?? (object)[
                'lat' => null, 'lng' => null
            ];
        } catch(\Throwable $e) {
            return (object)[
                'lat' => null, 'lng' => null
            ];
        }
    }
}