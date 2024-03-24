@extends(backpack_view('blank'))

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">Quản lý xu User: #{{ $user->id }} - {{ $user->name }}</span>
        </h2>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-2">
            <h4>Nạp xu</h4>
            @if(session()->has('add-coin-failed'))
                <div style="color: #902b2b">{{ session()->get('add-coin-failed') }}</div>
            @endif
            @if(session()->has('add-coin-success'))
                <div style="color: #0AA31E">{{ session()->get('add-coin-success') }}</div>
            @endif
            <form method="POST" action="{{ route('admin-coin.add', ['id' => $user->id]) }}">
                @csrf
                <input type="number" name="coin" class="form-control" min="0" value="0" step="10">
                <button type="submit" class="btn-primary" style="margin-top: 10px;">Thêm xu</button>
            </form>
        </div>
        <div class="col-1"></div>
        <div class="col-9">
            <h4>Lịch sử sử dụng xu</h4>
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
                            <th>{{ $loop->index + 1 }}</th>
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
@endsection
