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
        <li class="active">Nạp xu</li>
    </ol>
    @if(session()->has('lost_coin'))
        <marquee>🔈 Số xu không đủ khả dụng để xem phim, 🔥Vui lòng nạp thêm xu để xem tiếp!💥</marquee>
    @endif
@endsection

@section('content')[
    <div class="left-content" id="page-info">
        <div class="blockbody">
            <section class="main-layout pd-15" style="line-height: 1.6">
                <div style="font-size: 18px" class="primary-color">
                    <i class="fa fa-university"></i>
                    Chuyển khoản qua ngân hàng, Internet Banking hoặc máy ATM
                </div>
                <div>
                    <h4>Nội dung chuyển khoản</h4>
                    <div class="content_payment">
                        napxulientay{{ $user->id }}
                    </div>
                </div>
                <div class="payment_item_box">
                    <div style="flex: 0.5">
                        <img style="max-height: 60px" src="https://website5s.net/files/uploads/2019/08/techcombank.png">
                        <div style="line-height: 35px">
                            <div>Ngân hàng: <b>Techcombank</b></div>
                            <div>Chủ TK: Võ Văn Định</div>
                            <div>Số TK: <b>9705999999</b></div>
                            <div>Chi nhánh: <b>Mỹ Đình - Hà Nội</b></div>
                        </div>
                    </div>
                    <div style="flex: 0.5">
                        <img style="max-width: 80%; border-radius: 10px;" src="{{ asset('assets/qr_tpbank.jpg') }}">
                    </div>
                </div>
                <div class="payment_item_box">
                    <div style="flex: 0.5">
                        <img style="max-height: 60px" src="https://website5s.net/files/uploads/2023/11/Logo-TPB.png">
                        <div style="line-height: 35px">
                            <div>Ngân hàng: <b>TP Bank</b></div>
                            <div>Chủ TK: Võ Văn Định</div>
                            <div>Số TK: <b>80383200111</b></div>
                            <div>Chi nhánh: <b>Mỹ Đình - Hà Nội</b></div>
                        </div>
                    </div>
                    <div style="flex: 0.5">
                        <img style="max-width: 80%; border-radius: 10px;" src="{{ asset('assets/qr_tpbank.jpg') }}">
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="/themes/motchill/js/filmdetail.js?v=1.2.2"></script>
    <script type="text/javascript" src="/themes/motchill/js/owl.carousel.min.js"></script>
@endpush
