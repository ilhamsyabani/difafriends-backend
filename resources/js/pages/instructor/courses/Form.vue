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
    course: {
        id: number;
        title: string;
        category_id: number;
        description: string;
        price: number;
        discount_price: number | null;
        duration_minutes: number;
        has_certificate: boolean;
        prerequisites: string | null;
        is_featured: boolean;
        status: string;
    } | null;
    categories: Array<{
        id: number;
        name: string;
        parent: { name: string } | null;
    }>;
}>();

const isEdit = !!props.course;

const form = useForm({
    title: props.course?.title ?? '',
    category_id: props.course?.category_id ?? '',
    description: props.course?.description ?? '',
    price: props.course?.price ?? 0,
    discount_price: props.course?.discount_price ?? '',
    duration_minutes: props.course?.duration_minutes ?? 60,
    has_certificate: props.course?.has_certificate ?? false,
    prerequisites: props.course?.prerequisites ?? '',
    status: props.course?.status ?? 'draft',
});

function submit() {
    if (isEdit) {
        form.put(`/instructor/courses/${props.course!.id}`);
    } else {
        form.post('/instructor/courses');
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Kelas' : 'Buat Kelas Baru'" />

        <div class="max-w-7xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-10 flex items-center gap-4">
                <Link
                    href="/instructor/courses"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-all hover:border-purple-200 hover:bg-purple-50 hover:text-purple-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-purple-400"
                >
                    <ArrowLeft
                        class="h-5 w-5 transition-transform group-hover:-translate-x-1"
                    />
                </Link>
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{ isEdit ? 'Edit Kelas' : 'Buat Kelas Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Lengkapi informasi di bawah untuk merancang materi kelas
                        Anda.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <!-- Section 1: Informasi Dasar -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Informasi Dasar
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Nama, kategori, dan deskripsi kelas yang akan
                            membantu calon siswa memahami apa yang akan mereka
                            pelajari.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="space-y-6">
                            <!-- Judul Kelas -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Judul Kelas
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Contoh: Terapi Wicara untuk Anak Autisme"
                                    :class="[
                                        'w-full rounded-xl',
                                        form.errors.title
                                            ? 'border-red-300 focus-visible:ring-red-500'
                                            : '',
                                    ]"
                                />
                                <p
                                    v-if="form.errors.title"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <Select v-model="form.parent_id">
                                    <!-- Class di sini disamakan persis dengan input nama -->
                                    <SelectTrigger
                                        :class="[
                                            'mt-1 w-full rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900',
                                            form.errors.parent_id
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
                                                v-for="categori in categories"
                                                :key="categori.id"
                                                :value="categori.id.toString()"
                                            >
                                                {{ categori.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <p
                                    v-if="form.errors.category_id"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.category_id }}
                                </p>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Deskripsi
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="5"
                                    placeholder="Jelaskan isi kelas, siapa yang cocok mengikutinya, dan apa manfaatnya..."
                                    class="block w-full resize-y rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                                ></textarea>
                                <p
                                    v-if="form.errors.description"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Prasyarat -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Prasyarat
                                    <span class="font-normal text-gray-400"
                                        >(Opsional)</span
                                    >
                                </label>
                                <textarea
                                    v-model="form.prerequisites"
                                    rows="3"
                                    placeholder="Apa yang perlu dimiliki/diketahui sebelum mengikuti kelas ini?"
                                    class="block w-full resize-y rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pemisah -->
                <div class="h-px w-full bg-gray-100 dark:bg-gray-800"></div>

                <!-- Section 2: Harga & Durasi -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Harga & Durasi
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Atur nilai investasi untuk kelas ini dan estimasi
                            waktu penyelesaiannya.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Harga Normal -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Harga Normal (Rp)
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.price"
                                    type="number"
                                    min="0"
                                    placeholder="0 = Gratis"
                                    :class="[
                                        'w-full rounded-xl',
                                        form.errors.price
                                            ? 'border-red-300 focus-visible:ring-red-500'
                                            : '',
                                    ]"
                                />
                                <p
                                    v-if="form.errors.price"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.price }}
                                </p>
                            </div>

                            <!-- Harga Diskon -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Harga Diskon (Rp)
                                    <span class="font-normal text-gray-400"
                                        >(Opsional)</span
                                    >
                                </label>
                                <Input
                                    v-model="form.discount_price"
                                    type="number"
                                    min="0"
                                    placeholder="Kosongkan jika tidak ada diskon"
                                    class="w-full rounded-xl"
                                />
                            </div>

                            <!-- Durasi -->
                            <div class="sm:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Estimasi Durasi (Menit)
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.duration_minutes"
                                    type="number"
                                    min="1"
                                    class="w-full rounded-xl sm:w-1/2"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pemisah -->
                <div class="h-px w-full bg-gray-100 dark:bg-gray-800"></div>

                <!-- Section 3: Pengaturan Tambahan -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Pengaturan Tambahan
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Konfigurasi sertifikat dan status pengajuan kelas
                            Anda.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="space-y-6">
                            <!-- Toggle Sertifikat -->
                            <div
                                class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Sertifikat Kelulusan
                                    </p>
                                    <p
                                        class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Berikan sertifikat otomatis saat siswa
                                        menyelesaikan kelas.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="
                                        form.has_certificate =
                                            !form.has_certificate
                                    "
                                    :class="
                                        form.has_certificate
                                            ? 'bg-purple-600'
                                            : 'bg-gray-200 dark:bg-gray-700'
                                    "
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                                >
                                    <span class="sr-only"
                                        >Toggle Sertifikat</span
                                    >
                                    <span
                                        aria-hidden="true"
                                        :class="
                                            form.has_certificate
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    />
                                </button>
                            </div>

                            <!-- Status (Hanya Muncul Saat Edit) -->
                            <div
                                v-if="isEdit"
                                class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50"
                            >
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Status Kelas
                                </label>
                                <Select v-model="form.status">
                                    <SelectTrigger
                                        :class="[
                                            'mt-1 flex w-full items-center justify-between rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900',
                                            form.errors.status
                                                ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                                : '',
                                        ]"
                                    >
                                        <SelectValue
                                            placeholder="Pilih status kelas..."
                                        />
                                    </SelectTrigger>

                                    <SelectContent
                                        class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                                    >
                                        <SelectGroup>
                                            <SelectItem value="draft">
                                                Draft (Masih dikonsep)
                                            </SelectItem>
                                            <SelectItem value="review">
                                                Ajukan Review ke Admin
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <p
                                    class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Pilih "Ajukan Review" jika materi sudah
                                    lengkap dan siap dinilai oleh Admin sebelum
                                    diterbitkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <Link
                        href="/instructor/courses"
                        class="rounded-xl px-5 py-2.5 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-70 dark:focus:ring-offset-gray-900"
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
                                  : 'Buat Kelas'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
