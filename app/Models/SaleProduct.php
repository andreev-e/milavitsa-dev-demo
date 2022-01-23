<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
	protected $guarded = ['id'];

	public function product()
	{
		return $this->BelongsTo('App\Models\Product');
	}

	public function product_prop()
	{
		return $this->BelongsTo('App\Models\ProductProp', 'product_prop_id', 'id');
	}

	public function products()
	{
		return $this->hasMany('App\Models\Product', 'id', 'product_id');
	}

	public function propName()
	{
		return $this->BelongsTo('App\Models\ProductProp', 'product_prop_id', 'id')->first();
	}
}
