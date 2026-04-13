<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Search, Trash2, UserPlus, ArrowLeft, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    course: { id: number; title: string };
    enrollments: {
        data: Array<{
            id: number;
            order_id: number | null;
            status: string;
            enrolled_at: string;
            user: {
                id: number;
                first_name: string;
                last_name: string;
                email: string;
                photo: string | null;
            };
        }>;
        links: any[];
        meta: any;
    };
    users: Array<{
        id: number;
        first_name: string;
        last_name: string;
        email: string;
    }>;
    filters: { search?: string };
}>();

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });

const search = ref(props.filters.search ?? '');
let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            `/admin/courses/${props.course.id}/enrollments`,
            { search: val },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 300);
});

const assignForm = useForm({ user_id: null as number | null });

function assign(userId: number) {
    assignForm.user_id = userId;
    assignForm.post(`/admin/courses/${props.course.id}/enrollments`, {
        preserveScroll: true,
        onSuccess: () => {
            search.value = '';
        },
    });
}

function remove(enrollmentId: number) {
    if (confirm('Hapus akses user ini dari kelas?')) {
        router.delete(
            `/admin/courses/${props.course.id}/enrollments/${enrollmentId}`,
            { preserveScroll: true },
        );
    }
}

function statusClass(status: string) {
    return status === 'active'
        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
        : status === 'completed'
          ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
          : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400';
}

function statusLabel(status: string) {
    const map: Record<string, string> = {
        active: 'Aktif',
        completed: 'Selesai',
        expired: 'Kadaluarsa',
    };
    return map[status] ?? status;
}
</script>

<template>
    <AppLayout>
        <Head :title="`Peserta Kelas - ${course.title}`" />

        <div class="max-w-4xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-8">
                <Link
                    href="/admin/courses"
                    class="mb-4 inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-purple-600 dark:text-gray-400 dark:hover:text-purple-400"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke Daftar Kelas
                </Link>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Kelola Peserta
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ course.title }}
                </p>
            </div>

            <!-- Flash -->
            <div
                v-if="flash.success"
                class="mb-6 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash.error"
                class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-400"
            >
                {{ flash.error }}
            </div>

            <!-- Assign User -->
            <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h2 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">
                    Tambah Peserta
                </h2>

                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <Input
                        v-model="search"
                        type="text"
                        autocomplete="off"
                        placeholder="Cari nama atau email user..."
                        class="pl-9"
                    />
                </div>

                <!-- Hasil pencarian -->
                <div v-if="users.length > 0" class="mt-3 divide-y divide-gray-100 rounded-xl border border-gray-200 dark:divide-gray-800 dark:border-gray-700">
                    <div
                        v-for="user in users"
                        :key="user.id"
                        class="flex items-center justify-between px-4 py-3"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ user.first_name }} {{ user.last_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ user.email }}
                            </p>
                        </div>
                        <Button
                            size="sm"
                            variant="outline"
                            :disabled="assignForm.processing"
                            @click="assign(user.id)"
                            class="gap-1.5"
                        >
                            <UserPlus class="h-3.5 w-3.5" />
                            Tambahkan
                        </Button>
                    </div>
                </div>

                <p
                    v-else-if="search && users.length === 0"
                    class="mt-3 text-sm text-gray-500 dark:text-gray-400"
                >
                    Tidak ada user yang ditemukan atau semua sudah terdaftar.
                </p>
            </div>

            <!-- Daftar Peserta -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                        Peserta Terdaftar
                    </h2>
                    <span class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                        {{ enrollments.meta?.total ?? enrollments.data.length }} peserta
                    </span>
                </div>

                <div v-if="enrollments.data.length > 0" class="divide-y divide-gray-100 dark:divide-gray-800">
                    <div
                        v-for="enrollment in enrollments.data"
                        :key="enrollment.id"
                        class="flex items-center justify-between px-6 py-4"
                    >
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100 text-sm font-semibold text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                {{ enrollment.user.first_name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ enrollment.user.first_name }} {{ enrollment.user.last_name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ enrollment.user.email }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <!-- Badge sumber -->
                            <span
                                :class="[
                                    'rounded-full px-2.5 py-1 text-xs font-medium',
                                    enrollment.order_id
                                        ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                        : 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                ]"
                            >
                                {{ enrollment.order_id ? 'Berbayar' : 'Ditugaskan' }}
                            </span>

                            <!-- Badge status -->
                            <span
                                :class="['rounded-full px-2.5 py-1 text-xs font-medium', statusClass(enrollment.status)]"
                            >
                                {{ statusLabel(enrollment.status) }}
                            </span>

                            <button
                                @click="remove(enrollment.id)"
                                class="text-gray-400 transition-colors hover:text-red-500 dark:hover:text-red-400"
                                title="Hapus dari kelas"
                            >
                                <span class="sr-only">Hapus</span>
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center gap-3 py-16 text-gray-400 dark:text-gray-500">
                    <Users class="h-10 w-10" />
                    <p class="text-sm">Belum ada peserta di kelas ini.</p>
                </div>

                <!-- Pagination -->
                <div
                    v-if="enrollments.links?.length > 3"
                    class="flex justify-end gap-1.5 border-t border-gray-100 px-6 py-4 dark:border-gray-800"
                >
                    <Link
                        v-for="link in enrollments.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'rounded-xl px-3.5 py-2 text-sm font-medium transition-colors',
                            link.active
                                ? 'bg-purple-600 text-white shadow-sm'
                                : link.url
                                  ? 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                                  : 'cursor-not-allowed border border-transparent text-gray-400 dark:text-gray-600',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
