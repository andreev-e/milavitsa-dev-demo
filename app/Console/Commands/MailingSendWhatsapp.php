<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingMessage;

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
        $messages = MailingMessage::where('channel', 'whatsapp')->where('status', 'new')->limit(100)->get();
        foreach ($messages as $message) {
            if (!empty($message->client->phone)) {
                if (!$message->client->isBlackListed()) {
                    $email_addr = $message->client->phone[0];
                    $text = $message->mailingList->text;
                    try {
                        // TODO:
                        throw new \ErrorException();
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
            }
        }
        return 0;
    }
}
