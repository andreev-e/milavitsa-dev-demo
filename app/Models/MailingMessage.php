<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'status',
        'client_id',
        'mailing_list_id',
    ];

    public function mailingList() {
        return $this->belongsTo(MailingList::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function queueNext() {
        $alreadyTried = self::select('channel')
            ->where('mailing_list_id', $this->mailing_list_id)
            ->where('client_id', $this->client_id)->get()->pluck('channel')->toArray();
            dump($alreadyTried);
        $chanel_is_not_found = true;
        foreach ($this->mailingList->selected_channels as $mailingChannel) {
            if ($chanel_is_not_found &&
                in_array($mailingChannel, $this->client->selected_channels) &&
                !in_array($mailingChannel, $alreadyTried)
            ) {
                self::create([
                    'channel' => $mailingChannel,
                    'client_id' => $this->client->id,
                    'mailing_list_id' => $this->mailingList->id,
                ]);
                $chanel_is_not_found = false;
            }
        }
    }
}
