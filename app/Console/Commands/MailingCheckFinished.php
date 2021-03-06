<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingList;

class MailingCheckFinished extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:checkFinished';

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
        $sending = MailingList::where('status', 'sending')->get();
            foreach ($sending as $list) {
                if ($list->messages->whereIn('status', ['new', 'sending', 'sent'])->count() === 0) {
                    if ($list->chunk === null) {
                        $list->status = 'finished';
                    } else {
                        $list->status = 'partial';
                    }
                    $list->save();
                };
            }
        return 0;
    }
}
