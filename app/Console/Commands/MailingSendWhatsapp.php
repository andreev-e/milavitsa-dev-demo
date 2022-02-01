<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingMessage;
use App\Services\IDigitalService;

class MailingSendWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:sendWhatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $content = [];
        $messages = MailingMessage::where('channel', 'whatsapp')
            ->where('status', 'new')->limit(1000)->get();
        foreach ($messages as $message) {
            if (!empty($message->client->phone)) {
                if (!$message->client->isBlackListed()) {
                    $phone = $message->client->phone[0];
                    $text = $message->mailingList->text;
                    $template = $message->mailingList->whatsapp_teplate;
                    $message->status = 'sending';
                    $message->save();

                    // TODO: delete next line
                    $phone = '+79168874415';

                    $content[] = [
                        "channelType" => "WHATSAPP",
                        "senderName" => "MilaVitsa",
                        "destination" => $phone,
                        "callbackUrl" => config('app.url') . '/api'. config('idgtl.callback_url'),
                        "callbackEvents" => [
                            "sent",
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
                            "buttons" => config('whatsapptemplates.templates.' . $template . '.buttons'),
                        ]
                    ];

                } else {
                    $message->status = 'black';
                    $message->save();
                    $message->queueNext();
                }
            } else {
                $message->status = 'failed';
                $message->save();
                $message->queueNext();
            }
        }

        try {
            $api = new IDigitalService;
            echo $api->whatsappSendBulk($content);
        } catch(\Exception $e) {
            //TODO fail all messages
        }
        return 0;
    }
}
