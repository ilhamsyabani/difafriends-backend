<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkIdService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('linkid.is_production')
            ? 'https://api.linkid.co.id'
            : 'https://sandbox.linkid.co.id';
    }

    private function client(): PendingRequest
    {
        return Http::withToken(config('linkid.api_key'))
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout(10)
            ->connectTimeout(5);
    }

    public function createPaymentLink(Order $order): array
    {
        $user = $order->user;

        $payload = [
            'external_id' => $order->invoice_number,
            'amount' => (int) $order->final_amount,
            'currency' => 'IDR',
            'callback_url' => config('linkid.callback_url'),
            'description' => $order->item_name ?? 'Pembayaran Order #'.$order->invoice_number,
            'customer' => [
                'name' => trim($user->first_name.' '.$user->last_name),
                'email' => $user->email,
                'phone' => $user->phone ?? null,
            ],
            'metadata' => [
                'order_id' => $order->id,
                'orderable_type' => $order->orderable_type,
                'orderable_id' => $order->orderable_id,
            ],
        ];

        try {
            $response = $this->client()->post("{$this->baseUrl}/v1/payment-links", $payload);

            if (! $response->successful()) {
                Log::error('Link.id create payment link failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'order_id' => $order->id,
                ]);

                throw new \Exception('Link.id API error: '.$response->status());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Link.id service exception: '.$e->getMessage());

            throw $e;
        }
    }

    public function getPaymentLinkStatus(string $externalId): ?array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/v1/payment-links/{$externalId}");

            if (! $response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Link.id get status failed: '.$e->getMessage());

            return null;
        }
    }
}
