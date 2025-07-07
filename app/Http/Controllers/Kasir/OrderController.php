<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi\Transaksi;
use App\Models\Transaksi\TransaksiLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private Transaksi|null $order;
    private Collection|null $orderLists;
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->order = Transaksi::firstOrCreate(
            ['terima' => 0, 'kasir_id' => $this->user->id, 'tipe_bayar' => null],
            ['tanggal_transaksi' => now()]
        );

        if (!Carbon::parse($this->order->tanggal_transaksi)->isToday()) {
            $this->order->update(['tanggal_transaksi' => now()]);
        }

        $this->orderLists = TransaksiLog::where('id_transaksi', $this->order->id_transaksi)->get();
    }

    public function order()
    {
        return [
            'order'     => $this->order->only(['id', 'terima', 'subtotal', 'total', 'diskon']),
            'orderLists'     => $this->orderLists,
        ];
    }
}
