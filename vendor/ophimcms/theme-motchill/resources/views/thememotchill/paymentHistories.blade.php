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
        <li class="active">Lịch sử xu</li>
    </ol>
@endsection

@section('content')
    <div class="left-content" id="page-info">
        <div class="blockbody">
            <h3>Lịch sử nạp xu</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p>Số xu hiện có: <b style="color: #87711d">{{ $currentCoin }}</b></p>
                    <p>Số xu đã nạp: <b style="color: #0AA31E">{{ $buyCoin }}</b></p>
                    <p>Số xu đã chi: <b style="color: #902b2b">{{ $paymentCoin }}</b></p>
                </div>
                <table class="table" style="color: #c58560">
                    <thead>
                    <tr>
                        <th>STT</th>
                        <th>Ngày</th>
                        <th>Số xu</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentHistories as $payment)
                            @php
                                $episode = $payment->episode;
                                $movie = optional($episode)->movie;
                            @endphp
                            <tr>
                                <th>{{ $loop->index + 1}}</th>
                                <td>{{ $payment->created_at->format('H:i d/m/Y') }}</td>
                                <td>
                                    @if($payment->type === 1)
                                        <span style="color: #0AA31E">+<b>{{ $payment->coin }}</b></span>
                                    @else
                                        <span style="color: #902b2b">-<b>{{ $payment->coin }}</b></span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->type === 1)
                                        Nạp xu vào tài khoản
                                    @else
                                        Trừ xu xem phim {{ optional($movie)->name }} - Tập {{ $episode->name }}
                                    @endif
                                </td>
                                <td>
                                    @if($payment->status === 1)
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
