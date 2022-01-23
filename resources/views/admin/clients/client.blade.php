@extends('layouts.admin')

@section('h1')
    <a href="{{route('admin.clients.index')}}">Список клиентов</a> \ {{$client->f}}  {{$client->i}}  {{$client->o}}
@endsection

@section('actions')
@endsection
@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if(isset($errors) and count($errors) > 0)
        <div class="row text-center">
            <div class=" col-12 alert alert-danger">
                Для обновления карточки клиента необходимо ввести Имя клиента + Телефон/Email
            </div>
        </div>
    @endif
    <div class="btn-group-vertical  col-12 my-2  d-lg-none" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-primary btnViewClick" id="allInfoClient">Клиент</button>
        <button type="button" class="btn my-2 btn-secondary btnViewClick" id="clientActions">Взаимодействия
        </button>
        <button type="button" class="btn btn-secondary btnViewClick" id="clientStory">Изменения</button>
    </div>
    <div class="h3">Карты клиента:</div>
    <div class="row">
        @foreach($client->cards as $card)
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between p-1 font-weight-bold">
                            #{{$card->id}}
                        </div>
                        <table class="table mb-0">
                            <tr class="
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
                                <td class="p-1">Тип карты</td>
                                <td class="p-1">{{$card->type}}</td>
                            </tr>
                            <tr>
                                <td class="p-1">УИД</td>
                                <td class="p-1">{{$card->uid}}</td>
                            </tr>
                            <tr>
                                <td class="p-1">Код карты</td>
                                <td class="p-1">{{$card->barcode}}</td>
                            </tr>
                            <tr>
                                <td class="p-1">Телефон карты</td>
                                <td class="p-1">{{$card->phone}}</td>
                            </tr>
                            <tr>
                                <td class="p-1">Продавец</td>
                                <td class="p-1">{{$card->user->fi ?? 'Неизвестно'}}</td>
                            </tr>
                            <tr>
                                <td class="p-1">Магазин</td>
                                <td class="p-1">{{$card->shop->display_name ?? 'Неизвестно'}}</td>
                            </tr>
                            <tr>
                                <td class="p-1">Дата открытия</td>
                                <td class="p-1">
                                    {{$card->dateText}}
                                </td>
                            </tr>
                            <tr>
                                <td class="p-1">Сумма покупок</td>
                                <td class="p-1">{{money($card->total)}}</td>
                            </tr>
                            @if(mb_strpos($card->type, 'ДК') === 0)
                                <tr>
                                    <td class="p-1">Скидка</td>
                                    <td class="p-1">{{$card->sale}}%</td>
                                </tr>
                            @endif
                            @if(mb_strpos($card->type, 'БК') === 0)
                                <tr>
                                    <td class="p-1">Баллы</td>
                                    <td class="p-1">
                                        {{$card->balance}}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="h3">Данные клиента:</div>
            <div class="card card-body">
                @if (in_array('ClientCardUpdate',$accesses) or in_array('ClientsReadOnly',$accesses))
                    <form action="{{route('admin.clients.formUpdate')}}" method="post" enctype="multipart/form-data">
                        <input type="text" hidden name="client" value="{{$client->id}}">
                        @csrf
                        @endif
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group border-bottom pb-2">
                                    <strong>UID Клиента:</strong> {{$client->uid}}
                                    <hr class="my-1">
                                    <strong>Заполнение карточки:</strong> <span class="@if($percent < 100) text-danger @elseif($percent < 200) text-success @else text-primary @endif">{{round($percent)}}%</span>
                                    <hr class="my-1">
                                    <strong>Соглашение:</strong> {{!is_null($client->license_accept)?'Принятно':'Не принято' }} <br>
                                    <hr class="my-1">
                                    <strong>Бот:</strong>
                                    @if($client->tg_status == 1)
                                        <span class="text-success">Активирован</span>
                                    @else
                                        <span class="text-danger">Не активирован</span>
                                    @endif
                                    <hr class="my-1">
                                    <strong>Магазин:</strong>
                                    @if ($client->shop)
                                        {{$client->shop->display_name}}
                                    @else
                                        Пусто
                                    @endif
                                    <hr class="my-1">
                                    {{--                                        <strong>UID ДК:</strong> {{isset($client->card_uid)?$client->card_uid:'Нет'}}<br>--}}
                                    {{--                                        <strong>Номер ДК:</strong> {{isset($client->card_barcode)?$client->card_barcode:'Нет'}}<br>--}}
                                    {{--                                        <strong>Дата регистрация ДК:</strong> {{isset($client->card_date)?$client->card_date:'Нет'}}<br>--}}
                                    {{--                                        <strong>Скидка по ДК:</strong> {{$client->fix_sale??0}}% <br>--}}
                                    {{--                                        <strong>Сумма покупок:</strong> {{isset($client->total)?$client->total:0}} <br>--}}
                                    <strong>Источник :</strong> {{!empty($client->from)?$client->from:'DIT'}}
                                    <hr class="my-1">
                                    <strong>Дата создания клиента в DIT:</strong> {{\Carbon\Carbon::parse($client->created_at)->format('d.m.Y')}}
                                </div>
                                <div class="form-group">
                                    <label for="f">Фамилия:</label>
                                    <input type="text" name="f" id="f" class="form-control" value="{{$client->f}}"
                                           @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses) and empty($client->f)) @else disabled @endif @endif>

                                </div>
                                <div class="form-group">
                                    <label for="i">Имя:</label>
                                    <input type="text" name="i" id="i" class="form-control" value="{{$client->i}}"
                                           @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->i)) @else disabled @endif @endif>
                                    @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->i)) @else
                                        <input type="text" name="i" hidden value="{{$client->i}}"> @endif @endif
                                    @if($errors->first('i'))
                                        <div class="alert alert-danger">
                                            Поле необходимо заполнить!
                                        </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="o">Отчество:</label>
                                    <input type="text" name="o" id="o" class="form-control" value="{{$client->o}}"
                                           @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->o)) @else disabled @endif @endif>
                                </div>
                                <div class="form-group">
                                        <select name="status" id="status" class="form-control"
                                                @if(!in_array('ClientCardUpdate',$accesses))  @if(in_array('ClientsReadOnly',$accesses) and empty($client->status)) @else disabled @endif @endif>
                                            <option value="lead" @if($client->status == 'lead') selected @endif >Лид
                                            </option>
                                            <option value="buyer" @if($client->status == 'buyer') selected @endif >
                                                Покупатель
                                            </option>
                                            <option value="const_buyer"
                                                    @if($client->status == 'const_buyer') selected @endif >Постоянный
                                                покупатель
                                            </option>
                                        </select>
                                    <div id="leadForm" class="p-3">
                                        <div class="form-group sizeCard">
                                            <label for="size">Размер</label>
                                            @if(isset($client->size) and $client->size != '[]')
                                                @foreach($client->size as $key => $size)
                                                    <div class="col-12 my-1">
                                                        <div class="row size{{$size}}">
                                                            <input
                                                                class="col-10 col-lg-10 form-control sizeAutocompleteValue"
                                                                type="text"
                                                                name="size[]"
                                                                value="{{$size}}">
                                                            <button data-size="{{$size}}"
                                                                    class="btnRemove col-1 btn btn-danger">-
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </div>

                                        <div class="form-group">
                                            <label for="salePath">Канал продажи</label>
                                            <input type="text" id="salePath" name="salePath" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="text">Комментарий</label>
                                            <input type="text" id="text" name="text" class="form-control">
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-success">Сохранить</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="birthday">Дата рождения:</label>
                            <input type="text"
                                   class="form-control date" name="birthday" id="birthday"
                                   @if(in_array('ClientsReadOnly',$accesses) and !in_array('ClientCardUpdate',$accesses) and !in_array('clientsBirthdayEdit',$accesses))
                                   readonly="readonly"
                                   @endif
                                   value="{{!empty($client->birthday)?(\Carbon\Carbon::parse($client->birthday)->format('d.m.Y')):''}}">
                        </div>
                        <div class="form-group">
                            <label for="">Выбор цвета маркера:</label>
                            <select name="color" class="form-control" id="">
                                @foreach(\App\Models\Client::$colors as $key => $color)
                                    <option @if(isset($client->color) and $client->color == $key) selected @endif value="{{$key}}">{{$color}}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="#" class="btn  btn-block bg-secondary  text-light my-3 clickPriority">Приоритеты</a>
                        <div class="priority d-none">
                            <div class="form-group">
                                <input type="text" class="form-control" name="priority_communicate"
                                       placeholder="Предпочтительный канал коммуникации"
                                       value="{{$client->priority['communicate']??''}}"
                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->priority['communicate'])) @else disabled @endif @endif>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="priority_delivery"
                                       placeholder="Предпочтительный способ доставки"
                                       value="{{$client->priority['delivery']??''}}"
                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->priority['delivery'])) @else disabled @endif @endif>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" name="priority_socials"
                                       placeholder="Предпочтительная социальная сеть"
                                       value="{{$client->priority['socials']??''}}"
                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses) and empty($client->priority['socials'])) @else disabled @endif @endif>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary btn-block btn-danger  my-3 clickAddress">Адрес</a>
                        <div class="address d-none">
                            <div class="form-group">
                                <label for="region">Регион:</label>
                                <input type="text" name="region" id="region" class="form-control"
                                       value="{{$client->address['region']??''}}"
                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->address['region'])) @else disabled @endif  @endif>
                            </div>
                            <div class="form-group">
                                <label for="town">Населенный пункт:</label>
                                <input type="text" name="town" id="town" class="form-control"
                                       value="{{$client->address['town']??''}}"
                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->address['town'])) @else disabled @endif  @endif>
                            </div>
                            <div class="form-group">
                                <label for="street">Улица:</label>
                                <input type="text" name="street" id="street" class="form-control"
                                       value="{{$client->address['street']??''}}"
                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->address['street'])) @else disabled @endif  @endif>
                            </div>
                            <div class="form-group">
                                <label for="house">Дом:</label>
                                <input type="text" name="house" id="house" class="form-control"
                                       value="{{$client->address['house']??''}}"
                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->address['house'])) @else disabled @endif  @endif>
                            </div>
                            <div class="form-group">
                                <label for="flat">Квартира:</label>
                                <input type="text" name="flat" id="flat" class="form-control"
                                       value="{{$client->address['flat']??''}}"
                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)  and empty($client->address['flat'])) @else disabled @endif  @endif>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="phone" id="sizeAdd">Размер:</label>
                                @if(isset($client->client_claims[0]->size) and ($client->client_claims[0]->size) != "[]")
                                    @foreach($client->client_claims[0]->size as $name => $size)
                                        <div class="col-12 my-3">
                                            <div class="row">
                                                <input type="text"
                                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)) readonly="readonly"
                                                       @endif  @endif class="col-6 form-control sizeAutoComplete "
                                                       name="sizesLabels[]" placeholder="Белье(например трусы)"
                                                       value="{{$name}}">
                                                <input type="text"
                                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)) readonly="readonly"
                                                       @endif  @endif class="form-control col-4 sizeAutocompleteValue"
                                                       name="sizes[]" placeholder="Введите только размер"
                                                       value="{{$size}}">
                                                @if(in_array('ClientsReadOnly',$accesses) and !in_array('ClientCardUpdate',$accesses))
                                                @else
                                                    <div class="col-2 btn btn-danger blockDeleter">x</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <button class="btn btn-primary btn-block" id="sizeAddBtn">Добавить размер</button>
                            </div>
                            <div class="form-group">
                                <label for="measureAddBtn" id="measureAdd">Мерки:</label>
                                @if(isset($client->client_claims[0]->measure) and ($client->client_claims[0]->measure) != "[]")
                                    @foreach($client->client_claims[0]->measure as $name => $size)
                                        <div class="col-12 my-3">
                                            <div class="row">
                                                <input type="text"
                                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)) readonly="readonly"
                                                       @endif  @endif class="col-6 form-control measureAutoComplete"
                                                       name="measuresLabels[]" placeholder="Мерка (например грудь)"
                                                       value="{{$name}}">
                                                <input type="text"
                                                       @if(!in_array('ClientCardUpdate',$accesses)) @if(in_array('ClientsReadOnly',$accesses)) readonly="readonly"
                                                       @endif @endif name="measures[]" class="form-control col-4"
                                                       placeholder="Значение" value="{{$size}}">
                                                @if(in_array('ClientCardUpdate',$accesses))
                                                    <div class="col-2 btn btn-danger blockDeleter">x</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <button class="btn btn-primary btn-block" id="measureAddBtn">Добавить мерки</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="phoneCard">Телефон:</label>
                            @if(!empty($client->phone) and isset($client->phone) and $client->phone !='null' and $client->phone != '[]')
                                @if($client->phone)
                                    @foreach($client->phone as $phone)
                                        <div class="col-12 row my-1 mr-0 pr-0">
                                            <input class="col-10 @if(empty($client->phone) or in_array('clientsPhoneEdit',$accesses)) col-md-11 @else col-md-12 @endif form-control phonesMask" type="text"
                                                   name="phone[]"
                                                   @if(in_array('ClientsReadOnly',$accesses) and !in_array('ClientCardUpdate',$accesses) and !in_array('clientsPhoneEdit',$accesses)) readonly="readonly"
                                                   @endif
                                                   value="{{$phone}}">
                                            @if(in_array('ClientsReadOnly',$accesses) and !in_array('ClientCardUpdate',$accesses))
                                            @else
                                                @if(empty($client->phone) or in_array('clientsPhoneEdit',$accesses))

                                                    <button class="btnRemove col-1 btn btn-danger">-</button>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                            @if(empty($client->phone) or in_array('clientsPhoneEdit',$accesses))

                                <div class="row col-12 mr-0 pr-0">
                                    <input type="text" class="form-control col-10 col-md-11 phonesMask " id="phone">
                                    <button class="btn btn-success col-1" id="phoneAdd">+</button>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="email" class="emailCard">Email:</label>
                            <div class="row col-12 mr-0 pr-0">
                                <input type="text" name="email" class="form-control" value="{{$client->email[0]??''}}"
                                       @if(in_array('ClientsReadOnly',$accesses) and !in_array('ClientCardUpdate',$accesses) and !empty($client->email[0])) readonly="readonly"
                                       @endif
                                       id="email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="socials" class="socialCard" id="socialAdd">Соц сети:</label>
                            @if(!empty($client->socials) and isset($client->socials) and $client->socials != '[]')
                                @foreach($client->socials as $key => $socials)
                                    <div class="col-12 my-3">
                                        <div class="row"><select name="socialsLabels[]" class="col-6"
                                                                 @if(in_array('ClientsReadOnly',$accesses) and !in_array('ClientCardUpdate',$accesses)) readonly="readonly" @endif>
                                                <option @if($key == 'vk') selected @Endif value="vk">Vk</option>
                                                <option @if($key == 'odnoklassniki') selected
                                                        @Endif value="odnoklassniki">Odnoklassniki
                                                </option>
                                                <option @if($key == 'instagram') selected @Endif value="instagram">
                                                    Instagram
                                                </option>
                                                <option @if($key == 'facebook') selected @Endif value="facebook">
                                                    Facebook
                                                </option>
                                            </select><input type="text" class="col-6" value="{{$socials}}"
                                                            @if(in_array('ClientsReadOnly',$accesses) and !in_array('ClientCardUpdate',$accesses)) readonly="readonly"
                                                            @endif
                                                            name="socialsValues[]"></div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 my-3">
                                    <div class="row">
                                        <select name="socialsLabels[]" id="socialAdd" class="col-6">
                                            <option value="vk">Vk</option>
                                            <option value="odnoklassniki">Odnoklassniki</option>
                                            <option value="instagram">Instagram</option>
                                            <option value="facebook">Facebook</option>
                                        </select>
                                        <input type="text" class="col-6" name="socialsValues[]"></div>
                                </div>
                            @endif
                            <button class="btn btn-primary btn-block" id="socialsAddBtn">Добавить социальную сеть
                            </button>
                            @if(isset($client->client_claims[0]->id))
                                <select name="sales_channels" id="sales_channels" class="form-control my-2">
                                    @if(!isset($client->client_claims[0]->sales_channels_id) or empty($client->client_claims[0]->sales_channels_id))
                                        <option selected="true" disabled="disabled">Канал продаж</option> @endif
                                    @foreach($sales_channels as $channel)
                                        <option
                                            value="{{$channel->id}}" @if(isset($client->client_claims[0]->sales_channels_id) and $client->client_claims[0]->sales_channels_id == $channel->id) selected @endif >{{$channel->name}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        @if (in_array('ClientCardUpdate',$accesses) or in_array('ClientsReadOnly',$accesses))
                            <div class="col-12">
                                <div class=" row d-flex justify-content-center mt-2">
                                    <button class=" btn btn-block btn-success" id="btnSave">Сохранить</button>
                                </div>
                            </div>
                        @endif
                        @if (in_array('ClientCardUpdate',$accesses) or in_array('ClientsReadOnly',$accesses))
                    </form>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="h3">История взаимодействий:</div>
            <div class="text">
                @if(count($answer) > 0)
                    @foreach($answer as $item)
                        @switch($item->formType)
                            @case('claim')
                            @if(isset($item->size) and $item->size != '[]')
                                <div class="card">
                                    <div class="card-body">
                                        <p>Заявка из {{$item->from}}.<br>
                                            <b>Размер:</b>
                                            @if (is_array($item->size))
                                                @foreach ($item->size as $key => $size)
                                                    {{$key}}:{{$size}}
                                                @endforeach
                                            @else
                                                Некорректные размеры
                                            @endif

                                            <br>
                                            @if($item->measure != '[]')
                                                <b>Мерки:</b>
                                                @if (is_array($item->measure))
                                                    @foreach ($item->measure as $key => $measure)
                                                        {{$key}}: {{$measure}}
                                                    @endforeach
                                                @else
                                                    {{$item->measure}}
                                                @endif
                                            @endif
                                            {{--                                                            @if(isset($item->text) and $item->text!='')--}}
                                            {{--                                                                <b>Комментарий:</b> {{$item->text}}--}}
                                            {{--                                                            @endif--}}
                                        </p>

                                        <div class="text-right">
                                            {{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s')}}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @break
                            @case('cart')
                            <div class="card card-body my-1">
                                <strong class="h5">Корзина:</strong>
                                @for($i=0;$i<count($item->prop);$i++)
                                    {{$i+1}})   {{$item->product[$i]->name}} {{$item->prop[$i]->name}}
                                    x {{$item->count[$i]}} <br>
                                @endfor
                                <div
                                    class="mt-2 text-right">{{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s')}}</div>
                            </div>
                            @break
                            @case('order')
                            @if(isset($item->text['order_id']))
                                <div class="card my-1">
                                    <div class="card-body">
                                        <strong class="h5">Заказ #{{$item->text['order_id']}} :</strong>
                                        <div>Номер платежа: {{$item->text['payment_id']}}</div>
                                        <div>Статус платежа: {{$item->status}}</div>
                                        <div class="text-right">
                                            {{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s')}}
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        {!! $item->text['cart'] ?? ''!!}
                                    </div>
                                </div>
                            @endif
                            @break
                            @case('sale')
                            @php($sale = $item)
                            <div class="col-lg-6 offset-lg-6 col-10 offset-2 my-2">
                                @include('_partials.sales',['infoFlag'=>TRUE])
                            </div>
                            @break
                            @case('message')
                            <div class="card my-2">
                                <div class="card-body">
                                    @if($item->user)
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-6 font-weight-bold p-0 m-0">
                                                    <strong
                                                        class="h5">{{$item->user->name??''}}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {!! changeTextToLink3($item->text) !!}

                                    @if(isset($item->image) and $item->image!='')
                                        <div class="row">
                                            @foreach(json_decode($item->image,TRUE) as $key =>$image)
                                                @if(in_array(explode('.',$image)[1],['jpeg','png','jpg','bmp','img']))
                                                    <a href="/{{$image}}" target="_blank"
                                                       data-fancybox="gallery-{{$key}}"><img
                                                            class="col-md-4 img-fluid my-3"
                                                            src="/{{$image}}"></a>
                                                @elseif(in_array(explode('.',$image)[1],['mkv','mp4','mov','avi','wmv']))
                                                    <video src="/{{$image}}" controls
                                                           class="img-fluid col-md-4 my-3"></video>
                                                @else
                                                    <div class="col-12">
                                                        <a href="/{{$image}}">Документ</a><br>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="text-right">
                                        {{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s')}}
                                    </div>

                                </div>
                            </div>
                            @break
                            @case('default')
                            <div class="card card-body my-2">
                                <strong class="h5">
                                    @switch($item->key)
                                        @case('call')
                                        Заказан звонок
                                        @break
                                        @case('feedback')
                                        Форма обратной связи
                                        @break
                                    @endswitch
                                </strong>
                                <div>{!! str_replace("\r\n",'<br>',$item->value) !!}</div>
                                <div class="text-right">
                                    {{\Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i:s')}}
                                </div>
                            </div>
                            @break
                        @endswitch
                    @endforeach
                @else
                    <div class="h4 text-center text-primary">
                        Взаимодействий с этим клиентом еще не происходило. <br> Вы можете оставить комментарий ниже.
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 mt-5">
        <div class="h3">История изменений:</div>
        <div class="row">
            @foreach($actions as $action)
                <div class="col-4">
                    <div class="card card-body mb-3 p-1">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-6 m-0 p-0">{{$action->user->name}}</div>
                                <div class="col-md-6 text-right">
                                    {{\Carbon\Carbon::parse($action->created_at)->format('d.m.Y H:i')}}
                                </div>
                            </div>
                        </div>
                        <table class="mt-3" style="font-size: 10px">
                            <thead class="border-bottom ">
                            <tr>
                                <th>Поле</th>
                                <th>Было</th>
                                <th>Стало</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(json_decode($action->info,TRUE) as $key => $value)
                                <tr>
                                    <td>
                                        @if(isset($actionNames[$key]))
                                            {{$actionNames[$key]}}
                                        @else
                                            {{$key}}
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_array($value['old']))
                                            @foreach($value['old'] as $newValue)
                                                {{$newValue}}<br>
                                            @endforeach
                                        @else
                                            @if($value['old'] != '[]' and $value['old']!='' )
                                                {{$value['old']}}
                                            @else
                                                пусто
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($key == 'dk')
                                            {{json_decode($value['new'], true)[0]['ВидДисконтнойКарты_Наименование']}}
                                            {{json_decode($value['new'], true)[0]['ПроцентСкидки']}}% от
                                            {{json_decode($value['new'], true)[0]['ДатаОткрытияКарты']}}
                                        @else
                                            @if(is_array($value['new']))
                                                @foreach($value['new'] as $newValue)
                                                    {{$newValue}}<br>
                                                @endforeach
                                            @else
                                                @if($value['new'] != '[]'  and $value['new']!='')
                                                    {{$value['new']}}
                                                @else
                                                    пусто
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection
@section('script')
    <script>

        $('#sales_channels').change(function () {
            $.ajax({
                type: 'get',
                url: '/clientSalesChannel',
                data: {
                    claim_id: {{$client->client_claims[0]->id??0}},
                    channel_id: $('#sales_channels').val()
                },
                success: function (data) {
                    console.log(data);
                }
            });
        });
        $('.btnViewClick').click(function (e) {
            e.preventDefault();
            $('.btnViewClick').removeClass('btn-primary');
            $('.btnViewClick').removeClass('btn-secondary');
            $('.btnViewClick').addClass('btn-secondary');
            $(this).removeClass('btn-secondary');
            $(this).addClass('btn-primary');

            $('.allClientsAction').removeClass('d-none');
            $('.allClientsAction').addClass('d-none');
            $('.' + $(this).prop('id')).removeClass('d-none');
        });


        //Валидация email
        $('#email').change(function () {
            if ($(this).val() != '') {
                if ($(this).val().indexOf('.') == -1 || $(this).val().indexOf('@') == -1) {
                    $('#btnSave').prop('disabled', true);
                    if (!$('.textEmailAlert').hasClass('checkEmail'))
                        $('#email').after("<div class='text-danger textEmailAlert checkEmail'>Email адрес указан неверно!</div>")
                } else {
                    $('#btnSave').prop('disabled', false);
                    $('.textEmailAlert').remove();
                }
            } else {
                $('#btnSave').prop('disabled', false);
                $('.textEmailAlert').remove();
            }
        })

        $('.clickPriority').click(function (e) {
            e.preventDefault();
            if ($('.priority').hasClass('d-none'))
                $('.priority').removeClass('d-none')
            else
                $('.priority').addClass('d-none')
        });
        $('.clickAddress').click(function (e) {
            e.preventDefault();
            if ($('.address').hasClass('d-none'))
                $('.address').removeClass('d-none')
            else
                $('.address').addClass('d-none')
        });
        $(document).on('click', '.blockDeleter', function () {
            $(this).parent().parent().remove()
        });
        $(document).on('click', '.sizeAutoComplete', function () {
            $(this).autocomplete("search", $(this).val());
        });
        $(document).on('click', '.measureAutoComplete', function () {
            $(this).autocomplete("search", $(this).val());
        });
        $(document).on('click', '.sizeAutocompleteValue', function () {
            $(this).autocomplete("search", $(this).val());
        });
        autoCompleteMeasure();
        autoCompleteSize();

        function autoCompleteSize() {
            $('.sizeAutoComplete').each(function (a, b) {
                $(b).autocomplete({
                    source: [
                        @foreach($sizeTypes as $size)
                            "{{$size}}",
                        @endforeach
                    ],
                    minLength: 0,
                })
            });
            $('.sizeAutocompleteValue').each(function (a, b) {
                $(b).autocomplete({
                    source: [
                        @foreach($sizes as $size)
                            "{{$size}}",
                        @endforeach
                    ],
                    minLength: 0,
                })
            });
        }

        function autoCompleteMeasure() {
            $('.measureAutoComplete').each(function (a, b) {
                $(b).autocomplete({
                    source: [
                        @foreach($measureTypes as $type)
                            "{{$type}}",
                        @endforeach
                    ],
                    minLength: 0,
                })
            });
        }

        $('#leadForm').slideUp();
        $(document).ready(function () {

            $('#socialsAddBtn').click(function (e) {
                e.preventDefault();
                $('#socialAdd').after('<div class="col-12 my-3"><div class="row"><select name="socialsLabels[]" id="" class="col-6"><option value="vk">Vk</option><option value="odnoklassniki">Odnoklassniki</option><option value="instagram">Instagram</option><option value="facebook">Facebook</option></select><input type="text" class="col-6" name="socialsValues[]"></div></div>\n')
            })

            $('#sizeAddBtn').click(function (e) {
                e.preventDefault();
                $('#sizeAdd').after('<div class="col-12 my-3"><div class="row"><input type="text" class="col-6 form-control sizeAutoComplete" name="sizesLabels[]" placeholder="Белье(например трусы)"><input type="text" name="sizes[]" class="form-control col-4 sizeAutocompleteValue" placeholder="Введите только размер"><div class="col-2 btn btn-danger blockDeleter">x</div></div></div>')
                autoCompleteSize();
            })
            $('#measureAddBtn').click(function (e) {
                e.preventDefault();
                $('#measureAdd').after('<div class="col-12 my-3"><div class="row"><input type="text" class="col-6 form-control measureAutoComplete" name="measuresLabels[]" placeholder="Мерка (например грудь)"><input type="text" name="measures[]" class="form-control col-4" placeholder="Значение"><div class="col-2 btn btn-danger blockDeleter">x</div></div></div>')
                autoCompleteMeasure();
            })


            $('#phoneAdd').click(function (e) {
                e.preventDefault();
                $('#phoneNoup').remove();
                $('.phoneCard').after('<div class="row col-12 my-1 pr-0 mr-0"><input class="col-10 col-md-11 form-control phonesMask" type="text" name="phone[]" value="' + $('#phone').val() + '"><button class="btnRemove col-1 btn btn-danger">-</button></div>')
                $('#phone').val('');
                $('.phonesMask').mask('+7(000)000-0000');
            });
            $('.phonesMask').mask('+7(000)000-0000');

            $('#emailAdd').click(function (e) {
                e.preventDefault();
                $('#emailNoup').remove();
                $('.emailCard').after('<div class="row col-12 my-1 mr-0 pr-0"><input class="col-10 col-md-11 form-control" type="text" name="email[]" value="' + $('#email').val() + '"><button class="btnRemove col-1 btn btn-danger">-</button></div>')
                $('#email').val('');
            });


            $(document).on('click', '.btnRemove', function (e) {
                e.preventDefault();
                $(this).parent().remove();

                var sizeD = $(this).data('size');
                $('.size' + sizeD).remove();
            })
            $('#status').change(function () {
                if ($(this).val() == 'lead') {
                    $('#leadForm').slideDown();
                } else {
                    $('#leadForm').slideUp();
                }
            })
            $('.statusSale').change(function () {
                val = $(this).val();
                sale = $(this).data('sale');

                $.ajax({
                    type: 'get',
                    url: '/saleStatusChange',
                    data: {
                        val: val,
                        sale: sale
                    },
                    success: function (data) {
                        console.log(data);
                    }
                });
            })
        })
    </script>
@endsection
