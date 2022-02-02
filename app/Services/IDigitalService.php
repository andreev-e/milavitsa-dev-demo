<?php

namespace App\Services;

use GuzzleHttp\Client;

class IDigitalService
{
    protected
        $link = 'https://direct.i-dgtl.ru/api/v1/message',
        $token = '';

    public function smsSend($phone, $text)
    {
        $this->token = config('idgtl.token');
        $client = new Client();
        try {
            $result = $client->request('POST', $this->link,
                [
                    'json' =>
                        [
                            [
                                "channelType" => "SMS",
                                "senderName" => "MilaVitsa",
                                "destination" => $phone,
                                "content" => $text,
                            ]
                        ],
                    'headers' =>
                        [
                            'Authorization' => "Bearer {$this->token}",
                        ]
                ],
            );
            $response = $result->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function smsSendBulk($content)
    {
        $this->token = config('idgtl.token');
        $client = new Client();
        try {
            // dump($content);
            $result = $client->request('POST', $this->link,
                [
                    'json' => $content,
                    'headers' =>
                        [
                            'Authorization' => "Bearer {$this->token}",
                        ]
                ],
            );
            $response = $result->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function whatsappSendBulk($content)
    {
        $this->token = config('idgtl.token');
        $client = new Client();

        try {
            // dd($content);
            $result = $client->request('POST', $this->link,
                [
                    'json' => $content,
                    'headers' =>
                        [
                            'Authorization' => "Bearer {$this->token}",
                        ]
                ],
            );
            $response = $result->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
