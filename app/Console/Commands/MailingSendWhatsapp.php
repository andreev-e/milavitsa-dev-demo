<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingMessage;

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
        $messages = MailingMessage::where('channel', 'whatsapp')->where('status', 'new')->limit(100)->get();
        foreach ($messages as $message) {
            if (!empty($message->client->email)) {
                $email_addr = $message->client->email[0];
                $text = $message->mailingList->text;
                $template = $message->mailingList->email_teplate;
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
            }
        }
        return 0;
    }
}
