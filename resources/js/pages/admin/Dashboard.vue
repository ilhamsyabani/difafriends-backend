<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import SectionHeader from '@/components/SectionHeader.vue';
import StatsCard from '@/components/StatsCard.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: 'admin/dashboard',
    },
];

const { formatPrice, formatDate } = useFormatters();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Dashboard Admin" />

        <div class="max-w-7xl space-y-6 p-6">
            <div>
                <h1 class="text-2xl font-bold">Dashboard Admin</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Ringkasan aktivitas platform DifaFriends
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <StatsCard
                    label="Total Pengguna"
                    :value="stats.totalUsers"
                    color="brand"
                >
                    <template #icon>
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
                            />
                        </svg>
                    </template>
                </StatsCard>

                <StatsCard
                    label="Total Kelas"
                    :value="stats.totalCourses"
                    color="blue"
                >
                    <template #icon>
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                            />
                        </svg>
                    </template>
                </StatsCard>

                <StatsCard
                    label="Total Order"
                    :value="stats.totalOrders"
                    color="amber"
                >
                    <template #icon>
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                            />
                        </svg>
                    </template>
                </StatsCard>

                <StatsCard
                    label="Total Pendapatan"
                    :value="formatPrice(stats.totalRevenue)"
                    color="green"
                >
                    <template #icon>
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </template>
                </StatsCard>
            </div>

            <!-- Alert: Kelas pending review -->
            <div
                v-if="stats.pendingCourses > 0"
                class="flex items-center justify-between rounded-xl border border-status-warning-bg bg-status-warning-bg p-4"
            >
                <div class="flex items-center gap-3">
                    <svg
                        class="h-5 w-5 text-status-warning"
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
                    <p class="text-sm font-medium text-status-warning">
                        Ada {{ stats.pendingCourses }} kelas menunggu review
                    </p>
                </div>
                <Link
                    href="/admin/courses"
                    class="text-sm font-medium text-status-warning hover:underline"
                >
                    Review sekarang →
                </Link>
            </div>

            <!-- Recent Orders -->
            <div
                class="overflow-hidden rounded-2xl border border-border bg-card"
            >
                <SectionHeader title="Transaksi Terbaru" href="/admin/orders" />

                <EmptyState
                    v-if="stats.recentOrders.length === 0"
                    message="Belum ada transaksi."
                />

                <table v-else class="w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th
                                class="px-5 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Invoice
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Pengguna
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Item
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Total
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-5 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Tanggal
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="order in stats.recentOrders"
                            :key="order.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td
                                class="px-5 py-3 font-mono text-xs text-muted-foreground"
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
                            <td class="px-5 py-3 font-semibold text-brand">
                                {{ formatPrice(order.final_amount) }}
                            </td>
                            <td class="px-5 py-3">
                                <StatusBadge :status="order.status" />
                            </td>
                            <td class="px-5 py-3 text-xs text-muted-foreground">
                                {{ formatDate(order.created_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
