<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    public function createSnapToken(Order $order): string
    {
        $user = $order->user;

        $params = [
            'transaction_details' => [
                'order_id'     => $order->invoice_number,
                'gross_amount' => (int) $order->final_amount,
            ],
            'customer_details' => [
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? '',
            ],
            'item_details' => [
                [
                    'id'       => $order->orderable_id,
                    'price'    => (int) $order->final_amount,
                    'quantity' => 1,
                    'name'     => substr($order->item_name, 0, 50), // max 50 char
                ],
            ],
            'expiry' => [
                'duration' => 1,
                'unit'     => 'hours',
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
