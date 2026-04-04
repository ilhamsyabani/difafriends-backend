<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    orders: {
        data: Array<{
            id: number;
            invoice_number: string;
            item_name: string;
            final_amount: number;
            status: string;
            created_at: string;
            expired_at: string | null;
            orderable: any;
            payments: Array<{ status: string; payment_type: string | null }>;
        }>;
        links: any[];
        meta: any;
    };
}>();

function formatPrice(price: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function statusColor(status: string): string {
    const map: Record<string, string> = {
        paid: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        pending:
            'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        expired:
            'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
        cancelled:
            'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
    };
    return map[status] ?? 'bg-gray-100 text-gray-500';
}

function statusLabel(status: string): string {
    const map: Record<string, string> = {
        paid: 'Lunas',
        pending: 'Menunggu Bayar',
        expired: 'Kedaluwarsa',
        cancelled: 'Dibatalkan',
        refunded: 'Dikembalikan',
    };
    return map[status] ?? status;
}
</script>

<template>
    <AppLayout>
        <Head title="Riwayat Transaksi" />

        <div class="max-w-6xl p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Riwayat Transaksi</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Semua transaksi pembelian kelas dan booking
                </p>
            </div>

            <!-- Empty -->
            <div
                v-if="orders.data.length === 0"
                class="py-20 text-center text-gray-400"
            >
                <svg
                    class="mx-auto mb-4 h-16 w-16 opacity-30"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />
                </svg>
                <p class="font-medium">Belum ada transaksi</p>
                <Link
                    href="/courses"
                    class="mt-2 inline-block text-sm text-purple-600 hover:underline"
                >
                    Mulai beli kelas →
                </Link>
            </div>

            <!-- List -->
            <div v-else class="space-y-4">
                <div
                    v-for="order in orders.data"
                    :key="order.id"
                    class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="mb-1 flex items-center gap-2">
                                <span class="font-mono text-xs text-gray-400">
                                    {{ order.invoice_number }}
                                </span>
                                <span
                                    :class="[
                                        'rounded-full px-2 py-0.5 text-xs font-medium',
                                        statusColor(order.status),
                                    ]"
                                >
                                    {{ statusLabel(order.status) }}
                                </span>
                            </div>
                            <p class="truncate font-semibold">
                                {{ order.item_name }}
                            </p>
                            <p class="mt-0.5 text-sm text-gray-500">
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>

                        <div class="shrink-0 text-right">
                            <p class="text-lg font-bold text-purple-600">
                                {{ formatPrice(order.final_amount) }}
                            </p>
                            <!-- Tombol bayar kalau masih pending -->
                            <button
                                v-if="order.status === 'pending'"
                                class="mt-2 rounded-lg bg-purple-600 px-3 py-1.5 text-xs text-white transition-colors hover:bg-purple-700"
                            >
                                Bayar Sekarang
                            </button>
                        </div>
                    </div>

                    <!-- Payment method -->
                    <div
                        v-if="order.payments.length > 0"
                        class="mt-3 border-t border-gray-100 pt-3 dark:border-gray-700"
                    >
                        <p class="text-xs text-gray-400">
                            Metode:
                            {{
                                order.payments[0].payment_type ??
                                'Belum dipilih'
                            }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="orders.links?.length > 3"
                class="mt-8 flex justify-center gap-1"
            >
                <Link
                    v-for="link in orders.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    :class="[
                        'rounded-lg px-3 py-2 text-sm transition-colors',
                        link.active
                            ? 'bg-purple-600 text-white'
                            : link.url
                              ? 'border border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800'
                              : 'cursor-not-allowed text-gray-300 dark:text-gray-600',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
