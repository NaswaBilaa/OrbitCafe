<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Services\FonnteService;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

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

    private function encryptId($id)
    {
        $method = 'AES-128-CTR'; 
        $key = config('app.key'); 
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method)); 
        
        $encrypted = openssl_encrypt($id, $method, $key, 0, $iv);
        
        $payload = base64_encode($iv . $encrypted);
        return rtrim(strtr($payload, '+/', '-_'), '=');
    }

    public static function createOrRefreshSnapToken(Order $order): string
    {
        self::configureMidtrans();

        if ($order->payment && $order->payment->snap_token) {
            return $order->payment->snap_token;
        }

        $order->loadMissing('items.menu');

        $item_details = [];
        foreach ($order->items as $item) {
            $item_details[] = [
                'id'       => $item->menu_id,
                'price'    => (int) ($item->subtotal / max($item->quantity, 1)),
                'quantity' => $item->quantity,
                'name'     => $item->menu->name,
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
        $order = Order::with(['items.menu', 'payment'])
            ->where('invoice_number', $invoice_number)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $snapToken = self::createOrRefreshSnapToken($order);

        return response()->json(['data' => $snapToken]);
    }

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

            try {
                $shortToken = $this->encryptId($order->id); 
                
                $trackingLink = route('payment.track', ['token' => $shortToken]);

                // 1. Siapkan Pesan WA
                $message  = "Halo kak *{$order->nama_lengkap}*! 👋\n\n";
                $message .= "Terima kasih, pembayaran untuk pesanan *{$order->invoice_number}* telah kami terima. ✅\n\n";
                $message .= "Pesanan kamu sedang kami proses. Kamu bisa memantau status pesanan (Live Tracking) melalui link berikut:\n\n";
                $message .= "👉 $trackingLink\n\n";
                $message .= "Mohon ditunggu ya! ☕\n*Orbit Cafe*";

                FonnteService::sendWhatsApp($order->no_telepon, $message);
                
                Log::info("WA Tracking sent to {$order->no_telepon}");

            } catch (\Exception $e) {
                Log::error("Gagal kirim WA Callback: " . $e->getMessage());
            }

        } elseif ($status == 'expire') {
            $order->update(['status' => 'expired']);
            $order->payment()->update(['payment_status' => 'expired']);
        } elseif ($status == 'deny' || $status == 'cancel') {
            $order->update(['status' => 'failed']);
            $order->payment()->update(['payment_status' => 'failed']);
        }

        return response()->json(['message' => 'Callback processed']);
    }

    public function success(string $invoice)
    {
        $order = Order::with(['items.menu', 'payment'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        $totalItems = $order->items()->sum('quantity');

        return view('payment.success', [
            'order'      => $order,
            'payment'    => $order->payment,
            'totalItems' => $totalItems,
        ]);
    }

    public function downloadReceipt(string $invoice)
    {
        $order = Order::with(['items.menu', 'payment'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        $totalItems = $order->items()->sum('quantity');

        return view('payment.receipt-image', [
            'order'      => $order,
            'payment'    => $order->payment,
            'totalItems' => $totalItems,
        ]);
    }
}