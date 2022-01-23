<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
	protected $guarded = ['id'];
	protected $casts = [
		'info' => 'array',
	];

	public function options()
	{
		return $this->hasMany('App\Models\Option');
	}

	public function option()
	{
		return $this->belongsTo('App\Models\Option');
	}
}
