<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    public static $colors = [
        NULL => '',
        'success' => 'Зеленый',
        'warning' => 'Желтый',
        'danger' => 'Красный',
        'primary' => 'Синий',
        'dark' => 'Черный',
    ];
    protected $guarded = ['id'];
    const measureTypes = ['Грудь', 'Под грудью', 'Талия', 'Бедра', 'Рост'];
    protected $dates = [
        'last_buy', 'created_at', 'updated_at', 'birthday', 'card_date'
    ];
    protected $casts = [
        'phone' => 'array',
        'socials' => 'array',
//		'address' => 'array',
        'email' => 'array',
        'priority' => 'array',
        'birthday' => 'datetime:d.m.Y',
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
        'card_date' => 'datetime:d.m.Y',
    ];

    public function client_claim()
    {
        return $this->hasOne(ClientClaim::class)->orderBy('id', 'desc');
    }

    public function getAddressAttribute($value)
    {
        if ($value) {

            $address_arr = explode('|', $value);
            $address = [];
            foreach ($address_arr as $address_item) {
                $address_item = explode(':', $address_item);
                if (isset($address_item[0]) and isset($address_item[1]))
                    $address[$address_item[0]] = $address_item[1];
                else
                    $address[] = '';
            }
        } else
            $address = [];
        return $address;
    }

    public function setAddressAttribute($value)
    {
        $address_arr = [];

        foreach ($value as $address_key => $address_value) {
            $address_arr[] = $address_key . ':' . $address_value;
        }
        $address = implode('|', $address_arr);
        $this->attributes['address'] = $address;
    }

    public function client_claims()
    {
        return $this->hasMany('App\Models\ClientClaim')->orderByDesc('created_at');
    }

    public function client_data()
    {
        return $this->hasMany('App\Models\ClientData');
    }

    public function getClaimAttribute()
    {
        if (isset($this->client_claims->first()->id))
            return $this->client_claims->first()->id;
        return '';
    }

    public function getClaimDateAttribute()
    {
        if (isset($this->client_claims->first()->created_at))
            return Carbon::parse($this->client_claims->first()->created_at)->format('d.m.Y H:i');
        return '';
    }

    public function getFromAttribute()
    {
        return $this->source;
    }

    public function getLastAttribute()
    {
        $last = $this->hasMany('App\Models\Sale')->orderByDesc('date')->first();
        if ($last)
            return $last->date;
        return '';
    }

    public function getLinkAttribute()
    {
        if (isset($this->client_claims->first()->link) and isset(Redirect::find($this->client_claims->first()->link)->link))
            return Redirect::find($this->client_claims->first()->link)->link;
        else
            return FALSE;
    }

    public function getPhonesAttribute()
    {
        return $this->arraysValues($this->phone);
    }

    public function getStatusTextAttribute()
    {
        if (isset($this->status)) {
            switch ($this->status) {
                case 'lead':
                    return 'Лид';
                case 'buyer':
                    return 'Покупатель';
                case 'const_buyer':
                    return 'Постоянный покупатель';
            }
        } else {
            return '';
        }
    }

    public function getTextAttribute()
    {
        if (isset($this->client_claims->first()->text))
            return $this->client_claims->first()->text;
        return '';
    }

    public function getWorkEmailAttribute()
    {
        return $this->arraysValues($this->email);
    }

    private function arraysValues($values)
    {
        if (is_array($values) and count($values) > 0) {
            foreach ($values as $value)
                if (!is_null($value) and !empty($value))
                    return $value;
        }
        return null;
    }

    public function getWorkSizeAttribute()
    {
        $answer = '';
        if (json_decode($this->getSizeAttribute(), TRUE)) {
            foreach (json_decode($this->getSizeAttribute(), TRUE) as $key => $size)
                $answer .= $size . '<br>';
        } else
            $answer .= $this->getSizeAttribute();
        return $answer;
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale');
    }

    public function sizeCompilation()
    {
        $sizes = $this->getSizeAttribute();
        if ($sizes == '[]')
            return '';
        $data = [];
        foreach ($sizes as $key => $value)
            $data[] = $value;
        return $data;
    }

    public function sizeForCatalog()
    {
        dd($this->getSizeAttribute());
    }

    public function getSizeAttribute()
    {
        if (isset($this->client_claims->first()->size))
            return $this->client_claims->first()->size;
        return [];
    }

    public function sumBuy()
    {
        $sum = 0;
        $sales = Sale::query()->where('client_id', $this->id)->get();
        foreach ($sales as $sale) {
            $sum += $sale->total;
        }
        return $sum;
    }

    public function getBalanceAttribute()
    {
        return $this->hasMany('App\Models\BallsBilling')->select(DB::raw('sum(value) as total'))->first()->total ?? 0;
    }

    public function isActive()
    {
        if (!is_null($this->f) and !is_null($this->i) and !is_null($this->birthday) and !is_null($this->license_accept) and !is_null($this->email) and !is_null($this->email_verify) and !is_null($this->phone))
            return true;
        return false;
    }

    public function cards()
    {
        return $this->hasMany(ClientCard::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getSelectedChannelsAttribute() : array
    {
        //TODO выбираем доступные каналы для рассылок пользователя и их приоритет
        return ['email','telegram','sms','whatsapp'];
    }

    public function segments()
    {
        return $this->belongsToMany(MailingSegment::class, 'client_mailing_segment');
    }

    public function isBlackListed() {
        // TODO: дополнить проверку - черный список на основе отдельных номеров и емейлов
        $activeUserEmails = User::select('email')->where('status', 1)->pluck('email')->toArray();
        if (count(array_intersect($activeUserEmails, $this->email))) {
            return true;
        }
        return $this->segments()->where('type', '=', 'black')->count() !== 0;
    }

}
