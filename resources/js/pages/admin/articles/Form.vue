<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Loader2, ImageIcon } from 'lucide-vue-next';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';

// Import Quill Editor
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps<{
    article?: {
        id: number;
        title: string;
        slug: string;
        thumbnail: string | null;
        content: string;
        status: string;
    } | null;
}>();

const isEdit = !!props.article;

const form = useForm({
    title: props.article?.title ?? '',
    thumbnail: null as File | null,
    content: props.article?.content ?? '',
    status: props.article?.status ?? 'published',
});

function handleFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.thumbnail = target.files[0];
    }
}

function submit() {
    if (isEdit) {
        // Trik khusus Laravel: Upload file (FormData) harus via POST + _method: PUT
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(`/admin/articles/${props.article!.id}`);
    } else {
        form.post('/admin/articles');
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Artikel' : 'Tulis Artikel'" />

        <div class="mx-auto max-w-7xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-10 flex items-center gap-4">
                <Link
                    href="/admin/articles"
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
                        {{ isEdit ? 'Edit Artikel' : 'Tulis Artikel Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Buat artikel edukatif untuk publik dan optimalkan SEO
                        platform.
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
                            Judul artikel dan gambar sampul (thumbnail) yang
                            menarik akan meningkatkan jumlah pembaca.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="space-y-6">
                            <!-- Judul -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Judul Artikel
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Masukkan judul artikel yang menarik..."
                                    :class="[
                                        'block w-full rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900',
                                        form.errors.title
                                            ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
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

                            <!-- Thumbnail -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Gambar Sampul (Thumbnail)
                                    <span v-if="!isEdit" class="text-red-500"
                                        >*</span
                                    >
                                </label>

                                <div
                                    v-if="
                                        isEdit &&
                                        article?.thumbnail &&
                                        !form.thumbnail
                                    "
                                    class="mb-4"
                                >
                                    <img
                                        :src="`/storage/${article.thumbnail}`"
                                        class="h-32 w-auto rounded-xl border object-cover dark:border-gray-700"
                                        alt="Current Thumbnail"
                                    />
                                </div>

                                <input
                                    type="file"
                                    @change="handleFileChange"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-purple-50 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-900/30 dark:file:text-purple-400"
                                />
                                <p
                                    v-if="form.errors.thumbnail"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.thumbnail }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pemisah -->
                <div class="h-px w-full bg-gray-100 dark:bg-gray-800"></div>

                <!-- Section 2: Konten -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Isi Artikel
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Tulis konten artikel Anda di sini. Gunakan format
                            yang rapi agar mudah dibaca.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div
                            class="flex h-[500px] flex-col overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700"
                        >
                            <QuillEditor
                                v-model:content="form.content"
                                contentType="html"
                                theme="snow"
                                class="flex-1 overflow-y-auto"
                            />
                        </div>
                        <p
                            v-if="form.errors.content"
                            class="mt-1.5 text-sm text-red-500"
                        >
                            {{ form.errors.content }}
                        </p>
                    </div>
                </div>

                <!-- Pemisah -->
                <div class="h-px w-full bg-gray-100 dark:bg-gray-800"></div>

                <!-- Section 3: Pengaturan -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Pengaturan & Publikasi
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Pilih status artikel apakah akan langsung
                            ditayangkan atau disimpan sebagai draft.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <!-- Status -->
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Status Artikel
                            </label>
                            <Select v-model="form.status">
                                <SelectTrigger
                                    :class="[
                                        'mt-1 w-full rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 md:w-1/2 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900',
                                        form.errors.status
                                            ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                            : '',
                                    ]"
                                >
                                    <SelectValue
                                        placeholder="Pilih status..."
                                    />
                                </SelectTrigger>
                                <SelectContent
                                    class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                                >
                                    <SelectGroup>
                                        <SelectItem value="published"
                                            >Tayangkan (Published)</SelectItem
                                        >
                                        <SelectItem value="draft"
                                            >Simpan Sementara
                                            (Draft)</SelectItem
                                        >
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <p
                                v-if="form.errors.status"
                                class="mt-1.5 text-sm text-red-500"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <Link
                        href="/admin/articles"
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
                                  : 'Terbitkan Artikel'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style>
/* Reset tinggi default Quill agar fit dalam container */
.ql-container {
    height: 100% !important;
    font-family: inherit !important;
    font-size: 15px !important;
}
.ql-toolbar {
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;
    background-color: #f9fafb; /* gray-50 */
}
.dark .ql-toolbar {
    background-color: #1f2937; /* gray-800 */
    border-color: #374151 !important; /* gray-700 */
}
.dark .ql-container {
    border-color: transparent !important;
}
.dark .ql-editor {
    color: #f3f4f6; /* gray-100 */
}
</style>
