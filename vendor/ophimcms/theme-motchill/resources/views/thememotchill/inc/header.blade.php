@php
    $logo = setting('site_logo', '');
    $brand = setting('site_brand', '');
    $title = isset($title) ? $title : setting('site_homepage_title', '');
@endphp

<div id="header">
    <div class="container">
        <div class="top">
            <div class="left logo">
                <a href="/">
                    <img src="/assets/logophimhay.png" style="width: 200px;">
                </a>
            </div>
            <div class="right-header">
                <div class="search-container relative">
                    <form id="form_search" method="GET" action="/">
                        <input type="text" id="keyword" name="search" autocomplete="off"
                            placeholder="Nhập tên phim bạn muốn tìm kiếm..." value="{{ request('search') }}" />
                        <i class="fa fa-search" onclick="$('#form_search').submit();"></i>
                    </form>
                </div>
                <div id="box-user-menu" class="actions-user right">
                    @if(backpack_auth()->user())
                        <div class="btn-group">
                            @php
                                $user = backpack_auth()->user();
                            @endphp
                            <button class="btn btn-default btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="
                                background: none;
                                border: none;
                                color: #da966e;
                                font-size: 17px;
                                display: flex;
                                align-items: center;
                                box-shadow: none;
                            ">
                                    <span>{{ $user->name }} (#{{ $user->id }})</span>
                                    <span style="padding: 0 5px;">-</span>
                                    <span style="padding-right: 5px;"> {{ $user->coin ?: 0 }} <img style="width: 20px;" src="{{ asset('assets/dongxu.png') }}"></span>
                                    <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="/nap-xu">Nạp xu</a></li>
                                <li><a href="/lich-su-nap-xu">Lịch sử xu</a></li>
                                <li><a href="{{ route('backpack.auth.logout') }}">Đăng xuất</a></li>
                            </ul>
                        </div>
                    @else
                        <ul>
                            <li style="width: unset; margin: 0 5px;">
                                <i class="fa fa-sign-in"></i>
                                <a href="{{ route('backpack.auth.login') }}">Đăng nhập</a>
                            </li>
                            <li style="width: unset; margin: 0 5px;">
                                <i class="fa fa-users"></i>
                                <a href="{{ route('backpack.auth.register') }}">Đăng ký</a>
                            </li>
                        </ul>
                    @endif
                </div>
                <div class="suggest-dns">
                    <p>Công cụ tìm kiếm phim.</p>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="main-menu">
        <ul class="container">
            @foreach ($menu as $item)
                @if (count($item['children']))
                    <li class="menu-item ">
                        <a title="{{ $item['name'] }}">
                            {{ $item['name'] }}
                        </a>
                        <ul class="sub-menu span-4 absolute">
                            @foreach ($item['children'] as $children)
                                <li class="sub-menu-item">
                                    <a title="{{ $children['name'] }}" href="{{ $children['link'] }}">{{ $children['name'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li class="menu-item {{$item['link'] === '/' ? 'active' : ''}}"><a title="{{ $item['name'] }}" href="{{ $item['link'] }}">{{ $item['name'] }}</a></li>
                @endif
            @endforeach
            @if(true)
                <li class="menu-item"><a title="Kiếm tiến" href="/kiem-tien">Kiếm tiền</a></li>
            @endif
        </ul>
    </div>
</div>
