<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCard extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $dates = [
        'date'
    ];

    protected $casts = [
        'date' => 'datetime:d-m-Y',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getDateTextAttribute()
    {
        if (Carbon::isValid($this->getRawOriginal('date')))
            return $this->date;
        else
            return 'Некорректная дата';
    }

}
