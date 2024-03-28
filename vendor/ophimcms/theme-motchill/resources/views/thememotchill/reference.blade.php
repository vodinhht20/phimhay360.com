@extends('themes::thememotchill.layout')

@push('header')
@endpush

@section('breadcrumb')
    <ol class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a href="/" itemprop="item">
                <span itemprop="name">Xem phim</span>
            </a>
            <meta itemprop="position" content="1"/>
        </li>
        <li class="active">Kiếm tiền</li>
    </ol>
@endsection

@section('content')
    <div class="left-content" id="page-info">
        <div class="blockbody">
            <h3>Giới thiệu cho bạn bè để nhận nhiều ưu đãi</h3>
            <div class="content_payment" style="width: 90%; font-size: 22px;">
                {{ route('backpack.auth.register', ['ref' => $user->reference_code]) }}
            </div>
            <p style="text-align: center;"><i>(Copy link để chia sẻ cho bạn bè)</i></p>
            <div style="text-align: center; margin-top: 40px;">
                <img src="{{ $imageUrl }}" style="width: 90%;">
                <p style="margin-top: 50px;">{{ $desciption }}</p>
            </div>

            <h3>Lịch sử hoa hồng</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div style="display: flex;
    justify-content: space-between;
    align-items: center;">
                        <span>Số tiền hoa hồng đang có: <b style="color: #0AA31E">{{ $user->cash }}</b></span>
                        <div style="display: flex;
                        justify-content: flex-end; grid-column-gap: 10px;
    align-items: center;">
                            <button class="btn btn-primary" onclick="alert('Vui lòng nhắn tin qua fanpage hoặc zalo để đổi xu')">Đổi xu</button>
                            <button class="btn btn-success" onclick="alert('Vui lòng nhắn tin qua fanpage hoặc zalo để rút tiền')">Rút tiền</button>
                        </div>
                    </div>
                </div>
                <table class="table" style="color: #c58560">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th>Ngày</th>
                        <th>Số hoa hồng</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($cashHistories as $cashHistory)
                            <tr>
                                <th>{{ $loop->index + 1 }}</th>
                                <td>{{ $cashHistory->created_at->format('H:i d/m/Y') }}</td>
                                <td>
                                    @if($cashHistory->type === 1)
                                        <span style="color: #0AA31E">+<b>{{ $cashHistory->cash }}</b></span>
                                    @else
                                        <span style="color: #902b2b">-<b>{{ $cashHistory->cash }}</b></span>
                                    @endif
                                </td>
                                <td>
                                    {{ $cashHistory->desciption }}
                                </td>
                                <td>
                                    @if($cashHistory->status === 1)
                                        <span style="color: #0AA31E">Đã xử lý</span>
                                    @else
                                        <span style="color: #0a90eb">Chưa xử lý</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="/themes/motchill/js/filmdetail.js?v=1.2.2"></script>
    <script type="text/javascript" src="/themes/motchill/js/owl.carousel.min.js"></script>
@endpush
