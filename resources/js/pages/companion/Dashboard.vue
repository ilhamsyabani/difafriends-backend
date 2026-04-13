<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import SectionHeader from '@/components/SectionHeader.vue';
import StatsCard from '@/components/StatsCard.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { useFormatters } from '@/composables/useFormatters';
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

const { formatPrice, formatDateTime } = useFormatters();
</script>

<template>
    <AppLayout>
        <Head title="Dashboard Guru Pendamping" />

        <div class="max-w-7xl space-y-6 p-6">
            <div>
                <h1 class="text-2xl font-bold">Dashboard Guru Pendamping</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Pantau jadwal dan booking kamu
                </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <StatsCard
                    label="Total Booking"
                    :value="stats.totalBookings"
                    color="brand"
                />
                <StatsCard
                    label="Akan Datang"
                    :value="stats.upcomingBookings"
                    color="blue"
                />
                <StatsCard
                    label="Total Pendapatan"
                    :value="formatPrice(stats.totalRevenue)"
                    color="green"
                />
            </div>

            <!-- Upcoming Bookings -->
            <div
                class="overflow-hidden rounded-2xl border border-border bg-card"
            >
                <SectionHeader
                    title="Booking Terbaru"
                    href="/companion/bookings"
                />

                <EmptyState
                    v-if="stats.recentBookings.length === 0"
                    message="Belum ada booking masuk."
                />

                <div v-else>
                    <div
                        v-for="booking in stats.recentBookings"
                        :key="booking.id"
                        class="flex items-center justify-between border-b border-border px-5 py-4 last:border-0"
                    >
                        <div>
                            <p class="font-medium">
                                {{ booking.student.first_name }}
                                {{ booking.student.last_name }}
                            </p>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                {{ formatDateTime(booking.start_at) }}
                                · {{ booking.schedule.session_duration }} menit
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-brand">
                                {{ formatPrice(booking.schedule.price) }}
                            </p>
                            <StatusBadge :status="booking.status" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
