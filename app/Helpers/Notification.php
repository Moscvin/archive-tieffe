<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\CoreAdminOptions;
use Illuminate\Support\Facades\Storage;

class Notification
{
    private $message = '';
    private $title = '';
    private $data = null;
    private $targets = [];

    public function __construct($targets, $message, $title = null, $data = null)
    {
        $this->message = $message;
        $this->title = $title ? $title : 'Tieffe';
        $this->data = $data ? $data : (object)[];
        $this->targets = $targets;
    }

    public function send() {
        try {
            if(!count($this->targets)) {
                return false;
            }
            $request = new \GuzzleHttp\Client();
            $promise = $request->postAsync($this->getUrl(), [
                'headers' => $this->getHeaders(),
                'json' => $this->getBody()
            ])->then(function($response) {
                    return (object)[
                        'result' => $response,
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
                Storage::put('errors/LastError.txt', $response->message);
                throw new \Exception($response->message);
            }
        } catch(\Throwable $e) {
            return $e->getMessage();
        }
    }

    private function getUrl()
    {
        return "https://fcm.googleapis.com/fcm/send";
    }

    private function getHeaders()
    {
        $apiKey = CoreAdminOptions::where('description', 'PUSH_NOTIFICATION_TOKEN')->first();
        if($apiKey) {
            return [
                'Authorization' =>  'key=' . $apiKey->value,
                'Content-Type' => 'application/json',
            ];
        }
        throw new \Exception('Cannot find PUSH_NOTIFICATION_TOKEN in options');
    }

    private function getBody()
    {
        return [
            'registration_ids' => $this->targets,
            'notification' => [
                'title' => $this->title,
                'body' => $this->message,
            ],
            'data' => $this->data,
        ];
    }
}
