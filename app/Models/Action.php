<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
	protected $guarded = ['id'];

	static function index($user_id, $info)
	{
		Action::create([
			'user_id' => $user_id,
			'info' => $info,
		]);
	}

	public function user()
	{
        return $this->belongsTo('App\Models\User');
    }
}
