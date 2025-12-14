<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    public static function sendWhatsApp($target, $message)
    {
        $token = env('FONNTE_TOKEN');

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', 
            ]);

            return $response->json();
            
        } catch (\Exception $e) {
            \Log::error('Gagal kirim WA Fonnte: ' . $e->getMessage());
            return null;
        }
    }
}