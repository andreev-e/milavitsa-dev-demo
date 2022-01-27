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

}
