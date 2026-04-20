<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
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
    activities: {
        data: Array<{
            id: number;
            log_name: string;
            description: string;
            event: string | null;
            subject_type: string | null;
            subject_id: number | null;
            subject: { id: number; label: string } | null;
            causer: { id: number; name: string; email: string } | null;
            properties: {
                old?: Record<string, any>;
                attributes?: Record<string, any>;
            };
            created_at: string;
        }>;
        links: any[];
        current_page: number;
        last_page: number;
        total: number;
    };
    causers: Array<{ id: number; name: string }>;
    filters: {
        causer_id?: string;
        log_name?: string;
        event?: string;
        date_from?: string;
        date_to?: string;
    };
}>();

const causerId = ref(props.filters.causer_id ?? 'all');
const logName = ref(props.filters.log_name ?? 'all');
const event = ref(props.filters.event ?? 'all');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');

const expandedId = ref<number | null>(null);

function applyFilters() {
    router.get(
        '/admin/activity-log',
        {
            causer_id: causerId.value === 'all' ? undefined : causerId.value,
            log_name: logName.value === 'all' ? undefined : logName.value,
            event: event.value === 'all' ? undefined : event.value,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

watch([causerId, logName, event], applyFilters);

function eventBadgeClass(ev: string | null) {
    return (
        {
            created:
                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            updated:
                'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            deleted:
                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        }[ev ?? ''] ??
        'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
    );
}

function toggleExpand(id: number) {
    expandedId.value = expandedId.value === id ? null : id;
}
</script>

<template>
    <AppLayout>
        <Head title="Log Aktivitas" />

        <div class="max-w-7xl space-y-6 p-6 sm:p-10">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Log Aktivitas
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Riwayat semua aksi yang dilakukan oleh pengguna di sistem.
                </p>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-3">
                <Select v-model="causerId">
                    <SelectTrigger>
                        <SelectValue placeholder="Semua pengguna" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="all">Semua pengguna</SelectItem>
                            <SelectItem
                                v-for="c in causers"
                                :key="c.id"
                                :value="String(c.id)"
                            >
                                {{ c.name }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <Select v-model="logName">
                    <SelectTrigger>
                        <SelectValue placeholder="Semua model" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="all">Semua model</SelectItem>
                            <SelectItem value="default">User</SelectItem>
                            <SelectItem value="Course">Course</SelectItem>
                            <SelectItem value="Booking">Booking</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <Select v-model="event">
                    <SelectTrigger>
                        <SelectValue placeholder="Semua event" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="all">Semua event</SelectItem>
                            <SelectItem value="created">Created</SelectItem>
                            <SelectItem value="updated">Updated</SelectItem>
                            <SelectItem value="deleted">Deleted</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <Input v-model="dateFrom" type="date" @change="applyFilters" />
                <Input v-model="dateTo" type="date" @change="applyFilters" />
            </div>

            <!-- Table -->
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
            >
                <table
                    class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800"
                >
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300"
                            >
                                Waktu
                            </th>
                            <th
                                class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300"
                            >
                                Pengguna
                            </th>
                            <th
                                class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300"
                            >
                                Aksi
                            </th>
                            <th
                                class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300"
                            >
                                Objek
                            </th>
                            <th
                                class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300"
                            >
                                Detail
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-100 dark:divide-gray-800"
                    >
                        <template v-if="activities.data.length === 0">
                            <tr>
                                <td
                                    colspan="5"
                                    class="py-12 text-center text-gray-400"
                                >
                                    Belum ada log aktivitas.
                                </td>
                            </tr>
                        </template>
                        <template v-for="log in activities.data" :key="log.id">
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/40"
                            >
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400"
                                >
                                    {{ log.created_at }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        v-if="log.causer"
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ log.causer.name }}
                                    </span>
                                    <span v-else class="text-gray-400 italic"
                                        >Sistem</span
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                        :class="eventBadgeClass(log.event)"
                                    >
                                        {{ log.event ?? log.description }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-gray-700 dark:text-gray-300"
                                >
                                    <span
                                        v-if="log.subject_type"
                                        class="font-medium"
                                        >{{ log.subject_type }}</span
                                    >
                                    <span
                                        v-if="log.subject"
                                        class="ml-1 text-gray-500"
                                    >
                                        — {{ log.subject.label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        v-if="
                                            log.properties?.attributes ||
                                            log.properties?.old
                                        "
                                        class="text-xs text-primary hover:underline"
                                        @click="toggleExpand(log.id)"
                                    >
                                        {{
                                            expandedId === log.id
                                                ? 'Sembunyikan'
                                                : 'Lihat perubahan'
                                        }}
                                    </button>
                                </td>
                            </tr>
                            <!-- Expanded detail -->
                            <tr
                                v-if="expandedId === log.id"
                                class="bg-gray-50 dark:bg-gray-800/30"
                            >
                                <td colspan="5" class="px-6 py-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div v-if="log.properties?.old">
                                            <p
                                                class="mb-1 text-xs font-semibold text-gray-500 uppercase"
                                            >
                                                Sebelum
                                            </p>
                                            <pre
                                                class="rounded bg-red-50 p-3 text-xs text-red-700 dark:bg-red-900/20 dark:text-red-400"
                                                >{{
                                                    JSON.stringify(
                                                        log.properties.old,
                                                        null,
                                                        2,
                                                    )
                                                }}</pre
                                            >
                                        </div>
                                        <div v-if="log.properties?.attributes">
                                            <p
                                                class="mb-1 text-xs font-semibold text-gray-500 uppercase"
                                            >
                                                Sesudah
                                            </p>
                                            <pre
                                                class="rounded bg-green-50 p-3 text-xs text-green-700 dark:bg-green-900/20 dark:text-green-400"
                                                >{{
                                                    JSON.stringify(
                                                        log.properties
                                                            .attributes,
                                                        null,
                                                        2,
                                                    )
                                                }}</pre
                                            >
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                class="flex items-center justify-between text-sm text-gray-500"
            >
                <span>Total {{ activities.total }} log</span>
                <div class="flex gap-1">
                    <template
                        v-for="link in activities.links"
                        :key="link.label"
                    >
                        <button
                            v-if="link.url"
                            class="rounded px-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-800"
                            :class="
                                link.active
                                    ? 'bg-primary text-white hover:bg-primary'
                                    : ''
                            "
                            @click="router.visit(link.url)"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="rounded px-3 py-1 text-gray-300 dark:text-gray-600"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
