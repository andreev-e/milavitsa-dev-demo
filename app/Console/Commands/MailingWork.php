<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailingList;
use App\Models\MailingMessage;
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
        $readytosend = MailingList::where('status', 'submitted')
            ->where('start', '<', Carbon::now())
            ->orderBy('start', 'asc')
            ->get();
        foreach($readytosend as $list) {
            $this->info('Генерируем сообщения для рассылки: "' . $list->name . '"');
            foreach($list->segments as $segment) {
                foreach($segment->clients as $client) {
                    $chanel_is_not_found = true;
                    foreach ($list->selected_channels as $mailingChannel) {
                        if ($chanel_is_not_found && in_array($mailingChannel, $client->selected_channels)) {
                            // dump('Канал ' . $mailingChannel . ' есть в списке у клиента ' . $client->id . ', шлем');
                            $message = MailingMessage::create([
                                'channel' => $client->selected_channels[0],
                                'client_id' => $client->id,
                                'mailing_list_id' => $list->id,
                            ]);
                            $chanel_is_not_found = false;
                        }
                    }
                }
            }
            $list->status = 'sending';
            $list->save();
        }
        return 0;
    }
}
