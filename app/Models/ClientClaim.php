<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientClaim extends Model
{
	protected $guarded = ['id'];
	protected $casts = [
		'size' => 'array',
		'measure' => 'array',
		'compilation' => 'array',
	];

	public function client()
	{
		return $this->belongsTo('App\Models\Client');
	}
	public function user()
	{
        return $this->belongsTo('App\Models\User');
    }
}
