<?php

namespace Ophim\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ophim\Core\Models\PaymentHistory;

class CoinController extends Controller
{
    public function index(User $id, Request $request)
    {
        $paymentHistories = PaymentHistory::query()
            ->where('user_id', $id->id)
            ->with('episode.movie')
            ->orderByDesc('id')
            ->get();

        return view('ophim::crud.coin', [
            'paymentHistories' => $paymentHistories,
            'user' => $id,
        ]);
    }

    public function update(User $id, Request $request)
    {
        if ($request->coin <= 0) {
            return redirect()
                ->back()
                ->with('add-coin-failed', 'Số xu thêm phải có giá trị > 0');
        }

        try {
            DB::beginTransaction();
            PaymentHistory::query()->create([
                'coin' => $request->coin,
                'user_id' => $id->id,
                'status' => 1,
                'type' => 1,
                'user_approve_id' => backpack_auth()->user()->id
            ]);
            $id->refresh();
            $id->coin += $request->coin;
            $id->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('add-coin-failed', 'Không thể thêm xu vào lúc này');
        }

        return redirect()
            ->back()
            ->with('add-coin-success', 'Bạn đã cộng thành cộng ' . $request->coin .' xu cho user #' . $id->id);
    }
}
