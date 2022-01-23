<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductProp extends Model
{
    protected $guarded = ['id'];

    public function amounts()
    {
        return $this->hasMany('App\Models\ProductAmount', 'product_prop_uid', 'uid');
    }

    public function price()
    {
        return $this->hasOne('App\Models\ProductPrice', 'product_prop_uid', 'uid');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function productByUid()
    {
        return $this->belongsTo('App\Models\Product', 'uid', 'product_uid');
    }
}
