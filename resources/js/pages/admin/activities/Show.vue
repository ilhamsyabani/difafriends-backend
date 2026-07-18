<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Plus, MapPin, CalendarDays, ClipboardList, Trash2, Edit2 } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useConfirm } from '@/composables/useConfirm';

const confirm = useConfirm();

const props = defineProps<{
    activity: {
        id: number;
        name: string;
        start_date: string;
        end_date: string;
        location: string;
        description: string | null;
        attendance_forms: Array<{
            id: number;
            title: string;
            fields: Array<{ key: string; label: string }>;
            sessions: Array<{ id: number; session_date: string; attendances_count: number }>;
        }>;
    };
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function totalHadir(form: (typeof props.activity.attendance_forms)[number]): number {
    return form.sessions.reduce((sum, s) => sum + s.attendances_count, 0);
}

async function destroyForm(id: number) {
    const ok = await confirm('Hapus Absensi', 'Yakin hapus absensi ini beserta seluruh data kehadirannya?');
    if (!ok) return;
    router.delete(`/admin/attendance-forms/${id}`);
}
</script>

<template>
    <AppLayout>
        <Head :title="activity.name" />

        <div class="mx-auto max-w-5xl p-6 sm:p-10">
            <div class="mb-8 flex items-center gap-4">
                <Link
                    href="/admin/activities"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm hover:border-purple-200 hover:bg-purple-50 hover:text-primary dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ activity.name }}</h1>
                    <div class="mt-1 flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <CalendarDays class="h-4 w-4" />
                            {{ formatDate(activity.start_date) }} – {{ formatDate(activity.end_date) }}
                        </span>
                        <span class="flex items-center gap-1.5"><MapPin class="h-4 w-4" /> {{ activity.location }}</span>
                    </div>
                </div>
                <Link
                    :href="`/admin/activities/${activity.id}/edit`"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2.5 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
                >
                    <Edit2 class="h-4 w-4" />
                </Link>
            </div>

            <div
                v-if="flash.success"
                class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-400"
            >
                {{ flash.success }}
            </div>

            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Absensi</h2>
                <Link
                    :href="`/admin/activities/${activity.id}/attendance-forms/create`"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-purple-700"
                >
                    <Plus class="h-4 w-4" /> Buat Absensi
                </Link>
            </div>

            <div
                v-if="activity.attendance_forms.length === 0"
                class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center text-gray-500 dark:border-gray-800 dark:bg-gray-900"
            >
                Belum ada absensi. Buat absensi lalu bagikan link/QR-nya ke peserta.
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="form in activity.attendance_forms"
                    :key="form.id"
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-white">
                                <ClipboardList class="h-4 w-4 text-primary" /> {{ form.title }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ form.fields.length }} field · {{ form.sessions.length }} hari ·
                                {{ totalHadir(form) }} total hadir
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <Link
                                :href="`/admin/attendance-forms/${form.id}`"
                                class="rounded-lg bg-primary px-3 py-2 text-xs font-medium text-white hover:bg-purple-700"
                            >
                                Link & QR
                            </Link>
                            <button
                                type="button"
                                @click="destroyForm(form.id)"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-red-500 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-red-900/20"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span
                            v-for="session in form.sessions"
                            :key="session.id"
                            class="rounded-full bg-gray-50 px-3 py-1 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                        >
                            {{ formatDate(session.session_date) }} · {{ session.attendances_count }} hadir
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
