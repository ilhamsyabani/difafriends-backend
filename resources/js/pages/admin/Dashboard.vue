<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    stats: {
        totalUsers: number;
        totalCourses: number;
        totalOrders: number;
        totalRevenue: number;
        pendingCourses: number;
        recentOrders: Array<{
            id: number;
            invoice_number: string;
            item_name: string;
            final_amount: number;
            status: string;
            created_at: string;
            user: { first_name: string; last_name: string };
        }>;
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
        month: 'short',
        year: 'numeric',
    });
}

function statusColor(status: string): string {
    const map: Record<string, string> = {
        paid: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        pending:
            'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        expired: 'bg-gray-100 text-gray-500',
        cancelled: 'bg-red-100 text-red-600',
    };
    return map[status] ?? 'bg-gray-100 text-gray-500';
}
</script>

<template>
    <AppLayout>
        <Head title="Admin Dashboard" />

        <div class="max-w-7xl space-y-6 p-6">
            <div>
                <h1 class="text-2xl font-bold">Dashboard Admin</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Ringkasan aktivitas platform DifaFriends
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div
                    v-for="stat in [
                        {
                            label: 'Total User',
                            value: stats.totalUsers,
                            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                            color: 'purple',
                        },
                        {
                            label: 'Total Kelas',
                            value: stats.totalCourses,
                            icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                            color: 'blue',
                        },
                        {
                            label: 'Total Order',
                            value: stats.totalOrders,
                            icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                            color: 'amber',
                        },
                        {
                            label: 'Total Revenue',
                            value: formatPrice(stats.totalRevenue),
                            icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            color: 'green',
                        },
                    ]"
                    :key="stat.label"
                    class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm text-gray-500">{{
                            stat.label
                        }}</span>
                        <div
                            :class="[
                                'flex h-9 w-9 items-center justify-center rounded-xl',
                                stat.color === 'purple'
                                    ? 'bg-purple-100 dark:bg-purple-900/30'
                                    : stat.color === 'blue'
                                      ? 'bg-blue-100 dark:bg-blue-900/30'
                                      : stat.color === 'amber'
                                        ? 'bg-amber-100 dark:bg-amber-900/30'
                                        : 'bg-green-100 dark:bg-green-900/30',
                            ]"
                        >
                            <svg
                                :class="[
                                    'h-5 w-5',
                                    stat.color === 'purple'
                                        ? 'text-purple-600'
                                        : stat.color === 'blue'
                                          ? 'text-blue-600'
                                          : stat.color === 'amber'
                                            ? 'text-amber-600'
                                            : 'text-green-600',
                                ]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    :d="stat.icon"
                                />
                            </svg>
                        </div>
                    </div>
                    <p class="text-2xl font-bold">{{ stat.value }}</p>
                </div>
            </div>

            <!-- Alert: Kelas pending review -->
            <div
                v-if="stats.pendingCourses > 0"
                class="flex items-center justify-between rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-900/20"
            >
                <div class="flex items-center gap-3">
                    <svg
                        class="h-5 w-5 text-amber-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                    <p
                        class="text-sm font-medium text-amber-800 dark:text-amber-300"
                    >
                        Ada {{ stats.pendingCourses }} kelas menunggu review
                    </p>
                </div>
                <Link
                    href="/admin/courses"
                    class="text-sm font-medium text-amber-700 hover:underline dark:text-amber-400"
                >
                    Review sekarang →
                </Link>
            </div>

            <!-- Recent Orders -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800"
            >
                <div
                    class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700"
                >
                    <h2 class="font-semibold">Transaksi Terbaru</h2>
                    <Link
                        href="/admin/orders"
                        class="text-sm text-purple-600 hover:underline"
                    >
                        Lihat semua
                    </Link>
                </div>

                <div
                    v-if="stats.recentOrders.length === 0"
                    class="py-10 text-center text-sm text-gray-400"
                >
                    Belum ada transaksi.
                </div>

                <table v-else class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th
                                class="px-5 py-3 text-left font-medium text-gray-500"
                            >
                                Invoice
                            </th>
                            <th
                                class="px-5 py-3 text-left font-medium text-gray-500"
                            >
                                User
                            </th>
                            <th
                                class="px-5 py-3 text-left font-medium text-gray-500"
                            >
                                Item
                            </th>
                            <th
                                class="px-5 py-3 text-left font-medium text-gray-500"
                            >
                                Total
                            </th>
                            <th
                                class="px-5 py-3 text-left font-medium text-gray-500"
                            >
                                Status
                            </th>
                            <th
                                class="px-5 py-3 text-left font-medium text-gray-500"
                            >
                                Tanggal
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-100 dark:divide-gray-700"
                    >
                        <tr
                            v-for="order in stats.recentOrders"
                            :key="order.id"
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/30"
                        >
                            <td
                                class="px-5 py-3 font-mono text-xs text-gray-400"
                            >
                                {{ order.invoice_number }}
                            </td>
                            <td class="px-5 py-3">
                                {{ order.user.first_name }}
                                {{ order.user.last_name }}
                            </td>
                            <td class="max-w-xs truncate px-5 py-3">
                                {{ order.item_name }}
                            </td>
                            <td class="px-5 py-3 font-semibold text-purple-600">
                                {{ formatPrice(order.final_amount) }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        statusColor(order.status),
                                    ]"
                                >
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-400">
                                {{ formatDate(order.created_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
