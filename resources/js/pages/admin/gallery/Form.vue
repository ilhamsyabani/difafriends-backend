<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { X, Upload, Image as ImageIcon, ArrowLeft } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';
import AppLayout from '@/layouts/AppLayout.vue';

const toast = useToast();

const props = defineProps<{
    gallery: {
        id: number;
        filename: string;
        alt: string;
        path: string;
        order: number;
    } | null;
}>();

const isEdit = !!props.gallery;

const form = useForm({
    image: null as File | null,
    alt: props.gallery?.alt ?? '',
    order: props.gallery?.order ?? 0,
});

const isDragging = ref(false);
const previewUrl = ref<string | null>(
    props.gallery ? `/storage/${props.gallery.path}` : null,
);

function onDrop(e: DragEvent) {
    isDragging.value = false;
    const file = e.dataTransfer?.files[0];
    if (file && file.type.startsWith('image/')) {
        handleFile(file);
    }
}

function onFileInput(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        handleFile(file);
    }
}

function handleFile(file: File) {
    form.image = file;
    previewUrl.value = URL.createObjectURL(file);
}

function removeFile() {
    form.image = null;
    if (!isEdit) {
        previewUrl.value = null;
    } else {
        previewUrl.value = `/storage/${props.gallery!.path}`;
    }
}

function submit() {
    if (isEdit) {
        // Use post with _method PUT for multipart form data
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(`/admin/gallery/${props.gallery!.id}`, {
            onSuccess: () => toast.success('Gambar berhasil diperbarui.'),
        });
    } else {
        form.post('/admin/gallery', {
            onSuccess: () => toast.success('Gambar berhasil diupload.'),
        });
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Gambar' : 'Tambah Gambar'" />

        <div class="max-w-3xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-10 flex items-center gap-4">
                <Link
                    href="/admin/gallery"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-all hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ isEdit ? 'Edit Gambar' : 'Tambah Gambar Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ isEdit ? 'Perbarui informasi deskripsi atau urutan gambar.' : 'Upload foto baru ke galeri workshop.' }}
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Dropzone / Preview -->
                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <label class="mb-4 block text-sm font-bold text-gray-900 dark:text-white">
                        File Gambar <span v-if="!isEdit" class="text-red-500">*</span>
                    </label>

                    <div
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="onDrop"
                        :class="[
                            'flex flex-col items-center justify-center rounded-xl border-2 border-dashed p-8 transition-all',
                            isDragging
                                ? 'border-primary bg-primary/5'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600',
                        ]"
                    >
                        <div v-if="previewUrl" class="flex flex-col items-center gap-4">
                            <div class="relative group">
                                <img
                                    :src="previewUrl"
                                    class="h-64 w-full max-w-md rounded-xl object-cover shadow-xl"
                                />
                                <label
                                    v-if="isEdit || form.image"
                                    class="absolute inset-0 flex items-center justify-center rounded-xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                                >
                                    <div class="flex flex-col items-center text-white">
                                        <Upload class="h-8 w-8 mb-2" />
                                        <span class="text-sm font-bold">Ganti Gambar</span>
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        @change="onFileInput"
                                    />
                                </label>
                            </div>
                            <p v-if="form.image" class="text-xs text-gray-500 italic">
                                File terpilih: {{ form.image.name }}
                            </p>
                        </div>
                        <div v-else class="flex flex-col items-center gap-4 text-center py-6">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                <ImageIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <div>
                                <p class="text-base font-bold text-gray-700 dark:text-gray-300">
                                    <label class="text-primary cursor-pointer hover:underline">
                                        Klik untuk upload
                                        <input
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden"
                                            @change="onFileInput"
                                        />
                                    </label>
                                </p>
                                <p class="mt-1 text-sm text-gray-400">JPG, PNG, WebP · Maks. 5MB</p>
                            </div>
                        </div>
                    </div>
                    <p v-if="form.errors.image" class="mt-2 text-sm text-red-500">{{ form.errors.image }}</p>
                </div>

                <!-- Inputs -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">
                            Deskripsi Alt <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.alt"
                            type="text"
                            placeholder="Contoh: Suasana workshop membatik..."
                            class="w-full rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 dark:bg-gray-800 dark:text-white"
                        />
                        <p class="mt-1.5 text-xs text-gray-400 italic">Deskripsi ini digunakan untuk aksesibilitas dan SEO.</p>
                        <p v-if="form.errors.alt" class="mt-2 text-sm text-red-500">{{ form.errors.alt }}</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-gray-700 dark:text-gray-300">
                            Urutan Tampil
                        </label>
                        <input
                            v-model="form.order"
                            type="number"
                            min="0"
                            class="w-full rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 dark:bg-gray-800 dark:text-white"
                        />
                        <p v-if="form.errors.order" class="mt-2 text-sm text-red-500">{{ form.errors.order }}</p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-end gap-4">
                    <Link
                        href="/admin/gallery"
                        class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-primary px-10 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/20 transition-all hover:bg-orange-600 disabled:opacity-50 active:scale-[0.98]"
                    >
                        {{ form.processing ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Upload Gambar') }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
