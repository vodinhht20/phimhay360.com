@extends('themes::layout')

@php
    $menu = \Ophim\Core\Models\Menu::getTree();
    $tops = Cache::remember('site.movies.tops', setting('site_cache_ttl', 5 * 60), function () {
        $lists = preg_split('/[\n\r]+/', get_theme_option('hotest'));
        $data = [];
        foreach ($lists as $list) {
            if (trim($list)) {
                $list = explode('|', $list);
                [$label, $relation, $field, $val, $sortKey, $alg, $limit, $template] = array_merge($list, ['Phim hot', '', 'type', 'series', 'view_total', 'desc', 4, 'top_thumb']);
                try {
                    $data[] = [
                        'label' => $label,
                        'template' => $template,
                        'data' => \Ophim\Core\Models\Movie::when($relation, function ($query) use ($relation, $field, $val) {
                            $query->whereHas($relation, function ($rel) use ($field, $val) {
                                $rel->where($field, $val);
                            });
                        })
                            ->when(!$relation, function ($query) use ($field, $val) {
                                $query->where($field, $val);
                            })
                            ->orderBy($sortKey, $alg)
                            ->limit($limit)
                            ->get(),
                    ];
                } catch (\Exception $e) {
                    # code
                }
            }
        }

        return $data;
    });
@endphp

@push('header')
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/owl.carousel.css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,500" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/font-face.css?v=1.3.1" />
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/font-awesome.css" />
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/default.css?v=0.3.9" />
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/styles.css?v=1.1.9" />
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/responsive.css?v=1.0.5" />
    @if(!(new \Jenssegers\Agent\Agent())->isDesktop())
        <link rel="stylesheet" type="text/css" href="/themes/motchill/css/ipad.css?v=1.0.5" />
    @endif
    <link rel="stylesheet" type="text/css" href="/themes/motchill/css/custom.css" />
    <script type="text/javascript" src="/themes/motchill/js/jquery.min.js"></script>
    <script type="text/javascript" src="/themes/motchill/js/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="/themes/motchill/js/bootstrap2.min.js"></script>
    <script type="text/javascript" src="/themes/motchill/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/themes/motchill/js/jquery.lazyload.min.js"></script>
    <script type="text/javascript" src="/themes/motchill/js/jquery.core.min.js"></script>
    <script type="text/javascript" src="/themes/motchill/js/functions.js?v=2.0.1"></script>
    <script type="text/javascript" src="/themes/motchill/js/js.cookie.js?v=2.1"></script>
@endpush

@section('body')
    <div id="page">
        @if((new \Jenssegers\Agent\Agent())->isDesktop())
            @include('themes::thememotchill.inc.header')
        @else
            @include('themes::thememotchill.inc.header_mobile')
        @endif
        <div id="content">
            <div class="main-content">
                <div class="container">
                    @if (get_theme_option('ads_header'))
                        {!! get_theme_option('ads_header') !!}
                    @endif
                    @yield('slider_recommended')
                    <div class="clear"></div>
                    @yield('breadcrumb')
                    @yield('content')
                    <div class="right-content">
                        @foreach ($tops as $top)
                            @include('themes::thememotchill.inc.sidebar.' . $top['template'])
                        @endforeach
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        {!! get_theme_option('footer') !!}
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("img.lazy").lazyload({
                effect: "fadeIn"
            });
        });
    </script>
@endpush

@section('footer')
    @if (get_theme_option('ads_catfish'))
        {!! get_theme_option('ads_catfish') !!}
    @endif
    <script src="/themes/motchill/js/jquery.raty.js"></script>
    <script>
        $(document).ready(function() {
            $('.top-star').raty({
                readOnly: true,
                numberMax: 5,
                half: true,
                score: function() {
                    return $(this).attr('data-rating');
                },
                hints: ["bad", "poor", "regular", "good", "gorgeous"],
                space: false
            });
        })
    </script>

    {!! setting('site_scripts_google_analytics') !!}
@endsection
