<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Save, Loader2, Image, Upload, X } from 'lucide-vue-next';
import { ref } from 'vue';
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
        instructor_id: number;
        title: string;
        category_id: number;
        description: string;
        thumbnail: string | null;
        price: number;
        discount_price: number | null;
        duration_minutes: number;
        has_certificate: boolean;
        is_featured: boolean;
        prerequisites: string | null;
        status: string;
    } | null;
    categories: Array<{
        id: number;
        name: string;
        parent: { name: string } | null;
    }>;
    instructors: Array<{
        id: number;
        first_name: string;
        last_name: string;
        email: string;
    }>;
}>();

const isEdit = !!props.course;

const form = useForm({
    instructor_id: props.course?.instructor_id ?? '',
    title: props.course?.title ?? '',
    category_id: props.course?.category_id ?? '',
    description: props.course?.description ?? '',
    price: props.course?.price ?? 0,
    discount_price: props.course?.discount_price ?? '',
    duration_minutes: props.course?.duration_minutes ?? 60,
    has_certificate: props.course?.has_certificate ?? false,
    is_featured: props.course?.is_featured ?? false,
    prerequisites: props.course?.prerequisites ?? '',
    status: props.course?.status ?? 'draft',
    thumbnail: null as File | null,
});

// Dropzone
const isDragging = ref(false);
const previewUrl = ref<string | null>(
    props.course?.thumbnail ? `/storage/${props.course.thumbnail}` : null,
);

function onDrop(e: DragEvent) {
    isDragging.value = false;
    const file = e.dataTransfer?.files[0];
    if (file) handleFile(file);
}

function onFileInput(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) handleFile(file);
}

function handleFile(file: File) {
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        alert('Format file harus JPG, PNG, atau WebP.');
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file maksimal 2MB.');
        return;
    }
    form.thumbnail = file;
    previewUrl.value = URL.createObjectURL(file);
}

function removeThumbnail() {
    form.thumbnail = null;
    previewUrl.value = null;
}

function submit() {
    if (isEdit) {
        form.post(`/admin/courses/${props.course!.id}`);
    } else {
        form.post('/admin/courses');
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
                    href="/admin/courses"
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
                        {{ isEdit ? 'Edit Kelas' : 'Buat Kelas Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Admin dapat membuat kelas dan mengassign instruktur yang
                        sesuai.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <!-- ── Assign Instruktur ─────────────────────── -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Instruktur Kelas
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Pilih instruktur yang akan mengajar kelas ini.
                            Instruktur akan bisa mengelola materi dan kurikulum.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <label
                            class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Pilih Instruktur <span class="text-red-500">*</span>
                        </label>
                        <Select v-model="form.instructor_id">
                            <SelectTrigger
                                class="w-full rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white"
                            >
                                <SelectValue
                                    placeholder="— Pilih instruktur —"
                                />
                            </SelectTrigger>
                            <SelectContent
                                class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                            >
                                <SelectGroup>
                                    <SelectItem
                                        v-for="instructor in instructors"
                                        :key="instructor.id"
                                        :value="instructor.id.toString()"
                                    >
                                        {{ instructor.first_name }}
                                        {{ instructor.last_name }}
                                        <span class="ml-1 text-xs text-gray-400"
                                            >({{ instructor.email }})</span
                                        >
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <p
                            v-if="form.errors.instructor_id"
                            class="mt-1.5 text-sm text-red-500"
                        >
                            {{ form.errors.instructor_id }}
                        </p>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-100 dark:bg-gray-800" />

                <!-- ── Informasi Dasar ───────────────────────── -->
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
                            Nama, kategori, dan deskripsi kelas.
                        </p>
                    </div>

                    <div
                        class="space-y-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Judul Kelas <span class="text-red-500">*</span>
                            </label>
                            <Input
                                v-model="form.title"
                                type="text"
                                placeholder="Contoh: Terapi Wicara untuk Anak Autisme"
                                class="w-full rounded-xl"
                            />
                            <p
                                v-if="form.errors.title"
                                class="mt-1.5 text-sm text-red-500"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <Select v-model="form.category_id">
                                <SelectTrigger
                                    class="w-full rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white"
                                >
                                    <SelectValue
                                        placeholder="Pilih kategori..."
                                    />
                                </SelectTrigger>
                                <SelectContent
                                    class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                                >
                                    <SelectGroup>
                                        <SelectItem
                                            v-for="cat in categories"
                                            :key="cat.id"
                                            :value="cat.id.toString()"
                                        >
                                            {{
                                                cat.parent
                                                    ? `${cat.parent.name} → `
                                                    : ''
                                            }}{{ cat.name }}
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

                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="5"
                                placeholder="Jelaskan isi kelas..."
                                class="block w-full resize-y rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                            />
                            <p
                                v-if="form.errors.description"
                                class="mt-1.5 text-sm text-red-500"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

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
                                rows="2"
                                placeholder="Apa yang perlu diketahui sebelum mengikuti kelas?"
                                class="block w-full resize-y rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                            />
                        </div>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-100 dark:bg-gray-800" />

                <!-- ── Thumbnail ─────────────────────────────── -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Thumbnail
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Gambar cover kelas. JPG, PNG, WebP. Maks. 2MB.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div v-if="previewUrl" class="relative">
                            <img
                                :src="previewUrl"
                                class="h-48 w-full rounded-xl object-cover"
                            />
                            <div
                                class="absolute inset-0 flex items-center justify-center gap-3 rounded-xl bg-black/50 opacity-0 transition-opacity hover:opacity-100"
                            >
                                <label
                                    class="flex cursor-pointer items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    <Upload class="h-4 w-4" />
                                    Ganti
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        @change="onFileInput"
                                    />
                                </label>
                                <button
                                    type="button"
                                    @click="removeThumbnail"
                                    class="flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600"
                                >
                                    <X class="h-4 w-4" /> Hapus
                                </button>
                            </div>
                        </div>

                        <label
                            v-else
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="onDrop"
                            :class="[
                                'flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-12 text-center transition-all',
                                isDragging
                                    ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                                    : 'border-gray-200 hover:border-purple-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:border-purple-700',
                            ]"
                        >
                            <div
                                :class="[
                                    'mb-4 flex h-14 w-14 items-center justify-center rounded-full',
                                    isDragging
                                        ? 'bg-purple-100'
                                        : 'bg-gray-100 dark:bg-gray-800',
                                ]"
                            >
                                <Image
                                    :class="[
                                        'h-7 w-7',
                                        isDragging
                                            ? 'text-purple-600'
                                            : 'text-gray-400',
                                    ]"
                                />
                            </div>
                            <p
                                class="text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                <span class="text-purple-600"
                                    >Klik untuk upload</span
                                >
                                atau drag & drop
                            </p>
                            <p class="mt-1 text-xs text-gray-400">
                                JPG, PNG, WebP · Rasio 16:9 · Maks. 2MB
                            </p>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="onFileInput"
                            />
                        </label>
                    </div>
                </div>

                <div class="h-px w-full bg-gray-100 dark:bg-gray-800" />

                <!-- ── Harga & Durasi ─────────────────────────── -->
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
                            Harga dan estimasi waktu kelas.
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
                                    Harga Normal (Rp)
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
                                    Harga Diskon (Rp)
                                    <span class="font-normal text-gray-400"
                                        >(Opsional)</span
                                    >
                                </label>
                                <Input
                                    v-model="form.discount_price"
                                    type="number"
                                    min="0"
                                    placeholder="Kosongkan jika tidak ada"
                                    class="w-full rounded-xl"
                                />
                            </div>
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

                <div class="h-px w-full bg-gray-100 dark:bg-gray-800" />

                <!-- ── Pengaturan Admin ───────────────────────── -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Pengaturan
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Admin dapat langsung mengatur status dan fitur
                            khusus kelas.
                        </p>
                    </div>

                    <div
                        class="space-y-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <!-- Status — admin bisa set langsung published -->
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Status Kelas <span class="text-red-500">*</span>
                            </label>
                            <Select v-model="form.status">
                                <SelectTrigger
                                    class="w-full rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white"
                                >
                                    <SelectValue
                                        placeholder="Pilih status..."
                                    />
                                </SelectTrigger>
                                <SelectContent
                                    class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                                >
                                    <SelectGroup>
                                        <SelectItem value="draft"
                                            >Draft</SelectItem
                                        >
                                        <SelectItem value="review"
                                            >Dalam Review</SelectItem
                                        >
                                        <SelectItem value="published"
                                            >Published (Aktif)</SelectItem
                                        >
                                        <SelectItem value="archived"
                                            >Archived</SelectItem
                                        >
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>

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
                                <p class="mt-0.5 text-xs text-gray-500">
                                    Berikan sertifikat saat siswa menyelesaikan
                                    kelas.
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="
                                    form.has_certificate = !form.has_certificate
                                "
                                :class="
                                    form.has_certificate
                                        ? 'bg-purple-600'
                                        : 'bg-gray-200 dark:bg-gray-700'
                                "
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                            >
                                <span
                                    :class="
                                        form.has_certificate
                                            ? 'translate-x-5'
                                            : 'translate-x-0'
                                    "
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow transition"
                                />
                            </button>
                        </div>

                        <!-- Toggle Featured -->
                        <div
                            class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800"
                        >
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    Tampilkan di Featured
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    Kelas akan muncul di bagian unggulan landing
                                    page.
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="form.is_featured = !form.is_featured"
                                :class="
                                    form.is_featured
                                        ? 'bg-amber-500'
                                        : 'bg-gray-200 dark:bg-gray-700'
                                "
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors"
                            >
                                <span
                                    :class="
                                        form.is_featured
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
                        href="/admin/courses"
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
                                  : 'Buat Kelas'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
