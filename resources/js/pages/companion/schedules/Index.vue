<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Plus,
    Clock,
    Users,
    Wallet,
    CalendarClock,
    Edit2,
    Trash2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useConfirm } from '@/composables/useConfirm';

const confirm = useConfirm();

interface Schedule {
    id: number;
    tutor_id: number;
    day_of_week: string | number;
    start_time: string;
    end_time: string;
    session_duration: number;
    break_time: number;
    price: string | number;
    max_participants: number;
    is_active: boolean;
    bookings_count: number;
}

const props = defineProps<{
    schedules: Schedule[];
}>();

const dayNames: Record<string, string> = {
    '1': 'Senin',
    '2': 'Selasa',
    '3': 'Rabu',
    '4': 'Kamis',
    '5': 'Jumat',
    '6': 'Sabtu',
    '7': 'Minggu',
    monday: 'Senin',
    tuesday: 'Selasa',
    wednesday: 'Rabu',
    thursday: 'Kamis',
    friday: 'Jumat',
    saturday: 'Sabtu',
    sunday: 'Minggu',
};

function getDayName(val: string | number): string {
    return dayNames[String(val).toLowerCase()] || String(val);
}

// Memformat waktu dari "08:00:00" menjadi "08:00"
function formatTime(timeStr: string): string {
    if (!timeStr) {
        return '';
    }

    return timeStr.substring(0, 5);
}

const groupedSchedules = computed(() => {
    const groups: Record<string, Schedule[]> = {};

    props.schedules.forEach((schedule) => {
        const day = String(schedule.day_of_week);

        if (!groups[day]) {
            groups[day] = [];
        }

        groups[day].push(schedule);
    });

    return groups;
});

// ── Actions ────────────────────────────────────────────────

function toggleStatus(schedule: Schedule) {
    // Memperbarui status is_active secara langsung (toggle)
    router.put(
        `/companion/schedules/${schedule.id}/toggle-status`,
        {
            is_active: !schedule.is_active,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
}

async function destroy(id: number) {
    const ok = await confirm('Hapus Jadwal', 'Yakin ingin menghapus slot jadwal ini? Pastikan tidak ada booking yang aktif.');
    if (!ok) return;
    router.delete(`/companion/schedules/${id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Kelola Jadwal" />

        <div class="mx-auto max-w-7xl p-6 sm:p-10">
            <!-- Header -->
            <div
                class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between"
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
                            Jadwal Ketersediaan
                        </h1>
                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Atur hari dan jam kerja Anda untuk menerima sesi
                            bersama siswa.
                        </p>
                    </div>
                </div>

                <Link
                    href="/companion/schedules/create"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                >
                    <Plus class="h-4 w-4" />
                    Tambah Jadwal
                </Link>
            </div>

            <!-- Empty State -->
            <div
                v-if="schedules.length === 0"
                class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50 py-20 dark:border-gray-800 dark:bg-gray-900/50"
            >
                <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-200 text-gray-400 dark:bg-gray-800 dark:text-gray-500"
                >
                    <Clock class="h-6 w-6" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Belum Ada Jadwal
                </h3>
                <p
                    class="mt-1 max-w-sm text-center text-sm text-gray-500 dark:text-gray-400"
                >
                    Anda belum mengatur slot waktu kerja. Tambahkan jadwal agar
                    siswa bisa mulai memesan sesi.
                </p>
            </div>

            <!-- Daftar Jadwal Berdasarkan Hari -->
            <div v-else class="space-y-10">
                <div
                    v-for="(daySchedules, dayCode) in groupedSchedules"
                    :key="dayCode"
                >
                    <!-- Judul Hari -->
                    <div class="mb-4 flex items-center gap-3">
                        <h2
                            class="text-lg font-bold tracking-wider text-gray-900 uppercase dark:text-white"
                        >
                            {{ getDayName(dayCode) }}
                        </h2>
                        <div
                            class="h-px flex-1 bg-gray-200 dark:bg-gray-800"
                        ></div>
                        <span class="text-xs font-medium text-gray-400"
                            >{{ daySchedules.length }} Slot</span
                        >
                    </div>

                    <!-- Grid Kartu Jadwal -->
                    <div
                        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="schedule in daySchedules"
                            :key="schedule.id"
                            :class="[
                                'relative overflow-hidden rounded-2xl border p-5 transition-all hover:shadow-md dark:bg-gray-900',
                                schedule.is_active
                                    ? 'border-gray-200 bg-white dark:border-gray-800'
                                    : 'border-gray-100 bg-gray-50 opacity-75 dark:border-gray-800/50 dark:bg-gray-900/50',
                            ]"
                        >
                            <!-- Jam & Toggle -->
                            <div class="mb-5 flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <Clock
                                            class="h-4 w-4 text-purple-500"
                                        />
                                        <span
                                            class="font-mono text-lg font-bold text-gray-900 dark:text-white"
                                        >
                                            {{
                                                formatTime(schedule.start_time)
                                            }}
                                            -
                                            {{ formatTime(schedule.end_time) }}
                                        </span>
                                    </div>
                                    <p
                                        class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >
                                        Durasi: {{ schedule.session_duration }}m
                                        <span
                                            v-if="schedule.break_time > 0"
                                            class="text-orange-400 dark:text-[#0097B2]"
                                        >
                                            • Jeda {{ schedule.break_time }}m
                                        </span>
                                    </p>
                                </div>

                                <!-- Modern Toggle Switch -->
                                <button
                                    type="button"
                                    @click="toggleStatus(schedule)"
                                    :class="
                                        schedule.is_active
                                            ? 'bg-green-500'
                                            : 'bg-gray-200 dark:bg-gray-700'
                                    "
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                                    title="Aktifkan/Nonaktifkan Slot"
                                >
                                    <span class="sr-only">Use setting</span>
                                    <span
                                        :class="
                                            schedule.is_active
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    />
                                </button>
                            </div>

                            <!-- Detail Kuota & Harga -->
                            <div
                                class="mb-5 space-y-3 rounded-xl bg-gray-50 p-3 dark:bg-gray-800/50"
                            >
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <div
                                        class="flex items-center gap-2 text-gray-600 dark:text-gray-400"
                                    >
                                        <Users class="h-4 w-4" />
                                        <span>Peserta</span>
                                    </div>
                                    <span
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        <span
                                            :class="
                                                schedule.bookings_count >=
                                                schedule.max_participants
                                                    ? 'text-red-500'
                                                    : 'text-primary'
                                            "
                                        >
                                            {{ schedule.bookings_count }}
                                        </span>
                                        / {{ schedule.max_participants }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <div
                                        class="flex items-center gap-2 text-gray-600 dark:text-gray-400"
                                    >
                                        <Wallet class="h-4 w-4" />
                                        <span>Harga Sesi</span>
                                    </div>
                                    <span
                                        class="font-medium text-green-600 dark:text-green-400"
                                    >
                                        Rp
                                        {{
                                            Number(
                                                schedule.price,
                                            ).toLocaleString('id-ID')
                                        }}
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div
                                class="flex items-center justify-end gap-2 border-t border-gray-100 pt-4 dark:border-gray-800"
                            >
                                <Link
                                    :href="`/companion/schedules/${schedule.id}/edit`"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
                                >
                                    <Edit2 class="h-3.5 w-3.5" />
                                    Edit
                                </Link>
                                <button
                                    @click="destroy(schedule.id)"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-500 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30"
                                >
                                    <Trash2 class="h-3.5 w-3.5" />
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
