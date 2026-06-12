<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    schedule: {
        id: number;
        day_of_week: number;
        start_time: string;
        end_time: string;
        session_duration: number;
        break_time: number;
        price: number;
        max_participants: number;
        is_active: boolean;
    } | null;
}>();

const isEdit = !!props.schedule;

const days = [
    { value: 0, label: 'Minggu' },
    { value: 1, label: 'Senin' },
    { value: 2, label: 'Selasa' },
    { value: 3, label: 'Rabu' },
    { value: 4, label: 'Kamis' },
    { value: 5, label: 'Jumat' },
    { value: 6, label: 'Sabtu' },
];

const form = useForm({
    day_of_week: props.schedule?.day_of_week ?? 1,
    start_time: props.schedule?.start_time?.slice(0, 5) ?? '08:00',
    end_time: props.schedule?.end_time?.slice(0, 5) ?? '17:00',
    session_duration: props.schedule?.session_duration ?? 60,
    break_time: props.schedule?.break_time ?? 15,
    price: props.schedule?.price ?? 0,
    max_participants: props.schedule?.max_participants ?? 1,
    is_active: props.schedule?.is_active ?? true,
});

function submit() {
    if (isEdit) {
        form.put(`/companion/schedules/${props.schedule!.id}`);
    } else {
        form.post('/companion/schedules');
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Jadwal' : 'Tambah Jadwal'" />

        <div class="mx-auto max-w-7xl p-6">
            <div class="mb-6 flex items-center gap-3">
                <Link
                    href="/companion/schedules"
                    class="text-gray-400 hover:text-gray-600"
                >
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
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </Link>
                <h1 class="text-2xl font-bold">
                    {{ isEdit ? 'Edit Jadwal' : 'Tambah Jadwal' }}
                </h1>
            </div>

            <div
                class="space-y-5 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- Hari -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium"
                        >Hari <span class="text-red-500">*</span></label
                    >
                    <Select v-model="form.day_of_week">
                        <!-- Class di sini disamakan persis dengan input nama -->
                        <SelectTrigger
                            :class="[
                                'mt-1 w-full rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-900 dark:text-white dark:focus:bg-gray-900',
                                form.errors.day_of_week
                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                    : '',
                            ]"
                        >
                            <SelectValue
                                placeholder="Pilih kategori induk..."
                            />
                        </SelectTrigger>

                        <SelectContent
                            class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                        >
                            <SelectGroup>
                                <SelectItem
                                    v-for="day in days"
                                    :key="day.value"
                                    :value="day.value"
                                >
                                    {{ day.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <p
                        v-if="form.errors.day_of_week"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ form.errors.day_of_week }}
                    </p>
                </div>

                <!-- Waktu -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium"
                            >Jam Mulai
                            <span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="form.start_time"
                            type="time"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                        />
                        <p
                            v-if="form.errors.start_time"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.start_time }}
                        </p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium"
                            >Jam Selesai
                            <span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="form.end_time"
                            type="time"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                        />
                        <p
                            v-if="form.errors.end_time"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.end_time }}
                        </p>
                    </div>
                </div>

                <!-- Durasi & Break -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium"
                            >Durasi Sesi (menit)</label
                        >
                        <input
                            v-model="form.session_duration"
                            type="number"
                            min="15"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                        />
                        <p class="mt-1 text-xs text-gray-400">
                            Lama satu sesi pendampingan
                        </p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium"
                            >Jeda Antar Sesi (menit)</label
                        >
                        <input
                            v-model="form.break_time"
                            type="number"
                            min="0"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                        />
                    </div>
                </div>

                <!-- Harga & Kapasitas -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium"
                            >Harga per Sesi (Rp)
                            <span class="text-red-500">*</span></label
                        >
                        <input
                            v-model="form.price"
                            type="number"
                            min="0"
                            placeholder="150000"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                        />
                        <p
                            v-if="form.errors.price"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ form.errors.price }}
                        </p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium"
                            >Maks. Peserta</label
                        >
                        <input
                            v-model="form.max_participants"
                            type="number"
                            min="1"
                            max="10"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                        />
                        <p class="mt-1 text-xs text-gray-400">
                            Biasanya 1 untuk sesi privat
                        </p>
                    </div>
                </div>

                <!-- Is Active -->
                <div class="flex items-center gap-3">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        id="is_active"
                        class="h-4 w-4 rounded text-primary focus:ring-purple-500"
                    />
                    <label
                        for="is_active"
                        class="cursor-pointer text-sm font-medium"
                    >
                        Jadwal ini aktif dan bisa dipesan
                    </label>
                </div>

                <!-- Submit -->
                <div
                    class="flex items-center gap-3 border-t border-gray-100 pt-2 dark:border-gray-700"
                >
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-purple-700 disabled:opacity-60"
                    >
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : isEdit
                                  ? 'Simpan Perubahan'
                                  : 'Tambah Jadwal'
                        }}
                    </button>
                    <Link
                        href="/companion/schedules"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        Batal
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
