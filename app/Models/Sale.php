<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function realization()
    {
        return $this->hasOne(Billing::class)->where('type', 'realization');
    }

    public function billing()
    {
        return $this->belongsTo('App\Models\Billing')->whereIn('type', ['money', 'kpi']);
    }

    public function products()
    {
        return $this->hasMany('App\Models\SaleProduct');
    }

    public function npsAnswer()
    {
        return $this->belongsTo('App\Models\NpsAnswers', 'nps');
    }

    public function salePayments()
    {
        return SalePayments::query()->where('sale_id', $this->id)->get();
    }

    public function sale_products()
    {
        return $this->hasMany('App\Models\SaleProduct');
    }

    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function calculate(Carbon $date_start, Carbon $date_end)
    {
        if (!Carbon::parse($this->date)->isBetween($date_start->startOfDay(), $date_end->endOfDay()))
            return 0;

        return $this->total;
    }
}
