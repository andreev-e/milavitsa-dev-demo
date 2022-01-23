<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientUpdateFormRequest;
use App\Models\Access;
use App\Models\Action;
use App\Models\Client;
use App\Models\ClientCard;
use App\Models\ClientClaim;
use App\Models\LeadsChannel;
use App\Models\Message;
use App\Models\Option;
use App\Models\Product;
use App\Models\ProductProp;
use App\Models\Sale;
use App\Models\SalesChannel;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{

    public $botLink = '';

    /* Выводим список клиентов с фильтрами */
    public function index()
    {
        if (!Access::access(__METHOD__)) abort(403);
        setcookie("redirect", $_SERVER['REQUEST_URI']);
        $accesses = auth()->user()->accesses->pluck('action')->toArray();

        $filterText = 'Выбраны следующие фильтры: ';
        $onPage = request('onPage', 100);
        $date_start = request('date_start', FALSE);
        $date_end = request('date_end', FALSE);

        //Фильтр
        $query = $this->indexClientFilter($date_start, $date_end, $filterText);

        $query->with(['cards', 'cards.shop']);

        //Получение лид каналов
        $channels = ['null' => 'Выберите канал'];
        $channels = array_merge($channels, $this->getLeadsChannels());

        //Размеры
        $sizes = $this->getSizes();

        $clients = $query->paginate($onPage);
        return view('admin.clients.index', compact('filterText', 'clients', 'date_start', 'date_end', 'channels', 'accesses', 'sizes'));
    }

    //Фильтр index
    function indexClientFilter($date_start, $date_end, &$filterText)
    {
        /* формируем параметры фильтрации */
        $shop_id = request('shop_id', FALSE);
        $sum = request('sum', 0);
        $phone = request('phone', FALSE);
        $email = request('email', FALSE);
        $color = request('color', FALSE);
        $name = request('name', FALSE);
        $status = request('status', FALSE);
        $card = request('card', FALSE);
        $drsale = request('drsale', 2);
        $address = request('address', FALSE);
        $gender = request('gender', FALSE);
        $socials = request('socials', FALSE);
        $from = request('from', FALSE);
        $type = request('type', FALSE);
        $sizes = request('size', FALSE);

        if ($date_start)
            $date_start = Carbon::parse($date_start);
        if ($date_end)
            $date_end = Carbon::parse($date_end);
        $query = Client::query()
            ->with(['client_claims'])
            ->orderBy('status', 'desc')
            ->where('clients.id', '!=', 109999);
//        $query->withCount('sales');

        $channels = array_merge(['null' => 'Необходимо выбрать'], $this->getLeadsChannels());

        if (($from and $from != 'null') or ($type and $type == 'online') or ($sizes and $sizes[0] != NULL))
            $query->whereHas('client_claims', function ($query) use ($from, $type, $sizes, &$filterText, $channels) {
                if ($from and $from != 'null') {
                    $filterText .= 'по каналу продаж : ' . $channels[$from] . ';';
                    $query->where('from', 'LIKE', '%' . $from . '%');
                }
                if ($type) {
                    $arr = ['' => 'Выберите тип продажи', 'online' => 'online', 'offline' => 'offline'];
                    $filterText .= 'по типу продажи : ' . $arr[$type] . ';';
                    $query->where('type', 'LIKE', '%' . $type . '%');
                }
                if ($sizes and $sizes[0] != NULL) {
                    foreach ($sizes as $key => $size)
                        if ($key == 0) {
                            $filterText .= 'по размеру : ' . $size;
                            $query->where('size', 'LIKE', '%' . $size . '%');
                        } else {
                            $filterText .= ', ' . $size;
                            $query->orWhere('size', 'LIKE', '%' . $size . '%');
                        }
                    $filterText .= ';';
                }
            });
        if ($type and $type == 'offline')
            $query->where('total', '>', 0);

        if ($address) {
            $filterText .= 'по адресу : ' . $address . ';';
            $query->where(DB::raw('`address` COLLATE UTF8_GENERAL_CI'), 'like', '%' . $address . '%');
        }

        if ($color) {
            $filterText .= 'по цвету маркера :' . Client::$colors[$color] . ';';
            $query->where('color', 'LIKE', '%' . $color . '%');
        }
        if ($phone) {
            $filterText .= 'по телефону : ' . $phone . ';';
            $query->where('phone', 'LIKE', '%' . $phone . '%');
        }
        if ($socials and $socials != 'null') {
            $filterText .= 'по со сетям : ' . $socials . ';';
            $query->where('socials', '!=', '"[]"')->where('socials', 'LIKE', '%' . $socials . '%');
        }
        if ($email) {
            $filterText .= 'по email : ' . $email . ';';
            $query->where('email', 'LIKE', '%' . $email . '%');
        }
        $types = [NULL => 'Выбрать', 'lead' => 'Лид', 'buyer' => 'Покупатель', 'const_buyer' => 'Постоянный покупатель'];
        if ($status) {
            $filterText .= 'по типу клиента : ' . $types[$status] . ';';
            $query->where('status', $status);
        }
        if ($gender) {
            $filterText .= 'по полу : ' . $gender . ';';
            $query->where('gender', 'like', '%' . $gender . '%');
        }
        if ($drsale == 1) {
            $filterText .= ' даты по ДР; ';
            $query->whereBetween(DB::raw('EXTRACT(MONTH FROM birthday)'), [$date_start->month, $date_end->month]);
            $query->whereBetween(DB::raw('EXTRACT(DAY FROM birthday)'), [$date_start->day, $date_end->day]);
        }
        if ($drsale == 2) {
            $filterText .= ' даты по продажам; ';
            $client_ids = Sale::query()
                ->whereBetween('date', [Carbon::parse($date_start)->startOfDay(), Carbon::parse($date_end)->endOfDay()])
                ->select('client_id')
                ->groupBy('client_id')
                ->get()
                ->toArray();
            $query->whereIn('id', $client_ids);
        }
        if ($drsale == 3) {
            $filterText .= ' даты по добавлению; ';
            $query->whereBetween('created_at', [Carbon::parse($date_start)->startOfDay(), Carbon::parse($date_end)->endOfDay()]);
        }
        if ($drsale == 4) {
            $filterText .= ' даты по обновлению; ';
            $query->whereBetween('updated_at', [Carbon::parse($date_start)->startOfDay(), Carbon::parse($date_end)->endOfDay()]);
        }
        $query->orderBy('name', 'asc');
        if ($name) {
            $filterText .= 'по ФИО : ' . $name . ';';
            foreach (explode(' ', $name) as $oneName) {
                $query->where(DB::raw('`name` COLLATE UTF8_GENERAL_CI'), 'LIKE', '%' . $oneName . '%');
            }
        }

        if ($card or \request('card_shop', false)) {


            $clients_card_query = ClientCard::query();

            if ($card)
                $clients_card_query->where('type', 'like', $card . '%');

            if (\request('card_shop', false)) {
                $clients_card_query->where('shop_id', \request('card_shop'));
            }

            $clients_card = $clients_card_query
                ->pluck('client_id')
                ->toArray();

            $query->whereIn('id', $clients_card);

        }

        if ($sum > 0) {
            $filterText .= 'по сумме : ' . $sum . ';';
            $query->where('total', '>', $sum);
            $query->orderByDesc('total');
        }
        if ($shop_id and $shop_id !== 0) {
            $filterText .= 'по магазинам :' . Shop::query()->find($shop_id)->display_name . ';';

            $query->where('shop_id', $shop_id);
//            $client_ids = Sale::query()
//                ->where('shop_id', '=', $shop_id)
//                ->select('client_id')
//                ->groupBy('client_id')
//                ->get()
//                ->toArray();
//            $query->whereIn('id', $client_ids);
        }
        return $query;
    }

    public function client()
    {
        if (!Access::access('clientDetailsView')) abort(403);
        abort_if(!request('client_id'), 404);
        $products = [];
        $client = Client::query()
            ->where('id', request('client_id'))
//            ->with(
//                [
//                    'client_data',
//                    'client_claims',
//                ]
//            )
            ->first();

        $client = Client::query()
            ->where('id', request('client_id'))
            ->with(
                [
                    'client_data',
                    'client_claims',
                ]
            )
            ->first();

        if (is_array($client->client_claims)) {
            $claim = $client->client_claims[0];

            if (isset($claim) and $claim->user_id != auth()->user()->id) {
                $claim->user_id = auth()->user()->id;
                $claim->save();
            }
        }
        $sales_channels = SalesChannel::query()->get();

        $accesses = auth()->user()->accesses->pluck('action')->toArray();

        //История изменений
        $actions = Action::query()
            ->where('type', 'client')
            ->where('external_id', $client->id)
            ->where('info', 'LIKE', '%"old":%')
            ->where('info', 'NOT LIKE', '%"total":%')
            ->orderBy('created_at', 'desc')
            ->get();
//        dd($actions);
        $actionNames = [
            'phone' => 'Номер телефона',
            'socials' => 'Соц сети',
            'status' => 'Статус',
            'uid' => 'UID',
            'f' => 'Фамилия',
            'i' => 'Имя',
            'o' => 'Отчество',
            'birthday' => 'Дата рождения',
            'gender' => 'Пол',
            'email' => 'email',
            'address' => 'Адрес',
            'dk' => 'ДК',
            'bk' => 'БК',
            'card_date' => 'Дата карты',
        ];

        //Типы размеров по одежде
        $sizeTypes = $this->getSizeTypes();
        //Типы мерок
        $measureTypes = $this->getMeasureTypes();

        //История взаимодействий
        $answer = $this->allClientActions($client);

        //Заполненность карточки
        $percent = $this->countClientFormPercent($client);

        //Размеры для autocomplete
        $sizes = $this->getSizes();

        return view('admin.clients.client', compact('products', 'client', 'answer', 'sales_channels', 'actions', 'actionNames', 'percent', 'sizes',
            'sizeTypes', 'measureTypes', 'accesses'))->with('botLink', $this->botLink);
    }

    //Функции дял метода Client

    private function getClear($value)
    {
        return str_replace(['<p>', '</p>'], '', $value);
    }

    //Подсчет заполняемости карточки
    function countClientFormPercent($client)
    {
        $percentsFromBase = Option::query()->where('key', 'LIKE', '%ClientsCardKey%')->get();
        $percentArr = [];
        foreach ($percentsFromBase as $onePercent)
            $percentArr[explode('ClientsCardKey', $onePercent->key)[0]] = $this->getClear($onePercent->value);

        $percent = 0;
        $fields = ['i', 'birthday', 'email', 'phone'];
        foreach ($fields as $key)
            if ($client->$key) {
                $percent += $percentArr[$key];
            }
        if (isset($client->address['town']) and isset($client->address['street']))
            $percent += $percentArr['townStreet'];

        if (isset($client->client_claim)) {
            if (is_array($client->client_claim->measure) and count($client->client_claim->measure) > 0)
                $percent += $percentArr['firstMeasure'];
            if (is_array($client->client_claim->size) and count($client->client_claim->size) > 0)
                $percent += $percentArr['firstSize'];
        }
        if ($percent > 100) {

            if (!is_null($client->tg_status))
                $percent += $percentArr['bot'];

            $fields = ['f', 'o', 'socials'];
            foreach ($fields as $key)
                if ($client->$key)
                    $percent += $percentArr[$key];
            if (isset($client->address['house']) and isset($client->address['flat']))
                $percent += $percentArr['fullAddress'];

            if (isset($client->client_claim)) {
                if (is_array($client->client_claim->measure) and count($client->client_claim->measure) > 0)
                    foreach ($client->client_claim->measure as $key => $measure)
                        if ($key != 0)
                            $percent += $percentArr['otherMeasure'];

                if (is_array($client->client_claim->size) and count($client->client_claim->size) > 0)
                    foreach ($client->client_claim->size as $key => $size)
                        if ($key != 0)
                            $percent += $percentArr['otherSize'];
            }

            if (isset($client->priority["delivery"])) $percent += $percentArr['priority'];
            if (isset($client->priority["communicate"])) $percent += $percentArr['priority'];
            if (isset($client->priority["socials"])) $percent += $percentArr['priority'];

        }
        return $percent;
    }

    //история взаимодействий
    function allClientActions($client)
    {
        $messages = Message::query()->where('type', 'client')->where('external_id', $client->id)->orderBy('created_at', 'desc')->get();
        $answer = [];
        foreach ($client->client_claims as $claim) {
            if ($claim->from == 'Milavitsa.store') {
                $claim->formType = 'order';
                $claim->text = json_decode($claim->text, TRUE);
                if (is_array($claim->text)) {
                    switch ($claim->text['status']) {
                        case('new'):
                            $claim->status = 'Не оплачено';
                            break;
                        case('pay'):
                            $claim->status = 'Оплачено';
                            break;
                    }
                }
            } else
                $claim->formType = 'claim';
            $answer[] = $claim;
        }
        foreach ($messages as $key => $message) {
            $message->formType = 'message';
            $answer[] = $message;
        }

        $sales = Sale::query()->where('client_id', $client->id)->orderBy('date', 'desc')->get();
        foreach ($sales as $sale) {
            $sale->created_at = $sale->date;
            $sale->formType = 'sale';
            $answer[] = $sale;
        }
        if ($client->client_data)
            foreach ($client->client_data as $data) {
                if ($data->key == 'cart') {
                    $props = [];
                    $products = [];
                    $counts = [];
                    foreach (json_decode($data->value, TRUE) as $prop_id => $count) {
                        $props[] = $prop = ProductProp::find($prop_id);
                        $products[] = Product::query()->where('uid', $prop->product_uid)->first();
                        $counts[] = $count;
                    }
                    $answer[] = (object)[
                        'key' => 'cart',
                        'count' => $counts,
                        'created_at' => $data->created_at,
                        'prop' => $props,
                        'product' => $products,
                        'formType' => 'cart',
                    ];
                } else {
                    $data->formType = 'default';
                    $answer[] = $data;
                }
            }
        $answer = $this->quickSort($answer);
        return $answer;
    }

    function quickSort(array $arr)
    {
        $count = count($arr);
        if ($count <= 1) {
            return $arr;
        }
        $first_val = $arr[0];
        $left_arr = array();
        $right_arr = array();
        for ($i = 1; $i < $count; $i++) {
            if (Carbon::parse($arr[$i]->created_at) >= Carbon::parse($first_val->created_at)) {
                $left_arr[] = $arr[$i];
            } else {
                $right_arr[] = $arr[$i];
            }
        }
        $left_arr = self::quickSort($left_arr);
        $right_arr = self::quickSort($right_arr);
        return array_merge($left_arr, array($first_val), $right_arr);
    }

    //Конец функций дял метода Client

    //Получение размеров из базы
    function getSizes()
    {
        $props = ProductProp::query()->groupBy('fullness')->pluck('fullness')->toArray();
        $sizes = array_diff($props, ['']);
        return $sizes;
    }

    //Получение лид каналов
    function getLeadsChannels()
    {
        $channels = [];
        $leadChannels = LeadsChannel::query()->get();
        foreach ($leadChannels as $leadChannel)
            $channels[$leadChannel->name] = $leadChannel->name;

        return $channels;
    }

    //Получение из базы типов размеров по одежде
    function getSizeTypes()
    {
        $parameters = [];
        $sizeTypes = Option::query()->where('key', 'sizeTypes')->first();
        if ($sizeTypes->value)
            $parameters = str_replace(['<p>', '</p>'], '', explode('</p><p>', $sizeTypes->value));
        return $parameters;
    }

    //Получение из базы мерок
    function getMeasureTypes()
    {
        $parameters = [];
        $sizeTypes = Option::query()->where('key', 'measureTypes')->first();
        if ($sizeTypes->value)
            $parameters = str_replace(['<p>', '</p>'], '', explode('</p><p>', $sizeTypes->value));
        return $parameters;
    }

    public function formUpdate(ClientUpdateFormRequest $request)
    {
        $client = Client::find($request->client);
        //Переменныее
        $f = $client->f;
        $i = $client->i;
        $o = NULL;
        $phone = NULL;
        $email = NULL;
        $socials = NULL;
        $size = NULL;
        //Карточка клиента
        //ФИО
        //Фамилия имя и очтество отдельно
        $client->f = $request->input('f', $client->f);
        $client->i = $request->input('i', $client->i);
        $client->o = $request->input('o', $client->o);
        $client->color = $request->input('color', ($client->color ?? NULL));
        $client->name = $client->f . ' ' . $client->i . ' ' . $client->o;
        //Дата рождения
        if ($request->birthday) {
            $birthday = ($request->birthday ? Carbon::parse($request->birthday)->format('Y-m-d 00:00:00') : '');
            $client->birthday = $birthday;
        }
        //Номера телефона
        $phone = [];
        if ($request->phone) {
            foreach ($request->phone as $phoneNumber) {
                $phone[] = phoneToBase($phoneNumber);
            }
        }
        $client->phone = !empty($phone) ? $phone : '[]';
        //email
        $client->email = $request->email != NULL ? [$request->email] : NULL;
        //Социальные сети
        $socials = [];
        if ($request->socialsLabels)
            foreach ($request->socialsLabels as $key => $label)
                if (isset($request->socialsValues[$key]))
                    $socials[$label] = $request->socialsValues[$key];

        $size = [];
        if ($request->sizesLabels)
            foreach ($request->sizesLabels as $key => $label)
                if (isset($request->sizes[$key]))
                    $size[$label] = $request->sizes[$key];

        $measure = [];
        if ($request->measuresLabels)
            foreach ($request->measuresLabels as $key => $label)
                if (isset($request->measures[$key]))
                    $measure[$label] = $request->measures[$key];

        $address = (isset($client->address) ? $client->address : []);

        $address['region'] = request('region', (isset($address['region']) ? $address['region'] : ''));
        $address['town'] = request('town', (isset($address['town']) ? $address['town'] : ''));
        $address['street'] = request('street', (isset($address['street']) ? $address['street'] : ''));
        $address['house'] = request('house', (isset($address['house']) ? $address['house'] : ''));
        $address['flat'] = request('flat', (isset($address['flat']) ? $address['flat'] : ''));

        $client->address = $address;

        $client->socials = !empty($socials) ? $socials : '[]';

        $priority = ((isset($client->priority) and $client->priority != '[]') ? $client->priority : []);

        if ($request->priority_communicate) $priority['communicate'] = $request->input('priority_communicate', (isset($priority['communicate']) ? $priority['communicate'] : ''));
        if ($request->priority_delivery) $priority['delivery'] = $request->input('priority_delivery', (isset($priority['delivery']) ? $priority['delivery'] : ''));
        if ($request->priority_socials) $priority['socials'] = $request->input('priority_socials', (isset($priority['socials']) ? $priority['socials'] : ''));
        $client->priority = !empty($priority) ? $priority : '[]';

        if ($request->status)
            if ($request->status != $client->status)
                $client->status = $request->status;
        $client->save();
        //Создание client_claim
        ClientClaim::create([
            'client_id' => $client->id,
            'size' => !empty($size) ? $size : '[]',
            'measure' => !empty($measure) ? $measure : '[]',
            'from' => 'DIT(карточка клиента)',
            'text' => $request->text ?? '',
        ]);
        return redirect()->route('admin.clients.client', ['client_id' => $client->id]);
    }

    public function form()
    {
        $leadChannels = $this->getLeadsChannels();

        //Типы размеров по одежде
        $sizeTypes = $this->getSizeTypes();

        //Типы мерок
        $measureTypes = $this->getMeasureTypes();

        //Получение размеров
        $sizes = $this->getSizes();
        return view('admin.clients.form', compact('leadChannels', 'sizes', 'sizeTypes', 'measureTypes'));
    }


}
