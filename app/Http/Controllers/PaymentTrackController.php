<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Crypt;

class PaymentTrackController extends Controller
{
    private function decryptId($token)
    {
        try {
            $method = 'AES-128-CTR';
            $key = config('app.key');
            $payload = base64_decode(strtr($token, '-_', '+/'));
            
            $ivLength = openssl_cipher_iv_length($method);
            
            $iv = substr($payload, 0, $ivLength);
            $encrypted = substr($payload, $ivLength);
            
            return openssl_decrypt($encrypted, $method, $key, 0, $iv);
        } catch (\Exception $e) {
            return null; 
        }
    }

    public function show(string $token)
    {
        $orderId = $this->decryptId($token);
        if (!$orderId || !is_numeric($orderId)) {
            abort(404, 'Link tracking tidak valid.');
        }

        $order = Order::with([
            'items.menu',
            'payment',
            'table',
            'items.toppings.topping',
        ])->find($orderId);

        if (!$order) {
            abort(404, 'Pesanan tidak ditemukan.');
        }

        $queueNow = Order::whereIn('status', ['paid', 'processing'])
            ->where('created_at', '<', $order->created_at)
            ->count() + 1;

        $estimatedMinutes = max(5, $queueNow * 5);

        $statusLabel = match ($order->status) {
            'paid', 'processing' => 'Sedang diproses',
            'ready'             => 'Siap diambil',
            'completed'         => 'Selesai',
            'expired', 'failed' => 'Gagal / Expired',
            'pending'           => 'Menunggu pembayaran',
            default             => ucfirst($order->status),
        };

        return view('payment.track', compact(
            'order',
            'queueNow',
            'estimatedMinutes',
            'statusLabel'
        ));
    }
}
