<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Plus, Edit2, Trash2, MapPin, CalendarDays, Eye } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useConfirm } from '@/composables/useConfirm';

const confirm = useConfirm();

defineProps<{
    activities: {
        data: Array<{
            id: number;
            name: string;
            start_date: string;
            end_date: string;
            location: string;
            attendance_forms_count: number;
        }>;
        meta?: { total?: number };
    };
}>();

const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string },
);

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

async function destroy(id: number) {
    const ok = await confirm('Hapus Kegiatan', 'Yakin hapus kegiatan ini beserta absensinya?');
    if (!ok) return;
    router.delete(`/admin/activities/${id}`);
}
</script>

<template>
    <AppLayout>
        <Head title="Kegiatan & Presensi" />

        <div class="mx-auto max-w-7xl p-6 sm:p-10">
            <div class="mb-8 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Kegiatan & Presensi
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total {{ activities.meta?.total ?? activities.data.length }} kegiatan.
                    </p>
                </div>
                <Link
                    href="/admin/activities/create"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-purple-700"
                >
                    <Plus class="h-4 w-4" />
                    Kegiatan Baru
                </Link>
            </div>

            <div
                v-if="flash.success"
                class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-400"
            >
                {{ flash.success }}
            </div>

            <div
                v-if="activities.data.length === 0"
                class="rounded-2xl border border-dashed border-gray-200 bg-white p-16 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900"
            >
                Belum ada kegiatan. Buat kegiatan pertama Anda.
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities.data"
                    :key="activity.id"
                    class="flex flex-col rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                >
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ activity.name }}
                    </h3>
                    <div class="mt-3 space-y-1.5 text-sm text-gray-500 dark:text-gray-400">
                        <p class="flex items-center gap-2">
                            <CalendarDays class="h-4 w-4 shrink-0" />
                            {{ formatDate(activity.start_date) }} – {{ formatDate(activity.end_date) }}
                        </p>
                        <p class="flex items-center gap-2">
                            <MapPin class="h-4 w-4 shrink-0" />
                            {{ activity.location }}
                        </p>
                    </div>
                    <span class="mt-3 inline-flex w-fit rounded-full bg-purple-50 px-3 py-1 text-xs font-medium text-primary dark:bg-purple-900/20">
                        {{ activity.attendance_forms_count }} absensi
                    </span>

                    <div class="mt-5 flex items-center gap-2 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <Link
                            :href="`/admin/activities/${activity.id}`"
                            class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-primary px-3 py-2 text-xs font-medium text-white hover:bg-purple-700"
                        >
                            <Eye class="h-3.5 w-3.5" /> Detail
                        </Link>
                        <Link
                            :href="`/admin/activities/${activity.id}/edit`"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                        >
                            <Edit2 class="h-4 w-4" />
                        </Link>
                        <button
                            type="button"
                            @click="destroy(activity.id)"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-red-500 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-red-900/20"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
