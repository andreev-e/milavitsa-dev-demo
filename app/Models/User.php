<?php

namespace App\Models;

use App\Services\BillingService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    const TYPE_SELLER = 1;
    const TYPE_ADMIN = 2;
    const TYPE_MANAGER = 3;
    public static $calendar = [
        2019 => [1 => 136, 2 => 159, 3 => 159, 4 => 175, 5 => 143, 6 => 151, 7 => 184, 8 => 176, 9 => 168, 10 => 184, 11 => 160, 12 => 175],
        2020 => [1 => 136, 2 => 152, 3 => 168, 4 => 175, 5 => 135, 6 => 167, 7 => 184, 8 => 168, 9 => 176, 10 => 176, 11 => 159, 12 => 183],
        2021 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2022 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2023 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2024 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
    ];
    public static $back = [
        2019 => [1 => 136, 2 => 159, 3 => 159, 4 => 175, 5 => 143, 6 => 151, 7 => 184, 8 => 176, 9 => 168, 10 => 184, 11 => 160, 12 => 175],
        2020 => [1 => 168, 2 => 152, 3 => 168, 4 => 175, 5 => 152, 6 => 167, 7 => 184, 8 => 168, 9 => 176, 10 => 176, 11 => 159, 12 => 183],
        2021 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2022 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2023 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2024 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
    ];
    public static $mentor = [
        2019 => [1 => 136, 2 => 159, 3 => 159, 4 => 175, 5 => 143, 6 => 151, 7 => 184, 8 => 176, 9 => 168, 10 => 184, 11 => 160, 12 => 175],
        2020 => [1 => 184, 2 => 152, 3 => 168, 4 => 175, 5 => 152, 6 => 167, 7 => 184, 8 => 168, 9 => 176, 10 => 176, 11 => 159, 12 => 183],
        2021 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2022 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2023 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
        2024 => [1 => 120, 2 => 151, 3 => 176, 4 => 175, 5 => 152, 6 => 167, 7 => 176, 8 => 176, 9 => 176, 10 => 168, 11 => 159, 12 => 176],
    ];

    public static $userFiles = [
        NULL => 'Не выбрано',
        1 => 'ИНН',
        2 => 'Договор'
    ];
    protected $guarded = ['id'];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'files' => 'array'
    ];
    protected $with = [
//        'accesses',
//        'salary_stories',
    ];

    public static function getSelectOptions()
    {
        if (auth()->user()->can('admin'))
            $types = [self::TYPE_SELLER, self::TYPE_ADMIN, self::TYPE_MANAGER];
        else
            $types = [self::TYPE_SELLER];
        $items = self::query()
            ->where('status', 1)
            ->whereIn('type', $types)
            ->orderBy('name', 'asc')
            ->get();
        $result = [];
        foreach ($items as $item) {
            $result[$item->id] = $item->name;
        }
        return $result;
    }

    public function allInfoCheck()
    {
        if (!empty($this->name) and
            !empty($this->inn) and
            !empty($this->email) and
            !empty($this->phone)) {
            if (!empty($this->files) and is_array($this->files))
                foreach ($this->files as $file)
                    if ($file == 1) {
                        return true;
                    }
        }
        return false;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
    }

    public function scheduleCheck($date = FALSE)
    {
        $date = Carbon::parse($date ?? now());
        $schedules = Schedule::query()->where('date', 'LIKE', '%' . $date->copy()->format('Y-m-d') . '%')->pluck('users')->toArray();

        foreach ($schedules as $schedule)
            if (!empty($schedule))
                if (in_array($this->id, array_keys(json_decode($schedule, TRUE))))
                    return TRUE;
        return FALSE;
    }

    public function getShopIdAttribute()
    {
        $shops = json_decode($this->shops, TRUE);
        if (empty($shops))
            return NULL;
        return $shops[0] ?? 11;
    }

    public function getShopAttribute()
    {
        return Shop::find($this->shop_id);
    }

    public function shop()
    {
        if (empty($this->shops))
            $this->update([
                'shops' => json_encode([10])
            ]);
        $shop = Shop::query()->find(json_decode($this->shops, TRUE)[0]);
        return $shop;
    }

    public function assignments()
    {
        return $this->hasMany('App\Models\Assignment');
    }

    public function accesses()
    {
        return $this->hasMany('App\Models\Access', 'user_type', 'type');
    }

    public function billings()
    {
        return $this->hasMany('App\Models\Billing');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket');
    }

    public function context()
    {
        $context = $this->hasOne('App\Models\UserContext');
        return $context;
    }

    public function salary_stories()
    {
        return $this->hasMany('App\Models\SalaryStory')->orderByDesc('date_start');
    }

    public function getSalaryAttribute()
    {
        $ds = $this->dateSalary();
        if ($ds)
            return $ds->salary;
        return 0;
    }

    public function getSalaryTypeAttribute()
    {
        $ds = $this->dateSalary();
        if ($ds)
            return $ds->salary_type;
        return 'тип не указан';
    }

    public function getRequestedAt()
    {
        $id = $this->id;
        $schedule = $this->getSchedule();
        if ($schedule) {
            $requestedAt = json_decode($schedule->requested_at, TRUE);
            if (isset($requestedAt[$id]) and is_array($requestedAt[$id])) {
                $times = array_keys($requestedAt[$id]);
                $requestedAt_temp = end($times);
            } else $requestedAt_temp = [];
        } else $requestedAt_temp = FALSE;
        return $requestedAt_temp;
    }

    public function getSchedule($date = NULL)
    {
        if (!$date)
            $date = today();
        $schedule = Schedule::query()
            ->where('date', $date)
            ->where('users', 'LIKE', '%"' . $this->id . '":["%')
            ->first();
        return $schedule;
    }

    public function getScheduleTimes()
    {
        $schedule = $this->getSchedule();
        $users_schedule = json_decode($schedule->users, TRUE);
        return $users_schedule[$this->id];
    }

    public function hasActualAssignments($hour = NULL)
    {
        return $this->getActualAssignments($hour)->count() > 0;
    }

    public function getActualAssignments($hour = NULL)
    {
        if (!$hour)
            return collect([]);
        $assignments = $this->assignments()
            ->where('closed', 0)
            ->where('unactual', 0)
            ->where('times', 'like', '%' . $hour . '%')
            ->where('date', today())
            ->get();
        if (empty($assignments)) {
            return collect([]);
        }
        return $assignments;
    }

    public function getTypeTextAttribute()
    {
        return Role::find($this->type)->name ?? '';
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale');
    }

    public function getActualUnaskedAssignments($hour = NULL)
    {
        if (!$hour)
            return collect([]);
        $assignments = $this->assignments()
            ->where('closed', 0)
            ->where('asked', 0)
            ->where('unactual', 0)
            ->where('times', 'like', '%' . $hour . '%')
            ->where('date', today())
            ->get();
        if (empty($assignments)) {
            return collect([]);
        }
        return $assignments;
    }

    /**
     * "Заморозить" Продавца на Х минут.
     * Он перестанет получать уведомления до тех пор, пока Х минут не пройдет
     * или не отправит новое сообщение
     * @param $forMinutes
     */
    public function pause($forMinutes)
    {
        $context = $this->context()->firstOrCreate([
            'user_id' => $this->id,
        ]);
        $context->is_paused = TRUE;
        $context->paused_until = now()->addMinutes($forMinutes);
        $context->save();
    }

    /*снять Продавца с паузы*/
    public function unpause()
    {
        $context = $this->context()->firstOrCreate([
            'user_id' => $this->id,
        ]);
        $context->is_paused = FALSE;
        $context->paused_until = NULL;
        $context->save();
    }

    /*проверка Продавца на паузу*/
    public function isPaused()
    {
        $context = $this->context()->firstOrCreate([
            'user_id' => $this->id,
        ]);
        return $context->is_paused;
    }

    public function pausedUntil()
    {
        $context = $this->context()->firstOrCreate([
            'user_id' => $this->id,
        ]);
        return $context->paused_until;
    }

    public function disciplineDay()
    {
        $date_start = Carbon::createFromTimeString(request('date_start', today()->format('Y-m-d')) . '0:0')->startOfDay();
        $date_end = Carbon::createFromTimeString(request('date_start', today()->format('Y-m-d')) . '0:0')->endOfDay();
        $penalties = Penalty::query()
            ->where('created_at', '>=', $date_start)
            ->where('created_at', '<=', $date_end)
            ->where('user_id', $this->id)
            ->get();
        $assignments = Assignment::query()
            ->where('date', '>=', $date_start)
            ->where('date', '<=', $date_end)
            ->where('user_id', $this->id)
            ->get();
        $pen = [];
        foreach ($penalties as $penalty)
            if (isset($pen[$penalty->user_id]))
                $pen[$penalty->user_id]++;
            else
                $pen[$penalty->user_id] = 1;
        $shops = Shop::where('id', '!=', 10)->where('id', '!=', 11)->get();
        $work_times = [];
        foreach ($shops as $shop) {
            $users = $shop->getScheduleUsersPeriod($date_start, $date_end);
            foreach ($users as $user_id) {
                $work_times[$user_id] = [];
                foreach ($assignments as $assigment) {
                    if ($assigment->user_id == $user_id) {
                        if (!isset($work_times[$user_id][$assigment->date]))
                            $work_times[$user_id][$assigment->date] = [];
                        $work_times[$user_id][$assigment->date] = array_unique(array_merge($work_times[$user_id][$assigment->date], json_decode($assigment->times, TRUE)));
                    }
                }
            }
        }
        $total = [];
        foreach ($work_times as $user_id => $dates) {
            $total[$user_id] = 0;
            foreach ($dates as $date)
                $total[$user_id] += sizeof($date);
            if (isset($pen[$user_id])) {
                $pen[$user_id] = 100 - $pen[$user_id] / $total[$user_id] * 50;
            } else
                $pen[$user_id] = '100';
        }
        asort($pen);
        $user_work_id = $this->id;
        return round($pen[$user_work_id]) . '%';
    }

    public function disciplineWeek()
    {
        $date_start = Carbon::createFromTimeString(request('date_start', now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d')) . '0:0')->startOfDay();
        $date_end = Carbon::createFromTimeString(request('date_end', now()->endOfWeek(Carbon::SUNDAY)->format('Y-m-d')) . '0:0')->endOfDay();
        $penalties = Penalty::query()
            ->where('created_at', '>=', $date_start)
            ->where('created_at', '<=', $date_end)
            ->where('user_id', $this->id)
            ->get();
        $assignments = Assignment::query()
            ->where('date', '>=', $date_start)
            ->where('date', '<=', $date_end)
            ->where('user_id', $this->id)
            ->get();
        $pen = [];
        foreach ($penalties as $penalty)
            if (isset($pen[$penalty->user_id]))
                $pen[$penalty->user_id]++;
            else
                $pen[$penalty->user_id] = 1;
        $shops = Shop::where('id', '!=', 10)->where('id', '!=', 11)->get();
        $work_times = [];
        foreach ($shops as $shop) {
            $users = $shop->getScheduleUsersPeriod($date_start, $date_end);
            foreach ($users as $user_id) {
                $work_times[$user_id] = [];
                foreach ($assignments as $assigment) {
                    if ($assigment->user_id == $user_id) {
                        if (!isset($work_times[$user_id][$assigment->date]))
                            $work_times[$user_id][$assigment->date] = [];
                        $work_times[$user_id][$assigment->date] = array_unique(array_merge($work_times[$user_id][$assigment->date], json_decode($assigment->times, TRUE)));
                    }
                }
            }
        }
        $total = [];
        foreach ($work_times as $user_id => $dates) {
            $total[$user_id] = 0;
            foreach ($dates as $date)
                $total[$user_id] += sizeof($date);
            if (isset($pen[$user_id])) {
                $pen[$user_id] = 100 - $pen[$user_id] / $total[$user_id] * 50;
            } else
                $pen[$user_id] = '100';
        }
        asort($pen);
        $user_work_id = $this->id;
        return round($pen[$user_work_id]) . '%';
    }

    public function getStatusName()
    {
        $context = $this->context()->firstOrCreate([
            'user_id' => $this->id,
        ]);
        switch ($context->current_status) {
            case 1:
                return 'новый Сотрудник';
                break;
            case 2:
                return 'начало отчёта';
                break;
            case 3:
                return 'запрошен отчет по задаче (' . $context->current_task_id . ')';
                break;
            case 4:
                return 'нет активных задач';
                break;
            case 5:
                return 'запрошен детальный отчет, задача выполнена (' . $context->current_task_id . ')';
                break;
            case 6:
                return 'запрошен детальный отчет, задача не выполнена (' . $context->current_task_id . ')';
                break;
            default:
                return 'Неизвестный статус';
        }
    }

    public function workTime($date_start = FALSE, $date_end = FALSE)
    {
        return (object)['good' => (new BillingService)->ipGeoCalculate($this, $date_start, $date_end)];
    }

    public function checkMoment($datetime, $journals)
    {
        $datetime = Carbon::parse($datetime);
        $before = $journals->where('created_at', '<', $datetime)->sortByDesc('created_at')->first();
        $after = $journals->where('created_at', '>', $datetime)->sortByDesc('created_at')->last();
        return $before && $after && in_array($before->status, ['good', 'start']);
    }

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
            ->where('user_id', $this->id)
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
            ->where('user_id', $this->id)
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
            ->where('user_id', $this->id)
            ->get();
        foreach ($sales as $sale) {
            $saleProducts = $sale->products()->get();
            foreach ($saleProducts as $saleProduct) {
                $count_items += $saleProduct->count;
            }
        }
        return round($count_items, 2);
    }

    public function checksCount($date_start = FALSE, $date_end = FALSE)
    {
        if ($date_start == FALSE) $date_start = today(); else $date_start = Carbon::parse($date_start);
        $date_start = $date_start->startOfDay();
        if ($date_end == FALSE) $date_end = today(); else    $date_end = Carbon::parse($date_end);
        $date_end = $date_end->endOfDay();
        $sales = Sale::query()
            ->where('date', '>=', $date_start)
            ->where('date', '<=', $date_end)
            ->where('user_id', $this->id)
            ->count();
        return round($sales, 2);
    }

    public function revenue()
    {
        $total = 0;
        $sales = Sale::query()
            ->where('date', 'LIKE', today()->format('Y-m-d') . '%')
            ->where('user_id', $this->id)
            ->get();
        foreach ($sales as $sale)
            $total += $sale->total;
        return $total;
    }

    function getImgHtmlAttribute()
    {
        $string = '';
        $string .= '<div class="d-inline-block p-0" data-toggle="tooltip" data-placement="top" title="' . $this->name . '"';
        if ($this->color != '')
            $string .= ' style="border:#' . $this->color . ' solid 2px" ';
        $string .= '>';
        if ($this->image) {
            $string .= '<a data-fancybox="gallery-' . $this->id . '" target="_blank" href="https://dyatlovait.ru/' . $this->image . '"> <img src="https://dyatlovait.ru/' . $this->image . '" height="40px" id="image"></a>';
        } else {
        }
        $string .= '</div>';
        return $string;
    }

    public function dateSalary($date = FALSE)
    {
        if (!$date)
            $date = now()->toDateString();

        //             ->whereRaw("DATE('" . $date . "') between date_start and IFNULL(date_end, NOW() + INTERVAL 1 MONTH)")

        $salary = $this->salary_stories
            ->filter(function ($s, $key) use ($date) {
                return (
                    Carbon::parse($s->date_start) <= Carbon::parse($date)
                    &&
                    (
                        Carbon::parse($s->date_end) >= Carbon::parse($date)
                        or
                        $s->date_end === null
                    )
                );
            })->first();
        return $salary;
    }

    public function hourSalary($date = FALSE)
    {
        $salary = $this->dateSalary($date);
        if (!$salary)
            return 0;
        $value = $this->hourSalaryCalc($salary, $date);
        return $value;
    }

    public function hourSalaryCalc($salary, $date)
    {
        $date = Carbon::parse($date);
        switch ($salary->salary_type) {
            case 1:
            case 2:
                $hours = $this->calendar();
                return $salary->salary / $hours[$date->year][$date->month];
            case 3:
                return 77.68;
            default:
                return 0;
        }
    }

    public function calendar()
    {
        $items = [];

        $type = 'seller';
        if (in_array($this->type, [1, 2]))
            $type = 'back';
        if (in_array($this->type, [3]))
            $type = 'mentor';

        $times = CalendarWork::query()->where('users_type', $type)->get();
        foreach ($times as $time)
            $items[$time->year][$time->month] = $time->value;
        return $items;
    }

    public function calendarNow($date = FALSE)
    {
        $calendar = $this->calendar();
        $days = $date->copy()->startOfMonth()->subDay()->diffInDays($date->copy()->endOfMonth());
        return $calendar[$date->year][$date->month] / $days;
    }

    public function periodSalary($date_start, $date_end)
    {
        $period = CarbonPeriod::create($date_start, $date_end);
        $salary = FALSE;

        foreach ($period as $date) {
            $salary += $this->hourSalary($date) * $this->calendarNow($date);
        }

        return $salary;
    }

//prepayment - аванс, salary - оклад , hospitals - больничные, vacation - отпускные, mission - командировочные, kpi , layoffs - увольнения, benefits - пособия, other - прочее
    public function calculate($date_start, $date_end, $types =
//    ['ipgeo', 'hours', 'money', 'dinner', 'official', 'inv', 'correct', 'prepayment', 'salary', 'hospitals', 'vacation', 'mission', 'kpi', 'layoffs','realization']
    ['hours', 'money', 'dinner', 'official', 'inv', 'correct', 'prepayment', 'salary', 'layoffs', 'ndfl1', 'hospitals', 'benefits', 'vacation', 'mission', 'kpi', 'realization', 'location', 'plus', 'planOverload', 'ipgeo']
)
    {
        $sum = $this->billings->sum(function ($billing) use ($date_start, $date_end, $types) {
            return $billing->calculate($date_start, $date_end, $types);
        });
        return $sum;
    }

    public function salesCalculate($date_start, $date_end)
    {
        $sum = $this->sales->sum(function ($sale) use ($date_start, $date_end) {
            return $sale->calculate($date_start, $date_end);
        });

        return $sum;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeWork($query)
    {
        return $query->where('status', 1);
    }

    public function bookForm()
    {
        return $this->hasOne(BookForm::class)->orderBy('id', 'desc');
    }

    public function seeker_feedback_answers()
    {
        return $this->hasMany(SeekerFeedbackAnswer::class, 'mentor_id', 'id');
    }

    public function npsSeekersForDateRating(Carbon $date_start, Carbon $date_end)
    {
        $rating = ['count' => 0, 'sum' => 0];
        $npsAnswers = $this->seeker_feedback_answers()
            ->whereBetween('date', [$date_start->subHour()->format('Y-m-d 00:00:00'), $date_end->addHour()->format('Y-m-d 23:59:59')])
            ->get();
        foreach ($npsAnswers as $npsAnswer)
            if (!is_null($npsAnswer->answer) and is_array($npsAnswer->answer))
                foreach ($npsAnswer->answer as $number => $value)
                    if (is_numeric($number)) {
                        $rating['sum'] += $value;
                        $rating['count']++;
                    }

        if ($rating['count'] != 0)
            return round($rating['sum'] / $rating['count'], 1);

        return 0;
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function timetableOnNextMonth()
    {
        return $this->timetables()->whereBetween('date', [now()->startOfMonth()->addMonth()->startOfDay(), now()->startOfMonth()->addMonth()->endOfMonth()->endOfDay()]);
    }

    public function isManager()
    {
        if ($this->type == 3 and $this->status == 1)
            return true;
        return false;
    }

    public function getFiAttribute()
    {
        if ($this->f == '' && $this->i == '') {
            $fio = explode(' ', $this->name);
            if (empty($f))
                $f = $fio[0] ?? '';
            if (empty($i))
                $i = $fio[1] ?? '';
            if (empty($o))
                $o = $fio[2] ?? '';

            $this->fill(
                [
                    'f' => $f,
                    'i' => $i,
                    'o' => $o,
                ]
            );
            $this->saveQuietly();
        }

        return ($this->f ?? '') . ' ' . ($this->i ?? '');
    }


}
