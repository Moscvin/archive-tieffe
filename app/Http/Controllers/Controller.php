<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Clienti;
use App\Models\Operation;
use App\Models\Mean;
use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\ReportEquipment;
use App\CoreUsers;
use App\CoreAdminOptions;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     function sendRequest(Request $request) {
        $clientIds = CoreUsers::whereNotNull('notification_token')->pluck('notification_token');
        $apiKey = CoreAdminOptions::where('description', 'NOTIFICATION_TOKEN')->first()->value;
        $headers = [
            'Authorization' =>  'key=' . $apiKey,
            'Content-Type' => 'application/json',
        ];
                $url = 'https://fcm.googleapis.com/fcm/send';
                $body = [
                    'data' => [
                        'title' => '',
                        'message' => '',
                        'subtitle' => '',
                        'vibrate' => '',
                        'sound' => '',
                        'largeIcon'	=> '',
                        'smallIcon'	=> '',
                    ],
                    "notification" => [
                        "title" => "Pronto intervento",
                        "body" => "E' stato inserito un nuovo Pronto intervento"
                    ]
                ];
                if(count($clientIds) && count($clientIds) > 1) {
                    $body['registration_ids'] = $clientIds;
                } else {
                    $body['to'] = $clientIds[0];
                }
        $client = new \GuzzleHttp\Client();
        $promise = $client->postAsync($url, [
            'headers' => $headers,
            'json' => $body
        ])->then(function($response) {
                return (object)[
                    'result' => json_decode($response->getBody()->getContents()),
                    'status' => true
                ];
            },
            function($exception) {
                return (object)[
                    'message' => $exception,
                    'status' => false
                ];
            }
        );
        $response = $promise->wait();
        if($response->status) {
            return true;
        } else {
            return false; 
        }
    }
}
