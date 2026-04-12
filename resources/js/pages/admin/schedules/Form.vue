<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Save, Loader2 } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
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
        tutor_id: number;
        day_of_week: number;
        start_time: string;
        end_time: string;
        session_duration: number;
        break_time: number;
        price: number;
        max_participants: number;
        is_active: boolean;
    } | null;
    companions: Array<{
        id: number;
        first_name: string;
        last_name: string;
        email: string;
    }>;
}>();

const isEdit = !!props.schedule;

const DAY_NAMES = [
    'Minggu',
    'Senin',
    'Selasa',
    'Rabu',
    'Kamis',
    'Jumat',
    'Sabtu',
];

const form = useForm({
    tutor_id: props.schedule?.tutor_id?.toString() ?? '',
    day_of_week: props.schedule?.day_of_week?.toString() ?? '',
    start_time: props.schedule?.start_time ?? '',
    end_time: props.schedule?.end_time ?? '',
    session_duration: props.schedule?.session_duration ?? 60,
    break_time: props.schedule?.break_time ?? 10,
    price: props.schedule?.price ?? 0,
    max_participants: props.schedule?.max_participants ?? 1,
    is_active: props.schedule?.is_active ?? true,
});

function submit() {
    if (isEdit) {
        form.put(`/admin/schedules/${props.schedule!.id}`);
    } else {
        form.post('/admin/schedules');
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Jadwal' : 'Buat Jadwal Baru'" />

        <div class="max-w-7xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-10 flex items-center gap-4">
                <Link
                    href="/admin/schedules"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-all hover:border-purple-200 hover:bg-purple-50 hover:text-purple-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                >
                    <ArrowLeft
                        class="h-5 w-5 transition-transform group-hover:-translate-x-1"
                    />
                </Link>
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{ isEdit ? 'Edit Jadwal' : 'Buat Jadwal Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Atur jadwal sesi pendampingan untuk companion.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <!-- ── Companion ─────────────────────────────── -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Companion
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Pilih companion yang akan menjalankan jadwal ini.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Companion <span class="text-red-500">*</span>
                        </label>
                        <Select v-model="form.tutor_id">
                            <SelectTrigger
                                class="w-full rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white"
                            >
                                <SelectValue
                                    placeholder="— Pilih companion —"
                                />
                            </SelectTrigger>
                            <SelectContent
                                class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                            >
                                <SelectGroup>
                                    <SelectItem
                                        v-for="companion in companions"
                                        :key="companion.id"
                                        :value="companion.id.toString()"
                                    >
                                        {{ companion.first_name }}
                                        {{ companion.last_name }}
                                        <span class="ml-1 text-xs text-gray-400"
                                            >({{ companion.email }})</span
                                        >
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <p
                            v-if="form.errors.tutor_id"
                            class="mt-1.5 text-sm text-red-500"
                        >
                            {{ form.errors.tutor_id }}
                        </p>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-100 dark:bg-gray-800" />

                <!-- ── Waktu & Hari ───────────────────────────── -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Jadwal Sesi
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Hari dan jam berlangsungnya sesi pendampingan.
                        </p>
                    </div>

                    <div
                        class="space-y-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <!-- Hari -->
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Hari <span class="text-red-500">*</span>
                            </label>
                            <Select v-model="form.day_of_week">
                                <SelectTrigger
                                    class="w-full rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white"
                                >
                                    <SelectValue placeholder="— Pilih hari —" />
                                </SelectTrigger>
                                <SelectContent
                                    class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                                >
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="(name, index) in DAY_NAMES"
                                            :key="index"
                                            :value="index.toString()"
                                        >
                                            {{ name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <p
                                v-if="form.errors.day_of_week"
                                class="mt-1.5 text-sm text-red-500"
                            >
                                {{ form.errors.day_of_week }}
                            </p>
                        </div>

                        <!-- Jam Mulai & Selesai -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Jam Mulai
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.start_time"
                                    type="time"
                                    class="w-full rounded-xl"
                                />
                                <p
                                    v-if="form.errors.start_time"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.start_time }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Jam Selesai
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.end_time"
                                    type="time"
                                    class="w-full rounded-xl"
                                />
                                <p
                                    v-if="form.errors.end_time"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.end_time }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-100 dark:bg-gray-800" />

                <!-- ── Durasi & Istirahat ─────────────────────── -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Durasi Sesi
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Durasi per sesi dan jeda antar sesi (dalam menit).
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Durasi Sesi (Menit)
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.session_duration"
                                    type="number"
                                    min="15"
                                    placeholder="Min. 15 menit"
                                    class="w-full rounded-xl"
                                />
                                <p
                                    v-if="form.errors.session_duration"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.session_duration }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Jeda / Istirahat (Menit)
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.break_time"
                                    type="number"
                                    min="0"
                                    placeholder="0 = tidak ada jeda"
                                    class="w-full rounded-xl"
                                />
                                <p
                                    v-if="form.errors.break_time"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.break_time }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-100 dark:bg-gray-800" />

                <!-- ── Harga & Kapasitas ──────────────────────── -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Harga & Kapasitas
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Harga per sesi dan jumlah maksimal peserta.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Harga per Sesi (Rp)
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.price"
                                    type="number"
                                    min="0"
                                    placeholder="0 = Gratis"
                                    class="w-full rounded-xl"
                                />
                                <p
                                    v-if="form.errors.price"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.price }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Maks. Peserta
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.max_participants"
                                    type="number"
                                    min="1"
                                    max="10"
                                    class="w-full rounded-xl"
                                />
                                <p
                                    v-if="form.errors.max_participants"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.max_participants }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-100 dark:bg-gray-800" />

                <!-- ── Status Aktif ───────────────────────────── -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Status Jadwal
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Jadwal aktif dapat ditemukan dan dipesan oleh
                            pengguna.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div
                            class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800"
                        >
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    Jadwal Aktif
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    Matikan untuk menyembunyikan jadwal
                                    sementara tanpa menghapusnya.
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="form.is_active = !form.is_active"
                                :class="
                                    form.is_active
                                        ? 'bg-purple-600'
                                        : 'bg-gray-200 dark:bg-gray-700'
                                "
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                            >
                                <span
                                    :class="
                                        form.is_active
                                            ? 'translate-x-5'
                                            : 'translate-x-0'
                                    "
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition"
                                />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <Link
                        href="/admin/schedules"
                        class="rounded-xl px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-purple-700 disabled:opacity-70"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin"
                        />
                        <Save v-else class="h-4 w-4" />
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : isEdit
                                  ? 'Simpan Perubahan'
                                  : 'Buat Jadwal'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
