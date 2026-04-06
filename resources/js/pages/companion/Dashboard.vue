<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    stats: {
        totalBookings: number;
        upcomingBookings: number;
        totalRevenue: number;
        recentBookings: Array<{
            id: number;
            start_at: string;
            status: string;
            student: { first_name: string; last_name: string };
            schedule: { session_duration: number; price: number };
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

function formatDateTime(dt: string): string {
    return new Date(dt).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function statusColor(status: string): string {
    const map: Record<string, string> = {
        confirmed:
            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        pending:
            'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        completed:
            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        cancelled: 'bg-red-100 text-red-500',
    };
    return map[status] ?? 'bg-gray-100 text-gray-500';
}
</script>

<template>
    <AppLayout>
        <Head title="Companion Dashboard" />

        <div class="max-w-7xl space-y-6 p-6">
            <div>
                <h1 class="text-2xl font-bold">Dashboard Guru Pendamping</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau jadwal dan booking kamu
                </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    v-for="stat in [
                        { label: 'Total Booking', value: stats.totalBookings },
                        { label: 'Akan Datang', value: stats.upcomingBookings },
                        {
                            label: 'Total Pendapatan',
                            value: formatPrice(stats.totalRevenue),
                        },
                    ]"
                    :key="stat.label"
                    class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
                >
                    <p class="mb-1 text-sm text-gray-500">{{ stat.label }}</p>
                    <p class="text-2xl font-bold">{{ stat.value }}</p>
                </div>
            </div>

            <!-- Upcoming Bookings -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800"
            >
                <div
                    class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700"
                >
                    <h2 class="font-semibold">Booking Terbaru</h2>
                    <Link
                        href="/companion/bookings"
                        class="text-sm text-purple-600 hover:underline"
                    >
                        Lihat semua
                    </Link>
                </div>

                <div
                    v-if="stats.recentBookings.length === 0"
                    class="py-12 text-center text-sm text-gray-400"
                >
                    Belum ada booking masuk.
                </div>

                <div v-else>
                    <div
                        v-for="booking in stats.recentBookings"
                        :key="booking.id"
                        class="flex items-center justify-between border-b border-gray-100 px-5 py-4 last:border-0 dark:border-gray-700"
                    >
                        <div>
                            <p class="font-medium">
                                {{ booking.student.first_name }}
                                {{ booking.student.last_name }}
                            </p>
                            <p class="mt-0.5 text-sm text-gray-400">
                                {{ formatDateTime(booking.start_at) }}
                                · {{ booking.schedule.session_duration }} menit
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-purple-600">
                                {{ formatPrice(booking.schedule.price) }}
                            </p>
                            <span
                                :class="[
                                    'rounded-full px-2 py-1 text-xs font-medium',
                                    statusColor(booking.status),
                                ]"
                            >
                                {{ booking.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
