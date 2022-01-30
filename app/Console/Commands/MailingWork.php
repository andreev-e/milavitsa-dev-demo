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
        $readytosend = MailingList::whereIn('status', ['submitted', 'continued'])
            ->where('start', '<', Carbon::now())
            ->where(function ($q) {
                $q->orWhere(function ($orWhere) {
                    $orWhere->whereNull('allow_send_from')
                        ->whereNull('allow_send_to');
                })->orWhere(function ($orWhere) {
                    $orWhere->where('allow_send_from', '<', Carbon::now()->format('H:i:s'))
                        ->where('allow_send_to', '>', Carbon::now()->format('H:i:s'));
                });
            })
            ->orderBy('start', 'asc')
            ->get();
        foreach($readytosend as $list) {
            $counter = 0;
            $this->info('Генерируем сообщения для рассылки: "' . $list->name . '"');
            dump($list->status, $list->chunk);
            foreach($list->segments as $segment) {
                foreach($segment->clients as $client) {
                    $chanel_is_not_found = true;
                    foreach ($list->selected_channels as $mailingChannel) {
                        if ($chanel_is_not_found &&
                            in_array($mailingChannel, $client->selected_channels) &&
                            ($list->chunk === null || $counter < $list->chunk || $list->status === 'continued')
                        ) {
                            $counter++;
                            if ($list->status === 'submitted' ||
                                ($list->status === 'continued' && $list->chunk < $counter)) {
                                MailingMessage::create([
                                    'channel' => $mailingChannel,
                                    'client_id' => $client->id,
                                    'mailing_list_id' => $list->id,
                                ]);
                            }
                            $chanel_is_not_found = false;
                        }
                    }
                }
            }
            if ($list->status === 'continued') {
                $list->chunk = null;
            };
            $list->status = 'sending';
            $list->save();
        }
        return 0;
    }
}
