<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Loader2 } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    activity: {
        id: number;
        name: string;
        start_date: string;
        end_date: string;
        location: string;
        description: string | null;
        linkid_product_uuid: string | null;
    } | null;
}>();

const isEdit = !!props.activity;

const form = useForm({
    name: props.activity?.name ?? '',
    start_date: props.activity?.start_date ?? '',
    end_date: props.activity?.end_date ?? '',
    location: props.activity?.location ?? '',
    description: props.activity?.description ?? '',
    linkid_product_uuid: props.activity?.linkid_product_uuid ?? '',
});

function submit() {
    if (isEdit) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/admin/activities/${props.activity!.id}`,
        );
    } else {
        form.post('/admin/activities');
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Kegiatan' : 'Buat Kegiatan Baru'" />

        <div class="mx-auto max-w-3xl p-6 sm:p-10">
            <div class="mb-10 flex items-center gap-4">
                <Link
                    href="/admin/activities"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm hover:border-purple-200 hover:bg-purple-50 hover:text-primary dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ isEdit ? 'Edit Kegiatan' : 'Buat Kegiatan Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Isi nama, tanggal, dan lokasi kegiatan.
                    </p>
                </div>
            </div>

            <form
                @submit.prevent="submit"
                class="space-y-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kegiatan</label>
                    <Input v-model="form.name" type="text" placeholder="mis. Webinar Nasional ABK 2026" />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                        <Input v-model="form.start_date" type="date" />
                        <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-500">{{ form.errors.start_date }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai</label>
                        <Input v-model="form.end_date" type="date" />
                        <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-500">{{ form.errors.end_date }}</p>
                    </div>
                </div>
                <p class="-mt-3 text-xs text-gray-400">
                    Tiap hari dalam rentang tanggal akan otomatis punya link absensi sendiri.
                </p>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
                    <Input v-model="form.location" type="text" placeholder="mis. Aula / Zoom" />
                    <p v-if="form.errors.location" class="mt-1 text-sm text-red-500">{{ form.errors.location }}</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi (opsional)</label>
                    <textarea
                        v-model="form.description"
                        rows="3"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800"
                    ></textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">LinkId Product UUID</label>
                    <Input v-model="form.linkid_product_uuid" type="text" placeholder="UUID produk dari dashboard Lynk.id" />
                    <p class="mt-1 text-xs text-gray-400">UUID dari Lynk.id untuk identifikasi transaksi otomatis.</p>
                    <p v-if="form.errors.linkid_product_uuid" class="mt-1 text-sm text-red-500">{{ form.errors.linkid_product_uuid }}</p>
                </div>

                <div class="flex justify-end border-t border-gray-100 pt-6 dark:border-gray-800">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-purple-700 disabled:opacity-50"
                    >
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <Save v-else class="h-4 w-4" />
                        {{ isEdit ? 'Simpan Perubahan' : 'Buat Kegiatan' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
