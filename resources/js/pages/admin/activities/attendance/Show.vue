<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Copy, Download, Edit2, Check, Lock, Unlock, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from '@/composables/useToast';

const toast = useToast();

const props = defineProps<{
    attendanceForm: {
        id: number;
        title: string;
        activity: { id: number; name: string };
    };
    sessions: Array<{
        id: number;
        session_date: string;
        token: string;
        is_open: boolean;
        attendances_count: number;
        public_url: string;
        qr: string;
    }>;
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });
const copiedId = ref<number | null>(null);

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

async function copyLink(session: (typeof props.sessions)[number]) {
    await navigator.clipboard.writeText(session.public_url);
    copiedId.value = session.id;
    toast.success('Link disalin.');
    setTimeout(() => (copiedId.value = null), 1500);
}

function downloadQr(session: (typeof props.sessions)[number]) {
    const link = document.createElement('a');
    link.href = session.qr;
    link.download = `qr-absensi-${session.session_date}.svg`;
    link.click();
}

function toggle(session: (typeof props.sessions)[number]) {
    router.patch(`/admin/attendance-sessions/${session.token}/toggle`, {}, { preserveScroll: true });
}
</script>

<template>
    <AppLayout>
        <Head :title="attendanceForm.title" />

        <div class="mx-auto max-w-5xl p-6 sm:p-10">
            <div class="mb-8 flex items-center gap-4">
                <Link
                    :href="`/admin/activities/${attendanceForm.activity.id}`"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm hover:border-purple-200 hover:bg-purple-50 hover:text-primary dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ attendanceForm.title }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ attendanceForm.activity.name }}</p>
                </div>
                <Link
                    :href="`/admin/attendance-forms/${attendanceForm.id}/edit`"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <Edit2 class="h-4 w-4" /> Edit Field
                </Link>
                <Link
                    :href="`/admin/attendance-forms/${attendanceForm.id}/attendances`"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <Users class="h-4 w-4" /> Daftar Hadir
                </Link>
                <a
                    :href="`/admin/attendance-forms/${attendanceForm.id}/export`"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-purple-700"
                >
                    <Download class="h-4 w-4" /> Export CSV
                </a>
            </div>

            <div
                v-if="flash.success"
                class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-400"
            >
                {{ flash.success }}
            </div>

            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                Setiap hari punya link & QR sendiri. Bagikan ke peserta untuk hari yang sesuai.
            </p>

            <div class="grid gap-4 sm:grid-cols-2">
                <div
                    v-for="session in sessions"
                    :key="session.id"
                    class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(session.session_date) }}</p>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ session.attendances_count }} peserta hadir</p>
                        </div>
                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="session.is_open
                                ? 'bg-green-50 text-green-600 dark:bg-green-900/20'
                                : 'bg-gray-100 text-gray-500 dark:bg-gray-800'"
                        >
                            {{ session.is_open ? 'Dibuka' : 'Ditutup' }}
                        </span>
                    </div>

                    <div class="mt-4 flex justify-center rounded-xl border border-gray-100 bg-white p-3 dark:border-gray-800">
                        <img :src="session.qr" alt="QR absensi" class="h-40 w-40" />
                    </div>

                    <div class="mt-4 flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800">
                        <span class="flex-1 truncate text-xs text-gray-500 dark:text-gray-400">{{ session.public_url }}</span>
                        <button type="button" @click="copyLink(session)" class="text-gray-400 hover:text-primary">
                            <Check v-if="copiedId === session.id" class="h-4 w-4 text-green-500" />
                            <Copy v-else class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="mt-3 flex items-center gap-2">
                        <button
                            type="button"
                            @click="downloadQr(session)"
                            class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            <Download class="h-3.5 w-3.5" /> QR
                        </button>
                        <button
                            type="button"
                            @click="toggle(session)"
                            class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium"
                            :class="session.is_open
                                ? 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800'
                                : 'bg-primary text-white hover:bg-purple-700'"
                        >
                            <component :is="session.is_open ? Lock : Unlock" class="h-3.5 w-3.5" />
                            {{ session.is_open ? 'Tutup' : 'Buka' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
