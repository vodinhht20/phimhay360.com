<?php

namespace Ophim\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ophim\Core\Models\CashHistory;
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
                'type' => $request->type,
                'user_approve_id' => backpack_auth()->user()->id
            ]);
            $id->refresh();
            $id->coin += $request->coin;
            $id->save();
            if ((int) $request->type === 1) {
                if ($id->reference_user_id > 0) {
                    $this->giftCash($id->reference_user_id, 0, $id->id, $request->coin);
                }
            }
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

    public function incress(User $id, Request $request)
    {
        if ($request->cash <= 0) {
            return redirect()
                ->back()
                ->with('incress-cash-failed', 'Số hoa hồng phải có giá trị > 0');
        }

        if ($request->cash <= $id->cash) {
            return redirect()
                ->back()
                ->with('incress-cash-failed', 'Số hoa hồng phải nhỏ hơn hoặc bằng số đang có');
        }

        try {
            DB::beginTransaction();
            CashHistory::query()->create([
                'cash' => $request->cash,
                'user_id' => $id->id,
                'cash_from_user_id' => 0,
                'desciption' => $request->description,
                'status' => 1,
                'type' => 2,
                'user_approve_id' => 1
            ]);
            $id->refresh();
            $id->cash -= $request->cash;
            $id->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('incress-cash-failed', 'Không thể trừ hoa hồng vào lúc này');
        }

        return redirect()
            ->back()
            ->with('incress-cash-success', 'Bạn trừ thành cộng ' . $request->cash .' hoa hồng của user #' . $id->id);
    }


    public function giftCash(int $parrentId, $init = 0, $srcId = 0, $coin): void
    {
        if ($init === 4) {
            return;
        }

        $parrent = User::find($parrentId);
        if (empty($parrent)) {
            return;
        }

        $percent = 0;
        switch ($init) {
            case 0:
                $percent = 10;
                break;
            case 1:
                $percent = 5;
                break;
            case 2:
                $percent = 2;
                break;
            case 3:
                $percent = 1;
                break;
        }
        $cash = ($percent * $coin * 1000) / 100;
        CashHistory::query()->create([
            'cash' => $cash,
            'user_id' => $parrent->id,
            'cash_from_user_id' => $srcId,
            'desciption' => 'Cộng tiền hoa hồng do F'.$srcId + 1 . ' nạp xu',
            'status' => 1,
            'type' => 1,
            'user_approve_id' => 1
        ]);
        $parrent->cash += $cash;
        $parrent->save();

        if (empty($parrent->reference_user_id)) {
            return;
        }

        $this->giftCash($parrent->reference_user_id, $init + 1, $parrent->id, $coin);
    }
}
