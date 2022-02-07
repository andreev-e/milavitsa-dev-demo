<?php

namespace App\Console\Commands;

use App\Models\LinkCounter;
use Illuminate\Console\Command;
use App\Models\MailingMessage;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mailing;

class MailingSendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:sendEmail';

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
        $messages = MailingMessage::where('channel', 'email')->where('status', 'new')->limit(100)->get();
        foreach ($messages as $message) {
            if (!empty($message->client->email)) {
                if (!$message->client->isBlackListed()) {
                    $email_addr = $message->client->email[0];
                    $text = LinkCounter::shortenLinks($message->mailingList->text, $message->id);
                    $template = $message->mailingList->email_teplate ? $message->mailingList->email_teplate : 'default';
                    $subj = $message->mailingList->name;
                    try {
                        // TODO: delete next line
                        $email_addr = 'andreev-e@mail.ru';
                        Mail::to($email_addr)->send(new Mailing($text, $subj, $template, $message->id));
                        $message->status = 'ok';
                        $message->save();
                    } catch(\Exception $e) {
                        echo $e->getMessage();
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
