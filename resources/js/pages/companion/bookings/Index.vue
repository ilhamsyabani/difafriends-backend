<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CalendarClock, Video } from 'lucide-vue-next';
import EmptyState from '@/components/EmptyState.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { useFormatters } from '@/composables/useFormatters';
import AppLayout from '@/layouts/AppLayout.vue';

interface Student {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
}

interface Schedule {
    session_duration: number;
    price: number;
}

interface Booking {
    id: number;
    start_at: string;
    end_at: string;
    status: string;
    meeting_link: string | null;
    notes: string | null;
    student: Student;
    schedule: Schedule;
}

interface PaginatedBookings {
    data: Booking[];
    links: { url: string | null; label: string; active: boolean }[];
    meta: { current_page: number; last_page: number; total: number };
}

defineProps<{
    bookings: PaginatedBookings;
    filters: { status: string | null };
}>();

const { formatPrice, formatDateTime } = useFormatters();

const statusOptions = [
    { value: '', label: 'Semua' },
    { value: 'pending', label: 'Menunggu' },
    { value: 'confirmed', label: 'Terkonfirmasi' },
    { value: 'ongoing', label: 'Berlangsung' },
    { value: 'completed', label: 'Selesai' },
    { value: 'cancelled', label: 'Dibatalkan' },
];

function filterByStatus(status: string) {
    router.get(
        '/companion/bookings',
        { status: status || undefined },
        { preserveState: true, replace: true },
    );
}
</script>

<template>
    <AppLayout>
        <Head title="Daftar Booking" />

        <div class="max-w-7xl p-6 sm:p-10">
            <!-- Header -->
            <div
                class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 text-primary dark:bg-purple-900/50 dark:text-purple-400"
                    >
                        <CalendarClock class="h-6 w-6" />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            Daftar Booking
                        </h1>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Kelola sesi pendampingan dari siswa
                        </p>
                    </div>
                </div>

                <!-- Filter Status -->
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="opt in statusOptions"
                        :key="opt.value"
                        @click="filterByStatus(opt.value)"
                        :class="[
                            'rounded-full px-4 py-1.5 text-xs font-medium transition-colors',
                            (filters.status ?? '') === opt.value
                                ? 'bg-primary text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700',
                        ]"
                    >
                        {{ opt.label }}
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <EmptyState
                v-if="bookings.data.length === 0"
                message="Belum ada booking yang masuk."
            />

            <!-- Tabel Booking -->
            <div
                v-else
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-gray-100 bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase dark:border-gray-800 dark:bg-gray-800/60 dark:text-gray-400"
                            >
                                <th class="px-5 py-3">Siswa</th>
                                <th class="px-5 py-3">Jadwal</th>
                                <th class="px-5 py-3">Durasi</th>
                                <th class="px-5 py-3">Harga</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Meeting</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 dark:divide-gray-800"
                        >
                            <tr
                                v-for="booking in bookings.data"
                                :key="booking.id"
                                class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                            >
                                <td class="px-5 py-4">
                                    <p
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ booking.student.first_name }}
                                        {{ booking.student.last_name }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-gray-400">
                                        {{ booking.student.email }}
                                    </p>
                                </td>
                                <td
                                    class="px-5 py-4 text-gray-700 dark:text-gray-300"
                                >
                                    {{ formatDateTime(booking.start_at) }}
                                </td>
                                <td
                                    class="px-5 py-4 text-gray-600 dark:text-gray-400"
                                >
                                    {{ booking.schedule.session_duration }}
                                    menit
                                </td>
                                <td class="px-5 py-4 font-semibold text-brand">
                                    {{ formatPrice(booking.schedule.price) }}
                                </td>
                                <td class="px-5 py-4">
                                    <StatusBadge :status="booking.status" />
                                </td>
                                <td class="px-5 py-4">
                                    <a
                                        v-if="booking.meeting_link"
                                        :href="booking.meeting_link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-600 transition-colors hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50"
                                    >
                                        <Video class="h-3.5 w-3.5" />
                                        Buka
                                    </a>
                                    <span v-else class="text-xs text-gray-400">
                                        Belum ada
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="bookings.meta.last_page > 1"
                    class="flex items-center justify-between border-t border-gray-100 px-5 py-4 dark:border-gray-800"
                >
                    <p class="text-sm text-gray-500">
                        Total {{ bookings.meta.total }} booking
                    </p>
                    <div class="flex gap-1">
                        <template
                            v-for="link in bookings.links"
                            :key="link.label"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-lg px-3 py-1.5 text-sm transition-colors"
                                :class="
                                    link.active
                                        ? 'bg-primary text-white'
                                        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800'
                                "
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
