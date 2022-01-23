<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $guarded = ['id'];

    public function avgCheck($date_start = FALSE, $date_end = FALSE)
    {
        $sum = 0;
        $count = 0;
        if ($date_start == FALSE) $date_start = today(); else $date_start = Carbon::parse($date_start);
        $date_start = $date_start->startOfDay();
        if ($date_end == FALSE) $date_end = today(); else    $date_end = Carbon::parse($date_end);
        $date_end = $date_end->endOfDay();
        $sales = Sale::query()
            ->where('date', '>=', $date_start)
            ->where('date', '<=', $date_end)
            ->where('shop_id', $this->id)
            ->get();
        foreach ($sales as $sale) {
            $sum += $sale->total;
            $count++;
        }
        if ($sum and $count != 0)
            return round($sum / $count, 2);
        else
            return 0;
    }

    public function avgItems($date_start = FALSE, $date_end = FALSE)
    {
        $count_items = 0;
        $count_checks = 0;
        if ($date_start == FALSE) $date_start = today(); else $date_start = Carbon::parse($date_start);
        $date_start = $date_start->startOfDay();
        if ($date_end == FALSE) $date_end = today(); else    $date_end = Carbon::parse($date_end);
        $date_end = $date_end->endOfDay();
        $sales = Sale::query()
            ->where('date', '>=', $date_start)
            ->where('date', '<=', $date_end)
            ->where('shop_id', $this->id)
            ->get();
        foreach ($sales as $sale) {
            $saleProducts = $sale->products()->get();
            foreach ($saleProducts as $saleProduct) {
                $count_items += $saleProduct->count;
            }
            $count_checks++;
        }
        if ($count_items and $count_checks != 0)
            return round($count_items / $count_checks, 2);
        else
            return 0;
    }

    public function checkMessage($date_start, $date_end)
    {
        return $this->hasMany('App\Models\Review')
            ->where('created_at', '>=', $date_start)
            ->where('created_at', '<', Carbon::parse($date_end)->addDays())
            ->where('submit_status', 0)
            ->count();
    }

    public function checklists()
    {
        return $this->hasMany('App\Models\Checklist');
    }

    public function diff($id)
    {
        return $this->matrix($id) - $this->fact($id);
    }

    public function matrix($id)
    {
        $item = TmcPosmMatrix::query()->orderBy('id', 'desc')->where('shop_id', $this->id)->where('tmcposm_id', $id)->first();
        if (isset($item))
            return $item->value;
        return 0;
    }

    public function fact($id)
    {
        $item = TmcPosmFact::query()->orderBy('id', 'desc')->where('shop_id', $this->id)->where('tmcposm_id', $id)->first();
        if (isset($item))
            return $item->value;
        return 0;
    }

    public function fullname()
    {
        return $this->name . ' ' . ($this->city ? (', ' . $this->city) : '') . ' ' . ($this->mart ? (', ' . $this->mart . '') : '');
    }

    public function getScheduleUsers($date = NULL)
    {
        $schedule = $this->getSchedule($date);
        $users = $schedule->users();
        if (is_array($users))
            return array_keys($users);
        else return [];
    }

    public function getSchedule($date = NULL)
    {
        if (!$date)
            $date = today();
        $schedule = $this->schedules()
            ->firstOrCreate([
                'date' => $date,
                'shop_id' => $this->id,
            ]);
        return $schedule;
    }

    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule');
    }

    public function getScheduleUsersPeriod($date_start = NULL, $date_end = NULL)
    {
        $schedules = $this->getSchedules($date_start, $date_end);
        $users = [];
        foreach ($schedules as $schedule) {
            $us = (array)$schedule->users();
            if ($us and array_keys($us))
                $users = array_merge($users, array_keys($us));
        }
        return array_unique($users);
    }

    public function getSchedules($date_start = NULL, $date_end = NULL)
    {
        if (!$date_start or !$date_end)
            return FALSE;
        $schedules = $this->schedules()
            ->where('date', '>=', $date_start)
            ->where('date', '<=', $date_end)
            ->get();
        return $schedules;
    }

    public static function getSelectOptions()
    {
        $items = self::query()
            ->active()
            ->select('id', 'display_name')
            ->orderBy('display_name', 'asc')
            ->get();
        $result = [];
        foreach ($items as $item) {
            $result[$item->id] = $item->display_name;
        }
        return $result;
    }

    public function getSelectUsers()
    {
        $items = User::query()
            ->where('shops', 'LIKE', '%"' . $this->id . '"%')
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();
        $result = [];
        foreach ($items as $item) {
            $result[$item->id] = $item->name;
        }
        return $result;
    }

    public function itemsCount($date_start = FALSE, $date_end = FALSE)
    {
        $count_items = 0;
        if ($date_start == FALSE) $date_start = today(); else $date_start = Carbon::parse($date_start);
        $date_start = $date_start->startOfDay();
        if ($date_end == FALSE) $date_end = today(); else    $date_end = Carbon::parse($date_end);
        $date_end = $date_end->endOfDay();
        $sales = Sale::query()
            ->where('date', '>=', $date_start)
            ->where('date', '<=', $date_end)
            ->where('shop_id', $this->id)
            ->get();
        foreach ($sales as $sale) {
            $saleProducts = $sale->products()->get();
            foreach ($saleProducts as $saleProduct) {
                $count_items += $saleProduct->count;
            }
        }
        return round($count_items, 2);
    }

    public function notCheckMessage($date_start, $date_end)
    {
        return $this->hasMany('App\Models\Review')
            ->where('created_at', '>=', $date_start)
            ->where('created_at', '<', Carbon::parse($date_end)->addDays())
            ->where('submit_status', '!=', 0)
            ->count();
    }

    public function plans()
    {
        return $this->hasMany('App\Models\Plan');
    }

    public function responses()
    {
        return $this->hasMany('App\Models\UserResponse');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public function reviews_count($date_start, $date_end)
    {
        return $this->hasMany('App\Models\Review')
            ->where('created_at', '>=', $date_start)
            ->where('created_at', '<', Carbon::parse($date_end)->addDays())
            ->count();
    }

    public function reviews_count_dislike($date_start, $date_end)
    {
        return $this->hasMany('App\Models\Review')
            ->where('created_at', '>=', $date_start)
            ->where('created_at', '<', Carbon::parse($date_end)->addDays())
            ->where('likes', 2)
            ->count();
    }

    public function reviews_count_like($date_start, $date_end)
    {
        return $this->hasMany('App\Models\Review')
            ->where('created_at', '>=', $date_start)
            ->where('created_at', '<', Carbon::parse($date_end)->addDays())
            ->where('likes', 1)
            ->count();
    }

    public function reviews_count_social($id, $date_start, $date_end)
    {
        if ($id == 1) {
            return $this->hasMany('App\Models\Review')
                ->where('created_at', '>=', $date_start)
                ->where('created_at', '<', Carbon::parse($date_end)->addDays())
                ->where('social', 'yandex')
                ->count();
        }
        if ($id == 2) {
            return $this->hasMany('App\Models\Review')
                ->where('created_at', '>=', $date_start)
                ->where('created_at', '<', Carbon::parse($date_end)->addDays())
                ->where('social', 'google')
                ->count();
        }
        if ($id == 3) {
            return $this->hasMany('App\Models\Review')
                ->where('created_at', '>=', $date_start)
                ->where('created_at', '<', Carbon::parse($date_end)->addDays())
                ->where('social', 'two_gis')
                ->count();
        }
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeWithoutTesting($query)
    {
        return $query->whereNotIn('id', [10]);
    }

    public function scopeWithoutBackOffice($query)
    {
        return $query->whereNotIn('id', [11]);
    }

    public function users()
    {
        $users = User::query()
            ->where('shops', 'like', '%"' . $this->id . '"%')
            ->where('status', 1)
            ->get();
        return $users;
    }

    public function scopeWork($query)
    {
        return $query->where('status', 1)->whereNotIn('id', [10]);
    }

}
