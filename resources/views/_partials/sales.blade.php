<div class="card m-3 px-3 pt-3" @if($sale->type == 'Возврат') style="background: rgba(255,0,0,.3)" @endif >
    {{--    @if((is_object($sale->shop) and $sale->shop->status == 1) or ($sale->shop->id == 21))--}}
    <div class="p-0 m-0">

        <div class="d-flex justify-content-between">
            <strong>#{{$sale->id}}</strong>
            <strong>{{$sale->number}}</strong>
        </div>
        <br>
        @if($sale->shop != '')
            Тип: {{$sale->type}} <br>
            Дата: {{\Carbon\Carbon::parse($sale->date)->format('d.m.Y H:i')}}<br>
            Магазин: {{$sale->shop->display_name}} <br>
        @endif
        Продавец: {{$sale->user->name ?? 'Неизвестно'}}
    </div>
    <div class="m-0">
        <table class="table p-1">
            <thead>
            <tr>
                <th class="p-1" style="font-size: 10px;">Наименование</th>
                <th class="p-1" style="font-size: 10px;">Количество</th>
                <th class="p-1" style="font-size: 10px;">Цена</th>
                <th class="p-1" style="font-size: 10px;">Скидка</th>
            </tr>
            </thead>
            <tbody>
            @foreach($sale->sale_products as $sale_product)
                <tr>
                    <td class="p-1 "
                        style="font-size: 10px;">
                        {{$sale_product->product->name}}
                        {{$sale_product->product_prop->name}}
                    </td>
                    <td class="p-1 "
                        style="font-size: 10px;">
                        {{$sale_product->count}}
                    </td>
                    <td class="p-1 "
                        style="font-size: 10px;">
                        {{$sale_product->price}}
                    </td>
                    <td class="p-1 "
                        style="font-size: 10px;">
                        {{$sale_product->autosale_total}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if(isset($_SERVER['REDIRECT_URL']))
        @if($sale->realisation == 1 and $_SERVER['REDIRECT_URL']=='/admin/clients/client')
            <div class="form-group">
                <select name="status" id="" class="form-control statusSale" data-sale="{{$sale->id}}">
                    @if($sale->status == NULL)
                        <option value="packAndDelivery" disabled selected="selected">Статус заказа</option>
                    @endif
                    <option value="packAndDelivery" @if($sale->status == 'packAndDelivery') selected @endif>Упакован и
                        доставлен
                    </option>
                    <option value="packAndPickup" @if($sale->status == 'packAndPickup') selected @endif>Упаковка и
                        самовывоз
                    </option>
                </select>
            </div>
        @endif
    @endif
    <div class="text-right">
        <strong>Итог: {{money($sale->total)}}</strong>
    </div>
    @if(!isset($infoFlag))
        @if(($sale->client_id) != '109999')
            <div class="row">
                <div class="alert alert-info w-100 px-3 py-1 m-0" style="font-size: 10px;">
                    Покупатель:
                    <a href="/admin/clients/client?client_id={{$sale->client->id}}">
                        @if( $sale->client->name != '')
                            {{$sale->client->name}}
                        @else
                            Нет имени
                        @endif
                    </a>
                </div>
            </div>
        @endif
    @endif
    {{--    @endif--}}
</div>
