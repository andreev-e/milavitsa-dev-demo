<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public static $colors = [
        NULL => '',
        'success' => 'Зеленый',
        'warning' => 'Желтый',
        'danger' => 'Красный',
        'primary' => 'Синий',
        'dark' => 'Черный',
    ];
    protected $with = [
    ];
    protected $guarded = ['id'];

    public function barcode($prop = 0)
    {
        $barcode = $this->hasOne('App\Models\ProductBarcode')->where('product_prop_id', $prop)->first();
        if ($barcode)
            return $barcode->barcode;
        else return 0;
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategory');
    }

    public function channel_products()
    {
        return $this->hasMany('App\Models\ChannelProduct');
    }

    public function channels()
    {
        $answer = '';
        if ($this->view === NULL)
            $answer .= '1';
        if ($this->ozon === NULL)
            $answer .= ',2';
        if ($this->aliexpress === NULL)
            $answer .= ',3';
        if ($this->wildberries === NULL)
            $answer .= ',4';
        if ($this->yandex === NULL)
            $answer .= ',5';
        return $answer;
    }

    public function getAmountAttribute()
    {
        $amount = $this->product_amounts->sum('amount');
        if ($amount)
            return $amount;
        return 0;
    }

    public function getPriceAttribute()
    {
        $value = $this->product_prices->first();
        if ($value) {
            return $value->price;
        }
        return 0;
    }

    public function group()
    {
        return $this->belongsTo('App\Models\ProductGroup')->first();
    }

    public function images()
    {
        return $this->hasMany('App\Models\Image', 'external_id', 'id')->whereType('product')->orderBy('number', 'asc');
    }

    public function imagesOne()
    {
        return $this->hasOne('App\Models\Image', 'external_id', 'id')->orderBy('id', 'desc');
    }

    public function loadChannel($id)
    {
        return $this->channel_products->where('sale_channels_id', $id)->first();
    }

    public function milaChannel()
    {
        return $this->channel_products()->where('sale_channels_id', 1);
    }

    public function milaDescription()
    {
        $sale = $this->channel_products->where('sale_channels_id', 1)->first();
        return $sale->description;
    }

    public function milaName()
    {
        $sale = $this->channel_products->where('sale_channels_id', 1)->first();
        return $sale->name;
    }

    public function milaPrice()
    {
        $sale = $this->channel_products->where('sale_channels_id', 1)->first();
        if (isset($sale->price))
            return $sale->price;
        return '';
    }

    public function product_amounts()
    {
        return $this->hasMany('App\Models\ProductAmount', 'product_uid', 'uid');
    }

    public function product_category()
    {
        return $this->belongsTo('App\Models\ProductCategory');
    }

    public function product_prices()
    {
        return $this->hasMany('App\Models\ProductPrice', 'product_uid', 'uid');
    }

    public function product_props()
    {
        return $this->hasMany('App\Models\ProductProp');
    }

    public function props()
    {
        $props = $this->hasMany('App\Models\ProductProp')->get();
        $props_out = [];
        foreach ($props as $prop) {
            $prop_explode = explode(',', $prop->name);
            if (sizeof($prop_explode) == 3)
                $props_out[$prop->id] = [
                    'Размер' => trim($prop_explode[1]) . trim($prop_explode[0]),
                    'Цвет' => trim($prop_explode[2]),
                ];
            if (sizeof($prop_explode) == 2)
                $props_out[$prop->id] = [
                    'Размер' => trim($prop_explode[0]),
                    'Цвет' => trim($prop_explode[1]),
                ];
            if (sizeof($prop_explode) == 1)
                $props_out[$prop->id] = [
                    'Цвет' => trim($prop_explode[0]),
                ];
        }
        return $props_out;
    }

    public function product_amount()
    {
        return $this->hasOne(ProductAmount::class, 'product_uid', 'uid')->where('amount', '>', 0);
    }
}
