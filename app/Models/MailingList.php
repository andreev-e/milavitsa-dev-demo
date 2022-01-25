<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sms',
        'email',
        'telegram',
        'whatsapp',
        'start',
        'allow_send_from',
        'allow_send_to',
        'status',
    ];

    protected $casts = [
        'start' => 'datetime',
        'start' => 'datetime',
    ];

    public function segments() {
        return $this->belongsToMany(MailingSegment::class);
    }
}
