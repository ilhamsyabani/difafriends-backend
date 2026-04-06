<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    stats: {
        totalCourses: number;
        totalStudents: number;
        totalRevenue: number;
        courses: Array<{
            id: number;
            title: string;
            status: string;
            enrollments_count: number;
            slug: string;
        }>;
    };
}>();

function formatPrice(price: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
}

function statusColor(status: string): string {
    const map: Record<string, string> = {
        published:
            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        draft: 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
        review: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        archived: 'bg-red-100 text-red-500',
    };
    return map[status] ?? 'bg-gray-100 text-gray-500';
}
</script>

<template>
    <AppLayout>
        <Head title="Instructor Dashboard" />

        <div class="max-w-7xl space-y-6 p-6">
            <div>
                <h1 class="text-2xl font-bold">Dashboard Instruktur</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pantau performa kelas kamu
                </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    v-for="stat in [
                        {
                            label: 'Total Kelas',
                            value: stats.totalCourses,
                            color: 'purple',
                        },
                        {
                            label: 'Total Siswa',
                            value: stats.totalStudents,
                            color: 'blue',
                        },
                        {
                            label: 'Total Revenue',
                            value: formatPrice(stats.totalRevenue),
                            color: 'green',
                        },
                    ]"
                    :key="stat.label"
                    class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
                >
                    <p class="mb-1 text-sm text-gray-500">{{ stat.label }}</p>
                    <p class="text-2xl font-bold">{{ stat.value }}</p>
                </div>
            </div>

            <!-- Kelas list -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800"
            >
                <div
                    class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700"
                >
                    <h2 class="font-semibold">Kelas Saya</h2>
                    <Link
                        href="/instructor/courses"
                        class="text-sm text-purple-600 hover:underline"
                    >
                        Kelola semua
                    </Link>
                </div>

                <div
                    v-if="stats.courses.length === 0"
                    class="py-12 text-center text-sm text-gray-400"
                >
                    <p>Belum ada kelas.</p>
                    <Link
                        href="/instructor/courses/create"
                        class="mt-2 inline-block text-purple-600 hover:underline"
                    >
                        Buat kelas pertama →
                    </Link>
                </div>

                <div v-else>
                    <div
                        v-for="course in stats.courses"
                        :key="course.id"
                        class="flex items-center justify-between border-b border-gray-100 px-5 py-4 transition-colors last:border-0 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/30"
                    >
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium">
                                {{ course.title }}
                            </p>
                            <p class="mt-0.5 text-sm text-gray-400">
                                {{ course.enrollments_count }} siswa terdaftar
                            </p>
                        </div>
                        <div class="ml-4 flex items-center gap-3">
                            <span
                                :class="[
                                    'rounded-full px-2 py-1 text-xs font-medium',
                                    statusColor(course.status),
                                ]"
                            >
                                {{ course.status }}
                            </span>
                            <Link
                                :href="`/instructor/courses/${course.id}/manage`"
                                class="text-sm text-purple-600 hover:underline"
                            >
                                Kelola
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
