<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Download, Users, X, PenLine } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Attendance {
    id: number;
    name: string;
    values: Record<string, string>;
    signature_url: string | null;
    submitted_at: string | null;
}

const props = defineProps<{
    attendanceForm: { id: number; title: string };
    activity: { id: number; name: string };
    columns: Array<{ key: string; label: string }>;
    sessions: Array<{ id: number; session_date: string; attendances: Attendance[] }>;
}>();

const activeSessionId = ref<number>(props.sessions[0]?.id ?? 0);
const previewSignature = ref<string | null>(null);

const activeSession = computed(() => props.sessions.find((s) => s.id === activeSessionId.value) ?? null);

const totalHadir = computed(() => props.sessions.reduce((sum, s) => sum + s.attendances.length, 0));

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short' });
}

function formatDateTime(dt: string | null): string {
    if (!dt) return '-';
    return new Date(dt).toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
    <AppLayout>
        <Head :title="`Daftar Hadir — ${attendanceForm.title}`" />

        <div class="mx-auto max-w-6xl p-6 sm:p-10">
            <div class="mb-8 flex items-center gap-4">
                <Link
                    :href="`/admin/attendance-forms/${attendanceForm.id}`"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm hover:border-purple-200 hover:bg-purple-50 hover:text-primary dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Daftar Hadir</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ activity.name }} · {{ attendanceForm.title }} ·
                        <span class="inline-flex items-center gap-1"><Users class="h-3.5 w-3.5" /> {{ totalHadir }} total hadir</span>
                    </p>
                </div>
                <a
                    :href="`/admin/attendance-forms/${attendanceForm.id}/export`"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-purple-700"
                >
                    <Download class="h-4 w-4" /> Export CSV
                </a>
            </div>

            <!-- Tab per hari -->
            <div class="mb-5 flex flex-wrap gap-2">
                <button
                    v-for="session in sessions"
                    :key="session.id"
                    type="button"
                    @click="activeSessionId = session.id"
                    class="rounded-full px-4 py-2 text-sm font-medium transition-colors"
                    :class="activeSessionId === session.id
                        ? 'bg-primary text-white'
                        : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300'"
                >
                    {{ formatDate(session.session_date) }}
                    <span class="ml-1 opacity-70">({{ session.attendances.length }})</span>
                </button>
            </div>

            <!-- Tabel -->
            <div class="overflow-x-auto rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th
                                v-for="col in columns"
                                :key="col.key"
                                class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300"
                            >
                                {{ col.label }}
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Tanda Tangan</th>
                            <th class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Waktu Isi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr v-if="!activeSession || activeSession.attendances.length === 0">
                            <td :colspan="columns.length + 3" class="px-4 py-12 text-center text-gray-400">
                                Belum ada peserta yang mengisi absensi hari ini.
                            </td>
                        </tr>
                        <tr
                            v-for="(attendance, index) in activeSession?.attendances ?? []"
                            :key="attendance.id"
                            class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30"
                        >
                            <td class="px-4 py-3 text-gray-400">{{ index + 1 }}</td>
                            <td
                                v-for="col in columns"
                                :key="col.key"
                                class="whitespace-nowrap px-4 py-3 text-gray-700 dark:text-gray-300"
                            >
                                {{ attendance.values[col.key] ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <button
                                    v-if="attendance.signature_url"
                                    type="button"
                                    @click="previewSignature = attendance.signature_url"
                                    class="inline-flex items-center overflow-hidden rounded-md border border-gray-200 bg-white p-0.5 hover:ring-2 hover:ring-primary/40 dark:border-gray-700"
                                >
                                    <img :src="attendance.signature_url" alt="TTD" class="h-8 w-20 object-contain" />
                                </button>
                                <span v-else class="inline-flex items-center gap-1 text-xs text-gray-400">
                                    <PenLine class="h-3.5 w-3.5" /> -
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-500 dark:text-gray-400">
                                {{ formatDateTime(attendance.submitted_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal preview tanda tangan -->
        <div
            v-if="previewSignature"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
            @click.self="previewSignature = null"
        >
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
                <button
                    type="button"
                    @click="previewSignature = null"
                    class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                >
                    <X class="h-5 w-5" />
                </button>
                <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Tanda Tangan</h3>
                <img :src="previewSignature" alt="Tanda tangan" class="w-full rounded-xl border border-gray-100 bg-white dark:border-gray-800" />
            </div>
        </div>
    </AppLayout>
</template>
