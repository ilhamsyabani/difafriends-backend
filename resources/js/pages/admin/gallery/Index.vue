<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onUnmounted } from 'vue';
import { X, Upload, Image as ImageIcon, Trash2 } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';
import AppLayout from '@/layouts/AppLayout.vue';

const toast = useToast();
const confirm = useConfirm();

const props = defineProps<{
    galleries: Array<{
        id: number;
        filename: string;
        alt: string;
        path: string;
        order: number;
    }>;
}>();

const uploadForm = useForm({
    image: null as File | null,
    alt: '',
});

const previewUrl = ref<string | null>(null);
const isDragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

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
    // Revoke old object URL to prevent memory leaks
    if (previewUrl.value) {
        window.URL.revokeObjectURL(previewUrl.value);
    }
    
    uploadForm.image = file;
    previewUrl.value = window.URL.createObjectURL(file);
}

function removeFile() {
    if (previewUrl.value) {
        window.URL.revokeObjectURL(previewUrl.value);
    }
    uploadForm.image = null;
    previewUrl.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

function submitUpload() {
    if (!uploadForm.image) {
        toast.error('Pilih gambar terlebih dahulu.');
        return;
    }

    if (!uploadForm.alt) {
        toast.error('Deskripsi Alt wajib diisi.');
        return;
    }

    uploadForm.post('/admin/gallery', {
        forceFormData: true,
        onSuccess: () => {
            removeFile();
            uploadForm.reset();
            toast.success('Gambar berhasil diupload.');
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            toast.error(Array.isArray(firstError) ? firstError[0] : String(firstError));
        },
    });
}

async function destroy(id: number) {
    const ok = await confirm('Hapus Gambar', 'Yakin ingin menghapus gambar ini?');
    if (!ok) return;

    router.delete(`/admin/gallery/${id}`, {
        onSuccess: () => {
            toast.success('Gambar berhasil dihapus.');
        },
    });
}

onUnmounted(() => {
    if (previewUrl.value) {
        window.URL.revokeObjectURL(previewUrl.value);
    }
});
</script>

<template>
    <AppLayout>
        <Head title="Kelola Galeri" />

        <div class="mx-auto max-w-7xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-10">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Kelola Galeri
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Upload dan kelola foto kegiatan atau workshop dalam satu halaman.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
                <!-- Kolom Kiri: Form Upload -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-6">
                        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">
                                Upload Gambar Baru
                            </h2>

                            <!-- Dropzone -->
                            <div
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="onDrop"
                                :class="[
                                    'mb-4 flex flex-col items-center justify-center rounded-xl border-2 border-dashed p-6 transition-all',
                                    isDragging
                                        ? 'border-primary bg-primary/5'
                                        : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600',
                                ]"
                            >
                                <div v-if="previewUrl" class="flex flex-col items-center gap-4">
                                    <div class="relative group">
                                        <img
                                            :src="previewUrl"
                                            class="h-40 w-full max-w-xs rounded-lg object-cover shadow-md"
                                        />
                                        <button
                                            type="button"
                                            @click="removeFile"
                                            class="absolute -top-2 -right-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-50 text-white shadow-lg hover:bg-red-600 transition-colors"
                                        >
                                            <X class="h-3 w-3" />
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-gray-500 truncate max-w-[150px]">
                                        {{ uploadForm.image?.name }}
                                    </p>
                                </div>
                                <div v-else class="flex flex-col items-center gap-3 text-center py-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                        <ImageIcon class="h-6 w-6 text-gray-400" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <label class="text-primary cursor-pointer hover:underline">
                                                Klik untuk upload
                                                <input
                                                    ref="fileInput"
                                                    type="file"
                                                    accept="image/jpeg,image/png,image/webp"
                                                    class="hidden"
                                                    @change="onFileInput"
                                                />
                                            </label>
                                        </p>
                                        <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP · Maks. 5MB</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Deskripsi Alt <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="uploadForm.alt"
                                        type="text"
                                        placeholder="Contoh: Suasana Workshop..."
                                        class="w-full rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary/10 dark:bg-gray-800 dark:text-white"
                                    />
                                    <p class="mt-1 text-[10px] text-gray-400 italic">
                                        Penting untuk SEO dan aksesibilitas.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="submitUpload"
                                    :disabled="uploadForm.processing || !uploadForm.image || !uploadForm.alt"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3.5 text-sm font-bold text-white shadow-md shadow-primary/20 transition-all hover:bg-orange-600 disabled:opacity-50 active:scale-[0.98]"
                                >
                                    <Upload class="h-4 w-4" />
                                    {{ uploadForm.processing ? 'Mengupload...' : 'Upload Gambar' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Daftar Galeri -->
                <div class="lg:col-span-2">
                    <div
                        v-if="galleries.length > 0"
                        class="grid grid-cols-1 gap-6 sm:grid-cols-2"
                    >
                        <div
                            v-for="gallery in galleries"
                            :key="gallery.id"
                            class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
                        >
                            <div class="aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-800">
                                <img
                                    :src="`/storage/${gallery.path}`"
                                    :alt="gallery.alt"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                />
                            </div>
                            <div class="p-4 bg-white dark:bg-gray-900">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="line-clamp-1 text-sm font-bold text-gray-900 dark:text-white">
                                            {{ gallery.alt }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-400 truncate">
                                            {{ gallery.filename }}
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="destroy(gallery.id)"
                                        class="shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all"
                                        title="Hapus Gambar"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Order Badge -->
                            <div class="absolute left-3 top-3 flex h-6 w-6 items-center justify-center rounded-full bg-black/50 text-[10px] font-bold text-white backdrop-blur-sm">
                                {{ gallery.order }}
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 py-20 px-4 text-center dark:border-gray-800"
                    >
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-800/50 mb-4">
                            <ImageIcon class="h-10 w-10 text-gray-300 dark:text-gray-600" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Galeri Masih Kosong</h3>
                        <p class="mt-2 max-w-xs text-sm text-gray-500 dark:text-gray-400">
                            Belum ada gambar yang diupload. Gunakan form di sebelah kiri untuk menambahkan foto baru ke galeri.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
