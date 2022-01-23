<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class  Access extends Model
{
    protected $guarded = ['id'];

    public static function access($action, $user = FALSE)
    {
        $access = Access::query()->where('action', $action)->first();
		if (empty($access)) {
			$access = new Access();
			$data = [
				'name' => $action,
				'action' => $action,
				'user_type' => '2',
			];
			$access->fill($data);
			$access->save();
			if ($user == TRUE) {
				$access = new Access();
				$data = [
					'name' => $action,
					'action' => $action,
					'user_type' => '1',
				];
				$access->fill($data);
				$access->save();
			}
		}
		return auth()->user()->can('access', $action);
	}

	public function option()
	{
		return $this->belongsTo('App\Models\Option');
	}
    
    public static function this_week_check(Carbon $date)
    {
        $date_start = now()->startOfWeek()->startOfDay();
        $date_end = now()->endOfWeek()->endOfDay();
        
        if ($date >= $date_start and $date <= $date_end)
            return TRUE;
        
        return FALSE;
    }
}
