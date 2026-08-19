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
            ? 'https://api.lynk.id'
            : 'https://sandbox.lynk.id';
    }

    private function client(): PendingRequest
    {
        return Http::withToken(config('linkid.merchant_key'))
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

        // NOTE: Endpoint dan payload sesuai dokumentasi API Lynk.id merchant.
        // https://documenter.getpostman.com/view/43601478/2sBXc8o3kn
        // Jika API tidak tersedia, Lynk.id mungkin hanya berfungsi via webhook
        // (merchant membuat payment secara manual di dashboard Lynk.id).
        $payload = [
            'refId' => $order->invoice_number,
            'amount' => (int) $order->final_amount,
            'callback_url' => config('linkid.webhook_url'),
            'productName' => $order->item_name ?? 'Order #'.$order->invoice_number,
            'customer' => [
                'name' => trim($user->first_name.' '.$user->last_name),
                'email' => $user->email,
                'phone' => $user->phone ?? null,
            ],
        ];

        try {
            $response = $this->client()->post("{$this->baseUrl}/v1/payment-links", $payload);

            if (! $response->successful()) {
                Log::error('Lynk create payment link failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'order_id' => $order->id,
                ]);

                throw new \Exception('Lynk API error: '.$response->status());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Lynk service exception: '.$e->getMessage());

            throw $e;
        }
    }

    public function getPaymentLinkStatus(string $refId): ?array
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/v1/payment-links/{$refId}");

            if (! $response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Lynk get status failed: '.$e->getMessage());

            return null;
        }
    }
}
