<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { Plus, Edit2, Trash2, Search, Filter } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps<{
    schedules: {
        data: Array<{
            id: number;
            day_of_week: number;
            start_time: string;
            end_time: string;
            session_duration: number;
            price: number;
            is_active: boolean;
            bookings_count: number;
            tutor: {
                id: number;
                first_name: string;
                last_name: string;
                photo: string | null;
            };
        }>;
        links: any[];
        meta: any;
    };
}>();

const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string },
);

const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

function formatPrice(price: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
}

function formatTime(time: string): string {
    return time.slice(0, 5);
}

function destroy(id: number) {
    if (confirm('Yakin hapus jadwal ini?')) {
        router.delete(`/admin/schedules/${id}`);
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Kelola Jadwal Sesi" />

        <div class="max-w-7xl p-6 sm:p-10">
            <!-- Header -->
            <div
                class="mb-8 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Kelola Jadwal Sesi
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total
                        {{ schedules.meta?.total ?? schedules.data.length }}
                        jadwal tersedia.
                    </p>
                </div>
                <Link
                    href="/admin/schedules/create"
                    class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-purple-700"
                >
                    <Plus class="h-4 w-4" />
                    Jadwal Baru
                </Link>
            </div>

            <!-- Flash -->
            <div
                v-if="flash.success"
                class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-400"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash.error"
                class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400"
            >
                {{ flash.error }}
            </div>

            <!-- Table -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <table class="w-full text-sm">
                    <thead
                        class="border-b border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/50"
                    >
                        <tr>
                            <th
                                class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Guru Pendamping
                            </th>
                            <th
                                class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Jadwal
                            </th>
                            <th
                                class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Durasi & Harga
                            </th>
                            <th
                                class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Booking
                            </th>
                            <th
                                class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                            >
                                Status
                            </th>
                            <th class="px-5 py-4" />
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-100 dark:divide-gray-800"
                    >
                        <tr
                            v-for="schedule in schedules.data"
                            :key="schedule.id"
                            class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/50"
                        >
                            <!-- Tutor -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100 text-sm font-bold text-purple-600 dark:bg-purple-900/30"
                                    >
                                        {{
                                            schedule.tutor.first_name.charAt(0)
                                        }}
                                    </div>
                                    <div>
                                        <p
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ schedule.tutor.first_name }}
                                            {{ schedule.tutor.last_name }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            Guru Pendamping
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Jadwal -->
                            <td class="px-5 py-4">
                                <p
                                    class="font-medium text-gray-900 dark:text-white"
                                >
                                    {{ days[schedule.day_of_week] }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ formatTime(schedule.start_time) }} –
                                    {{ formatTime(schedule.end_time) }}
                                </p>
                            </td>

                            <!-- Durasi & Harga -->
                            <td class="px-5 py-4">
                                <p
                                    class="font-medium text-gray-900 dark:text-white"
                                >
                                    {{ schedule.session_duration }} menit/sesi
                                </p>
                                <p
                                    class="text-sm font-semibold text-purple-600"
                                >
                                    {{ formatPrice(schedule.price) }}
                                </p>
                            </td>

                            <!-- Booking count -->
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-400"
                                >
                                    {{ schedule.bookings_count }} booking
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                        schedule.is_active
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                            : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                                    ]"
                                >
                                    {{
                                        schedule.is_active
                                            ? 'Aktif'
                                            : 'Nonaktif'
                                    }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4">
                                <div
                                    class="flex items-center justify-end gap-3"
                                >
                                    <Link
                                        :href="`/admin/schedules/${schedule.id}/edit`"
                                        class="text-gray-400 transition-colors hover:text-purple-600 dark:hover:text-purple-400"
                                    >
                                        <Edit2 class="h-4 w-4" />
                                    </Link>
                                    <button
                                        @click="destroy(schedule.id)"
                                        class="text-gray-400 transition-colors hover:text-red-500 dark:hover:text-red-400"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="schedules.data.length === 0">
                            <td
                                colspan="6"
                                class="px-5 py-16 text-center text-sm text-gray-400 dark:text-gray-500"
                            >
                                Belum ada jadwal.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="schedules.links?.length > 3"
                class="mt-8 flex justify-end gap-1.5"
            >
                <Link
                    v-for="link in schedules.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    :class="[
                        'rounded-xl px-3.5 py-2 text-sm font-medium transition-colors',
                        link.active
                            ? 'bg-purple-600 text-white shadow-sm'
                            : link.url
                              ? 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300'
                              : 'cursor-not-allowed border border-transparent text-gray-400',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
