@extends('layouts.admin')
@section('h1')
    Список клиентов
@endsection
@section('actions')
@endsection
@section('content')
    @include('form.open', ['method' => 'get'])
    <div class="row ">
        <div class="col-12 col-md-6">
            @if (in_array( 'ClientsFilter',$accesses))
                <div class="form-group">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-3 font-weight-bold text-lg-right">
                            Поиск по ФИО
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="text" id="name2" value="{{request('name')}}" placeholder="ФИО" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-3 font-weight-bold text-lg-right">
                            Применять даты
                        </div>
                        <div class="col-12 col-md-9">
                            @include('form.radio', ['name' => 'drsale','checked'=>request('drsale')==0? TRUE :FALSE ,'label' => 'Не применять', 'value' => '0'])
                            @include('form.radio', ['name' => 'drsale','checked'=>request('drsale')==1? TRUE :FALSE ,'label' => 'По Д.Р.', 'value' => '1'])
                            @include('form.radio', ['name' => 'drsale','checked'=>request('drsale')==2? TRUE : FALSE, 'label' => 'По продажам', 'value' => '2'])
                            @include('form.radio', ['name' => 'drsale','checked'=>request('drsale')==3? TRUE : FALSE, 'label' => 'По добавлению', 'value' => '3'])
                            @include('form.radio', ['name' => 'drsale','checked'=>request('drsale')==4? TRUE : FALSE, 'label' => 'По обновлению', 'value' => '4'])
                        </div>
                    </div>
                </div>
                @include('form.date', ['name' => 'date_start','value'=>\Carbon\Carbon::parse($date_start)->format('d.m.Y'), 'label' => 'От даты', 'placeholder' => ''])
                @include('form.date', ['name' => 'date_end','value'=>\Carbon\Carbon::parse($date_end)->format('d.m.Y'), 'label' => 'До даты', 'placeholder' => ''])

                @include('form.input', ['name' => 'sum', 'label' => 'Сумма покупок больше чем', 'placeholder' => 'Минимальное значение'])
                @include('form.select', ['name' => 'socials', 'label' => 'Наличие соц сети', 'options' => ["null"=>'Выберите соц сети',"vk"=>"vk","odnoklassniki"=>"odnoklassniki","instagram"=>"instagram","facebook"=>"facebook",]])
                @include('form.select', ['name' => 'from', 'label' => 'Канал продаж','value' => request('from',NULL), 'options' =>  $channels])
                @include('form.select', ['name' => 'type', 'label' => 'Тип продажи','value' => request('type',NULL), 'options' =>  [''=>'Выберите тип продажи','online'=>'online','offline'=>'offline']])
                @include('form.select', ['name' => 'color', 'label' => 'Цвет маркера','value' => request('color',NULL), 'options' =>  \App\Models\Client::$colors])
                @include('form.input', ['name' => 'sizeAdd', 'class' => 'size','label' => 'Размер', 'value'=>'','placeholder' => 'Размер'])
                <div class="col-12 col-md-9 offset-md-3">
                    <div class="row" id="sizes">
                        @if(request('size') and request('size')[0]!=NULL)
                            @foreach(request('size') as $size)
                                <div class='col-md-2 ml-2 my-2'>
                                    <div class='row'><span class='col-8 btn btn-primary'> {{$size}} </span>
                                        <button class='col-4 btn btn-secondary removeSize'>X</button>
                                        <input type='text' value='{{$size}}' name='size[]' hidden></div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </div>
        <div class="col-12 col-md-6">
            @if (in_array( 'ClientsFilter',$accesses))
                @include('form.input', ['name' => 'address', 'label' => 'Адрес', 'placeholder' => 'Адрес','value'=>request('address')])
                @include('form.input', ['name' => 'name', 'label' => 'ФИО', 'value'=>request('name'),'placeholder' => 'поиск по ФИО'])
                @include('form.select', ['name' => 'gender', 'label' => 'Пол', 'placeholder' => 'Пол', 'value' => request('gender',NULL), 'options' =>  [NULL => 'Выбрать','Женский' => 'Женский','Мужской'=>'Мужской']])
                @include('form.input', ['name' => 'phone', 'label' => 'Телефон', 'placeholder' => 'Телефон','value'=>request('phone')])
                @include('form.input', ['name' => 'email', 'label' => 'E-Mail','value'=>request('email'), 'placeholder' => 'E-Mail'])

                @include('form.select', ['name' => 'card', 'label' => 'Карта', 'placeholder' => 'Б/К', 'value' => request('card$card',NULL),
'options' =>  [
NULL => 'Выбрать',
'ДК Фиксированая' => 'ДК Фиксированая',
'ДК Накопительная' => 'ДК Накопительная',
'ДК Деактивированная' => 'ДК Деактивированная',
'ДК Заблокированная при создании БК'=>'ДК Заблокированная при создании БК',

'БК' => 'Любая БК',
"БК Активированная" => "БК Активированная",
"БК Не активированная" => "БК Не активированная",
"БК Деактивированная" => "БК Деактивированная",
]])

                @include('form.select', ['name' => 'card_shop', 'label' => 'Магазин карты', 'placeholder' => 'Магазин карты', 'value' => request('card_shop',NULL),
                'options' =>  get_select_options('shop_id')
                ])



                @include('form.input', ['name' => 'onPage', 'label' => 'Количество записей на странице', 'placeholder' => 'Количество записей на странице'])
                @include('form.select', ['name' => 'status', 'label' => 'Статус',  'placeholder' => 'Статус', 'value' => request('status'), 'options' =>  [NULL => 'Выбрать','lead' => 'Лид','buyer'=>'Покупатель','const_buyer'=>'Постоянный покупатель']])
            @endif
            <div class="row d-flex justify-content-end">

                @if (in_array( 'ClientsFilter',$accesses))
                    <div class="col-auto">
                        <button class="btn btn-success">Применить</button>
                    </div>
                @endif
                @if (in_array( 'ClientsFilter',$accesses))
                    <div class="col-auto">
                        <a href="{{route('admin.clients.index')}}" class="btn btn-success">Сбросить</a>
                    </div>
                @endif

            </div>
        </div>
    </div>
    @include('form.close')

    <div class="col-12 h5 p-3  ">
        {{$filterText}}
    </div>
    <div class="table-responsive">
        <table class="table pt-1 w-100 table-striped">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Покупатель</th>
                <th>email</th>
                <th>Телефон</th>
                <th>Пол</th>
                <th>Статус</th>
                <th>Источник</th>
                <th>Д/Р</th>
                <th>Возраст</th>
                <th>Карты</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clients as $client)
                <tr class="table-{{$client->color}} @if($client->status_text=='Лид') table-primary @endif">
                    <td>

                        <a href="@if (in_array( 'clientDetailsView',$accesses))
                            /admin/clients/client?client_id={{$client->id}} @else # @endif">
                            {{$client->id}}
                        </a>
                    </td>
                    <td>
                        <a href="@if (in_array( 'clientDetailsView',$accesses))
                            /admin/clients/client?client_id={{$client->id}} @else # @endif">
                            @if($client->name != '')
                                {{$client->name}}
                            @else
                                Нет имени
                            @endif
                        </a>
                    </td>
                    <td>{!! $client->work_email !!}</td>
                    <td>{!! $client->phones !!}</td>
                    <td>{{$client->gender}}</td>
                    <td>{{$client->status_text}}</td>
                    <td>{{$client->from}}</td>
                    <td>{{Carbon\Carbon::parse($client->birthday)->format('d.m.Y')}}</td>
                    <td>{{now()->diffInYears(Carbon\Carbon::parse($client->birthday))}}</td>

                    <td class="p-0">
                        @foreach($client->cards as $card)
                            <div class="card mb-1">
                                <div class="d-flex justify-content-between p-0 font-weight-bold
    @if($card->type == 'БК Активированная')
                                    table-success
@elseif($card->type == 'БК Не активированная')
                                    table-warning
@elseif(
        $card->type ==  'БК Деактивированная' or
        $card->type ==  'ДК Деактивированная' or
        $card->type ==  'ДК Заблокированная при создании БК' or
        mb_stripos($card->type, 'Заблокированная') > 0
        )
                                    table-danger
@else
                                    table-info
@endif

                                    ">
                                    #{{$card->id}}
                                    <div class="  ">
                                        {{$card->type}}
                                    </div>
                                </div>
                                <table class="table mb-0">
                                    <tr>
                                        <td class="p-0">Тел/Код</td>
                                        <td class="p-0">{{$card->phone}}/{{$card->barcode}}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-0">Магазин</td>
                                        <td class="p-0">{{$card->shop->display_name ?? 'Неизвестно'}}</td>
                                    </tr>
                                    <tr>
                                        <td class="p-0">Дата открытия</td>
                                        <td class="p-0">
                                            {{$card->dateText}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-0">Сумма покупок</td>
                                        <td class="p-0">{{money($card->total)}}</td>
                                    </tr>
                                    @if(mb_strpos($card->type, 'ДК') === 0)
                                        <tr>
                                            <td class="p-0">Скидка</td>
                                            <td class="p-0">{{$card->sale}}%</td>
                                        </tr>
                                    @endif
                                    @if(mb_strpos($card->type, 'БК') === 0)
                                        <tr>
                                            <td class="p-0">Баллы</td>
                                            <td class="p-0">
                                                {{$card->balance}}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        @endforeach
                    </td>
                    {{--                <td>{{Carbon\Carbon::parse($client->card_date)->format('d.m.Y')}}</td>--}}
                    {{--                <td>{{$client->count}}</td>--}}
                    {{--                <td class="text-nowrap">{{money($client->total)}}</td>--}}
                    {{--                <td>{{isset($client->last_buy)?$client->last_buy->toDateString():''}}</td>--}}

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{$clients->withQueryString()->links()}}
@endsection
@section('script')
    <script>
        $(document).on('click', '.size', function () {
            $(this).autocomplete("search", $(this).val());
        });

        $('.size').each(function (a, b) {
            $(b).autocomplete({
                source: [
                    @foreach($sizes as $size)
                        "{{$size}}",
                    @endforeach
                ],
                minLength: 0,
            })
        });
        $(document).on('click', '.ui-menu-item', function () {
            $('#sizes').append("<div class='col-md-2 ml-2 my-2'><div class='row'><span class='col-8 btn btn-primary'>" + $(this)[0].innerText + "</span><button class='col-4 btn btn-secondary removeSize'>X</button><input type='text' value=' " + $(this)[0].innerText + "' name='size[]' hidden> </div></div>")
        })
        $(document).on('click', '.removeSize', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });

        $('#name2').keyup(function () {
            $('#name').val($(this).val());
        })
    </script>
@endsection
