<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
// import { Badge } from '@/components/ui/badge';
import { Smartphone } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    order: {
        id: number;
        invoice_number: string;
        item_name: string;
        final_amount: number;
        status: string;
        created_at: string;
        expired_at: string | null;
        orderable: any;
        user: any; // Pastikan relasi user juga didefinisikan agar tidak error TypeScript
        payments: Array<{
            id: number;
            status: string;
            payment_type: string | null;
            amount: number;
            created_at: string;
        }>;
    };
}>();

// Helper untuk format Rupiah
const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
};

// Helper warna badge status menyesuaikan Light & Dark Mode
const statusColor = (status: string) => {
    const colors: Record<string, string> = {
        pending:
            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        paid: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        expired: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        cancelled:
            'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
        refunded:
            'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    };

    return (
        colors[status] ||
        'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300'
    );
};
</script>

<template>
    <!-- Gunakan optional chaining (?.) untuk mencegah error undefined saat initial load -->
    <Head
        :title="`Detail Transaksi - ${order?.invoice_number || 'Memuat...'}`"
    />

    <AppLayout>
        <!-- Bungkus dengan v-if="order" sebagai pengaman data -->
        <div v-if="order" class="max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Detail Transaksi
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ order.invoice_number }}
                    </p>
                </div>
                <Link :href="`/admin/orders`">
                    <Button
                        variant="outline"
                        class="dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Kembali
                    </Button>
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Kiri: Info Transaksi & User -->
                <div class="space-y-6 md:col-span-1">
                    <!-- Kartu Status Transaksi -->
                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                    >
                        <h3
                            class="mb-4 border-b border-gray-100 pb-2 text-lg font-semibold text-gray-900 dark:border-gray-800 dark:text-white"
                        >
                            Status Transaksi
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Status</span
                                >
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium uppercase',
                                        statusColor(order.status),
                                    ]"
                                >
                                    {{ order.status }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Tanggal Dibuat</span
                                >
                                <span
                                    class="font-medium text-gray-900 dark:text-gray-200"
                                >
                                    {{
                                        new Date(
                                            order.created_at,
                                        ).toLocaleDateString('id-ID', {
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric',
                                        })
                                    }}
                                </span>
                            </div>
                            <div
                                class="mt-4 flex justify-between border-t border-gray-100 pt-4 text-lg font-bold dark:border-gray-800"
                            >
                                <span class="text-gray-900 dark:text-white"
                                    >Total</span
                                >
                                <span class="text-primary dark:text-purple-400">
                                    {{ formatRupiah(order.final_amount) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Informasi Pembeli -->
                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                    >
                        <h3
                            class="mb-4 border-b border-gray-100 pb-2 text-lg font-semibold text-gray-900 dark:border-gray-800 dark:text-white"
                        >
                            Informasi Pembeli
                        </h3>
                        <div class="mb-4 flex items-center space-x-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800"
                            >
                                <img
                                    v-if="order.user?.photo"
                                    :src="order.user.photo"
                                    alt="Avatar"
                                    class="h-full w-full object-cover"
                                />
                                <span
                                    v-else
                                    class="text-sm font-bold text-gray-500 dark:text-gray-400"
                                >
                                    {{
                                        (order.user?.first_name?.[0] || '') +
                                        (order.user?.last_name?.[0] || '')
                                    }}
                                </span>
                            </div>
                            <div>
                                <p
                                    class="font-medium text-gray-900 dark:text-white"
                                >
                                    {{ order.user?.first_name }}
                                    {{ order.user?.last_name }}
                                </p>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ order.user?.email }}
                                </p>
                            </div>
                        </div>
                        <div
                            v-if="order.user?.phone"
                            class="flex items-center gap-3"
                        >
                            <Smartphone
                                class="mx-4 h-4 w-3 shrink-0 text-gray-500 dark:text-gray-400"
                            />

                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ order.user.phone }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Detail Item & Riwayat Payment -->
                <div class="space-y-6 md:col-span-2">
                    <!-- Kartu Detail Item -->
                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                    >
                        <h3
                            class="mb-4 border-b border-gray-100 pb-2 text-lg font-semibold text-gray-900 dark:border-gray-800 dark:text-white"
                        >
                            Detail Item
                        </h3>
                        <div class="flex flex-col space-y-3">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm font-medium text-gray-500 uppercase dark:text-gray-400"
                                    >Tipe Layanan</span
                                >
                                <span
                                    class="rounded bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-300"
                                >
                                    {{
                                        order.orderable_type.includes('Course')
                                            ? 'Kelas Online'
                                            : 'Sesi Pendamping'
                                    }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-sm font-medium text-gray-500 uppercase dark:text-gray-400"
                                    >Nama Item</span
                                >
                                <span
                                    class="font-medium text-gray-900 dark:text-white"
                                    >{{ order.item_name }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Riwayat Pembayaran -->
                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                    >
                        <h3
                            class="mb-4 border-b border-gray-100 pb-2 text-lg font-semibold text-gray-900 dark:border-gray-800 dark:text-white"
                        >
                            Riwayat Pembayaran (Midtrans)
                        </h3>
                        <div
                            v-if="order.payments && order.payments.length > 0"
                            class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800"
                        >
                            <table
                                class="w-full text-left text-sm text-gray-600 dark:text-gray-300"
                            >
                                <thead
                                    class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-800/50 dark:text-gray-400"
                                >
                                    <tr>
                                        <th class="px-4 py-3 font-medium">
                                            Waktu
                                        </th>
                                        <th class="px-4 py-3 font-medium">
                                            Metode
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center font-medium"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right font-medium"
                                        >
                                            Nominal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-100 dark:divide-gray-800"
                                >
                                    <tr
                                        v-for="payment in order.payments"
                                        :key="payment.id"
                                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/30"
                                    >
                                        <td class="px-4 py-3">
                                            {{
                                                new Date(
                                                    payment.created_at,
                                                ).toLocaleString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'short',
                                                    year: 'numeric',
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                })
                                            }}
                                        </td>
                                        <td class="px-4 py-3 uppercase">
                                            {{
                                                payment.payment_type
                                                    ? payment.payment_type.replace(
                                                          '_',
                                                          ' ',
                                                      )
                                                    : '-'
                                            }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                :class="[
                                                    'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium uppercase',
                                                    statusColor(payment.status),
                                                ]"
                                            >
                                                {{ payment.status }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-200"
                                        >
                                            {{ formatRupiah(payment.amount) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div
                            v-else
                            class="rounded-xl border border-dashed border-gray-200 py-8 text-center text-gray-500 dark:border-gray-800 dark:text-gray-400"
                        >
                            Belum ada respon webhook pembayaran tercatat dari
                            Midtrans.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skeleton Loading State (Opsional, akan muncul sepersekian detik sebelum v-if="order" terpenuhi) -->
        <div v-else class="flex min-h-[50vh] items-center justify-center">
            <div class="flex flex-col items-center space-y-4">
                <div
                    class="h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-purple-600 dark:border-gray-700 dark:border-t-purple-500"
                ></div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Memuat detail transaksi...
                </p>
            </div>
        </div>
    </AppLayout>
</template>
