<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingMessage;
use App\Services\IDigitalService;

class MailingSendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:sendSms';

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
        $messages = MailingMessage::where('channel', 'sms')
            ->where('status', 'new')->limit(1000)->get();
        foreach ($messages as $message) {
            if (!empty($message->client->phone)) {
                if (!$message->client->isBlackListed()) {
                    $phone = $message->client->phone[0];
                    $text = $message->mailingList->text;
                    $message->status = 'sending';
                    $message->save();

                    // TODO: delete next line
                    $phone = '+79168874415';

                    $content[] = [
                        "channelType" => "SMS",
                        "senderName" => "MilaVitsa",
                        "callbackUrl" => config('app.url') . '/api'. config('idgtl.callback_url'),
                        "callbackEvents" => [
                            "sent",
                            "delivered",
                            "read",
                            "click"
                        ],
                        "destination" => $phone,
                        "content" => $text,
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
            echo $api->smsSendBulk($content);
        } catch(\Exception $e) {
            //TODO fail all messages
        }
        return 0;
    }
}
