<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
                $email_addr = $message->client->email[0];
                $text = $message->mailingList->text;
                $template = $message->mailingList->email_teplate;
                try {
                    Mail::to($email_addr)->send(new Mailing($text, $template));
                    $message->status = 'ok';
                    $message->save();
                } catch(\Exception $e) {
                    Log::error($e);
                    $message->status = 'failed';
                    $message->save();
                    // TODO: ставим в очередь новый канал
                }
            }
        }
        return 0;
    }
}
