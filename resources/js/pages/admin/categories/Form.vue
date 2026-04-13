<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Loader2 } from 'lucide-vue-next';
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
    parents: Array<{ id: number; name: string }>;
    category: {
        id: number;
        name: string;
        parent_id: number | null;
        description: string | null;
        is_active: boolean;
        sort_order: number;
    } | null;
}>();

const isEdit = !!props.category;

const form = useForm({
    name: props.category?.name ?? '',
    // parent_id: props.category?.parent_id ?? '',
    parent_id: props.category?.parent_id?.toString() ?? 'null',
    description: props.category?.description ?? '',
    is_active: props.category?.is_active ?? true,
    sort_order: props.category?.sort_order ?? 0,
});

function submit() {
    form.transform((data) => ({
        ...data,
        parent_id: data.parent_id === 'null' ? null : data.parent_id,
    }));

    if (isEdit) {
        form.put(`/admin/categories/${props.category!.id}`);
    } else {
        form.post('/admin/categories');
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Kategori' : 'Tambah Kategori'" />

        <!-- Lebar layout diperbesar sedikit untuk mengakomodasi split-layout -->
        <div class="mx-auto max-w-7xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-10 flex items-center gap-4">
                <Link
                    href="/admin/categories"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-all hover:border-purple-200 hover:bg-purple-50 hover:text-primary dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-purple-400"
                >
                    <ArrowLeft
                        class="h-5 w-5 transition-transform group-hover:-translate-x-1"
                    />
                </Link>
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{ isEdit ? 'Edit Kategori' : 'Buat Kategori Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Kelola data kategori untuk mengorganisir kelas Anda.
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
                            Nama dan deskripsi ini akan ditampilkan di halaman
                            publik untuk membantu siswa menemukan apa yang
                            mereka cari.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="space-y-6">
                            <!-- Nama -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Nama Kategori
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Contoh: Pemrograman Web"
                                    :class="[
                                        'block w-full rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900',
                                        form.errors.name
                                            ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                                            : '',
                                    ]"
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Deskripsi
                                    <span class="font-normal text-gray-400"
                                        >(Opsional)</span
                                    >
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Kategori ini membahas tentang..."
                                    class="block w-full resize-y rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pemisah -->
                <div class="h-px w-full bg-gray-100 dark:bg-gray-800"></div>

                <!-- Section 2: Pengaturan -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Pengaturan & Hirarki
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Atur posisi kategori ini di dalam sistem, urutan
                            tampilannya, dan status visibilitasnya.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="space-y-6">
                            <!-- Grid 2 Kolom untuk Parent & Sort -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Parent -->
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Induk Kategori
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
                                                    v-for="parent in parents"
                                                    :key="parent.id"
                                                    :value="
                                                        parent.id.toString()
                                                    "
                                                >
                                                    {{ parent.name }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>

                                    <p
                                        v-if="form.errors.parent_id"
                                        class="mt-1.5 text-sm text-red-500"
                                    >
                                        {{ form.errors.parent_id }}
                                    </p>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Urutan Tampil
                                    </label>
                                    <input
                                        v-model="form.sort_order"
                                        type="number"
                                        min="0"
                                        class="block w-full rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                                    />
                                </div>
                            </div>

                            <!-- Modern Toggle Switch -->
                            <div
                                class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Status Visibilitas
                                    </p>
                                    <p
                                        class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Tampilkan kategori ini ke publik.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="form.is_active = !form.is_active"
                                    :class="
                                        form.is_active
                                            ? 'bg-primary'
                                            : 'bg-gray-200 dark:bg-gray-700'
                                    "
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                                >
                                    <span class="sr-only">Use setting</span>
                                    <span
                                        aria-hidden="true"
                                        :class="
                                            form.is_active
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <Link
                        href="/admin/categories"
                        class="rounded-xl px-5 py-2.5 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-70 dark:focus:ring-offset-gray-900"
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
                                  : 'Buat Kategori'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
