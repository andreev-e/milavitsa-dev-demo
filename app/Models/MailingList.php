<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start',
        'allow_send_from',
        'allow_send_to',
        'text',
        'status',
        'selected_channels',
        'email_teplate',
        'whatsapp_teplate',
        'chunk',
    ];

    protected $casts = [
        'start' => 'datetime',
        'selected_channels' => Json::class,
    ];

    public function segments() {
        return $this->belongsToMany(MailingSegment::class);
    }

    public function messages() {
        return $this->hasMany(MailingMessage::class);
    }

}
