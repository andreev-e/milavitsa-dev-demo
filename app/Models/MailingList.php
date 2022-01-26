<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start',
        'allow_send_from',
        'allow_send_to',
        'text',
        'status',
        'selected_channels',
        'email_teplate'
    ];

    protected $casts = [
        'start' => 'datetime',
        'start' => 'datetime',
    ];

    public function segments() {
        return $this->belongsToMany(MailingSegment::class);
    }
}
