<label id="toggler-label" for="toggler">Поазать меню</label>
<input type="checkbox" id="toggler">
<div class="sidebar toggled">
    <ul class="navbar-nav d-flex">
        <li class="nav-item pt-3">
            <div style="width: 54px; margin: 0 auto;">
                @php
                    $user = auth()->user();
                        if(Cookie::has('admin_user_view'))
                            $user = \App\Models\User::query()->find(Cookie::get('admin_user_view'));
                @endphp
                {!! $user->img_html !!}
            </div>
            <span class="nav-link text-light text-center"><span>{{$user->name}}</span></span>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-success" href="#" id="statsDropdown" role="button"
               data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-fw fa-star"></i>
                <span>Favourites</span>
            </a>
            <div class="dropdown-menu" aria-labelledby="statsDropdown" x-placement="bottom-start"
                 style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(5px, 56px, 0px);">
                @foreach($favourites as $favourite)
                    <a class="dropdown-item"
                       href="{{route($favourite->value, $favourite->second_value)}}">{{$favourite->name}}</a>
                @endforeach
            </div>
        </li>
        @foreach($mains as $main)
            @if(!empty($main->second_value))
                <li class="nav-item">
                    <a class=" nav-link" href="{{ route($main->second_value) }}">
                        <i class="fas fa-fw fa-folder"></i>
                        <span>{{$main->name}}</span>
                    </a>
                </li>
            @else
                <li class="nav-item dropdown" data-toggle="tooltip" data-placement="top" title="{{$main->value}}">
                    <a class="nav-link dropdown-toggle menutooltip" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-fw fa-folder"></i>
                        <span>{{$main->name}}</span>
                    </a>
                    <div class="dropdown-menu">
                        @foreach($menus as $menu)
                            @if($menu->option_id == $main->id)
                                {{--                                @if($menu->value == 'wms')--}}
                                {{--                                    <a class="dropdown-item"--}}
                                {{--                                       href="{{route($menu->value, ['user_uid' => auth()->user()->uid])}}">{{$menu->name}}</a>--}}
                                {{--                                @else--}}
                                <a class="dropdown-item"
                                   href="{{route($menu->value, $menu->second_value)}}">{{$menu->name}}</a>
                                {{--                                @endif--}}
                            @endif
                        @endforeach
                    </div>
                </li>
            @endif
        @endforeach
        @if($user->superadmin)
            <li class="nav-item">
                <a class="text-warning nav-link" href="{{ route('admin.master') }}">
                    <i class="fas fa-fw fa-bolt"></i>
                    <span>Magic!</span>
                </a>
            </li>
        @endif
        {{--        @if(\Illuminate\Support\Facades\Cookie::has('admin_user_view'))--}}
        {{--            <li class="nav-item">--}}
        {{--                <a class="text-primary nav-link" href="{{ route('CookiesClear') }}">--}}
        {{--                    <i class="fas fa-fw fa-clock"></i>--}}
        {{--                    <span>Вернуться на свой аккаунт</span>--}}
        {{--                </a>--}}
        {{--            </li>--}}
        {{--        @endif--}}
        @auth
            <li class="nav-item">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a class="text-danger nav-link " href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-fw fa-window-close"></i>
                    <span>Exit</span>
                </a>
            </li>
        @endauth
    </ul>
</div>
