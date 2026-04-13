<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    enrollments: {
        data: Array<{
            id: number;
            status: string;
            enrolled_at: string;
            completed_at: string | null;
            course: {
                id: number;
                title: string;
                slug: string;
                thumbnail: string | null;
                instructor: { first_name: string; last_name: string };
                category: { name: string };
                has_certificate: boolean;
            };
        }>;
        links: any[];
        meta: any;
    };
}>();

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function statusLabel(status: string): string {
    const map: Record<string, string> = {
        active: 'Aktif',
        completed: 'Selesai',
        expired: 'Kedaluwarsa',
    };

    return map[status] ?? status;
}

function statusColor(status: string): string {
    const map: Record<string, string> = {
        active: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        completed:
            'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
        expired: 'bg-gray-100 text-gray-500',
    };

    return map[status] ?? 'bg-gray-100 text-gray-500';
}
</script>

<template>
    <AppLayout>
        <Head title="Kelas Saya" />

        <div class="max-w-6xl p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Kelas Saya</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Semua kelas yang sudah kamu ikuti
                </p>
            </div>

            <!-- Empty -->
            <div
                v-if="enrollments.data.length === 0"
                class="py-20 text-center text-gray-400"
            >
                <svg
                    class="mx-auto mb-4 h-16 w-16 opacity-30"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                    />
                </svg>
                <p class="font-medium">Belum ada kelas yang diikuti</p>
                <Link
                    href="/courses"
                    class="mt-2 inline-block text-sm text-purple-600 hover:underline"
                >
                    Lihat katalog kelas →
                </Link>
            </div>

            <!-- Grid -->
            <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="enrollment in enrollments.data"
                    :key="enrollment.id"
                    class="overflow-hidden rounded-2xl border border-gray-100 bg-white transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <!-- Thumbnail -->
                    <div
                        class="relative aspect-video overflow-hidden bg-purple-50 dark:bg-purple-900/20"
                    >
                        <img
                            v-if="enrollment.course.thumbnail"
                            :src="`/storage/${enrollment.course.thumbnail}`"
                            :alt="enrollment.course.title"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center"
                        >
                            <svg
                                class="h-10 w-10 text-purple-200"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1"
                                    d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"
                                />
                            </svg>
                        </div>
                        <span
                            :class="[
                                'absolute top-3 right-3 rounded-full px-2 py-1 text-xs font-medium',
                                statusColor(enrollment.status),
                            ]"
                        >
                            {{ statusLabel(enrollment.status) }}
                        </span>
                    </div>

                    <div class="p-4">
                        <p class="mb-1 text-xs text-gray-400">
                            {{ enrollment.course.category.name }}
                        </p>
                        <h3 class="mb-1 line-clamp-2 text-sm font-semibold">
                            {{ enrollment.course.title }}
                        </h3>
                        <p class="mb-3 text-xs text-gray-500">
                            {{ enrollment.course.instructor.first_name }}
                            {{ enrollment.course.instructor.last_name }}
                        </p>

                        <div class="mb-3 text-xs text-gray-400">
                            Terdaftar {{ formatDate(enrollment.enrolled_at) }}
                        </div>

                        <Link
                            :href="`/learn/${enrollment.course.slug}`"
                            class="block w-full rounded-xl bg-purple-600 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-purple-700"
                        >
                            {{
                                enrollment.status === 'completed'
                                    ? 'Lihat Ulang'
                                    : 'Lanjut Belajar'
                            }}
                        </Link>

                        <a
                            v-if="
                                enrollment.status === 'completed' &&
                                enrollment.course.has_certificate
                            "
                            :href="`/certificates/${enrollment.id}/download`"
                            target="_blank"
                            class="mt-2 block w-full rounded-xl border border-purple-200 py-2 text-center text-sm font-medium text-purple-600 transition-colors hover:bg-purple-50 dark:border-purple-800 dark:hover:bg-purple-900/20"
                        >
                            ⬇ Download Sertifikat
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="enrollments.links?.length > 3"
                class="mt-8 flex justify-center gap-1"
            >
                <Link
                    v-for="link in enrollments.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    :class="[
                        'rounded-lg px-3 py-2 text-sm transition-colors',
                        link.active
                            ? 'bg-purple-600 text-white'
                            : link.url
                              ? 'border border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800'
                              : 'cursor-not-allowed text-gray-300 dark:text-gray-600',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
