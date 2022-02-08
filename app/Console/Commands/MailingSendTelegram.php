<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingMessage;

class MailingSendTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:sendTelegram';

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
        $messages = MailingMessage::where('channel', 'telegram')->where('status', 'new')->limit(100)->get();
        foreach ($messages as $message) {
            if (!empty($message->client->tg_id)) {
                if (!$message->client->isBlackListed()) {
                    $chat_id = $message->client->tg_id;
                    $text = $message->mailingList->text;
                    try {
                        TelegramSendMessage([
                            'chat_id' => $chat_id,
                            'text' => $text
                        ]);
                        $message->status = 'ok';
                        $message->save();
                    } catch(\Exception $e) {
                        $message->status = 'failed';
                        $message->save();
                        $message->queueNext();
                    }
                } else {
                    $message->status = 'black';
                    $message->save();
                }
            } else {
                $message->status = 'failed';
                $message->save();
                $message->queueNext();
            }
        }
        return 0;
    }
}
