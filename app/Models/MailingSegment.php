<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingSegment extends Model
{
    use HasFactory, SoftDeletes;

    public function clients() {
        return $this->belongsToMany(Client::class);
    }
}
