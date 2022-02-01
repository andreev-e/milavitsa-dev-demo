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

    public function smsSendBulk($phones, $text)
    {
        $this->token = config('idgtl.token');
        $client = new Client();
        $content = [];
        foreach ($phones as $phone) {
            $content[] = [
                "channelType" => "SMS",
                "senderName" => "MilaVitsa",
                "destination" => $phone,
                "content" => $text,
            ];
        }
        try {
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

    public function whatsappSendBulk($phones, $text, $template)
    {
        $this->token = config('idgtl.token');
        $client = new Client();
        $content = [];
        foreach ($phones as $phone) {
            $content[] = [
                "channelType" => "WHATSAPP",
                "senderName" => "MilaVitsa",
                "destination" => $phone,
                "callbackUrl" => config('idgtl.callback_url'),
                "callbackEvents" => [
                  "delivered",
                  "read",
                  "click"
                ],
                "content" => [
                    "contentType" => "text",
                    "text" => $text,
                    "header" => [
                        "text" => config('whatsapptemplates.templates.' . $template . '.header'),
                    ],
                    "footer" => [
                        "text" => config('whatsapptemplates.templates.' . $template . '.footer'),
                    ],
                    "footer" => [
                        "buttons" => config('whatsapptemplates.templates.' . $template . '.buttons'),
                    ],
                ]
            ];
        }
        try {
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
