<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingList;
use Carbon\Carbon;

class MailingWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:work';

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
        $this->info('Проверка рассылок');
        $readytosend = MailingList::where('status', 'submitted')
            ->where('start', '<', Carbon::now())->get();
        dd(count($readytosend));
        foreach($readytosend as $list) {
            $list->status = 'processing';

            $list->save();
        }
        return 0;
    }
}
