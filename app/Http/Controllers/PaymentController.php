<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        self::configureMidtrans();
    }

    protected static function configureMidtrans(): void
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$is3ds        = true;
    }

    public static function createOrRefreshSnapToken(Order $order): string
    {
        self::configureMidtrans();

        // Kalau sudah ada snap_token, pakai yang lama
        if ($order->payment && $order->payment->snap_token) {
            return $order->payment->snap_token;
        }

        // Pastikan items dan drink ke-load
        $order->loadMissing('items.drink');

        // Item details untuk Midtrans
        $item_details = [];
        foreach ($order->items as $item) {
            $item_details[] = [
                'id'       => $item->drink_id,
                'price'    => (int) ($item->subtotal / max($item->quantity, 1)),
                'quantity' => $item->quantity,
                'name'     => $item->drink->name,
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'      => $order->invoice_number,
                'gross_amount'  => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->nama_lengkap,
                'phone'      => $order->no_telepon,
            ],
            'item_details' => $item_details,
        ];

        $snapToken = Snap::getSnapToken($params);

        // Simpan / update payment
        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'snap_token'      => $snapToken,
                'amount'          => $order->total_price,
                'payment_status'  => 'pending',
                'payment_method'  => 'midtrans', 
            ]
        );

        return $snapToken;
    }

    public function generateSnapToken($invoice_number)
    {
        $order = Order::with(['items.drink', 'payment'])
            ->where('invoice_number', $invoice_number)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $snapToken = self::createOrRefreshSnapToken($order);

        return response()->json(['data' => $snapToken]);
    }

    // Callback Midtrans
    public function callback(Request $request)
    {
        self::configureMidtrans();

        $notif   = new \Midtrans\Notification();
        $status  = $notif->transaction_status;
        $orderId = $notif->order_id;

        $order = Order::where('invoice_number', $orderId)->first();

        if (!$order) {
            Log::warning('Midtrans callback: order not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($status == 'capture' || $status == 'settlement') {
            $order->update(['status' => 'paid']);
            $order->payment()->update([
                'payment_status' => 'paid',
                'payment_method' => $notif->payment_type,
                'payment_date'   => now()
            ]);
        } elseif ($status == 'expire') {
            $order->update(['status' => 'expired']);
            $order->payment()->update(['payment_status' => 'expired']);
        } elseif ($status == 'deny' || $status == 'cancel') {
            $order->update(['status' => 'failed']);
            $order->payment()->update(['payment_status' => 'failed']);
        }

        return response()->json(['message' => 'Callback processed']);
    }
}
