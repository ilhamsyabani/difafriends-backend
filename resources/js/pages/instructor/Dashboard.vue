<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import SectionHeader from '@/components/SectionHeader.vue';
import StatsCard from '@/components/StatsCard.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { useFormatters } from '@/composables/useFormatters';
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

const { formatPrice } = useFormatters();
</script>

<template>
    <AppLayout>
        <Head title="Dashboard Instruktur" />

        <div class="max-w-7xl space-y-6 p-6">
            <div>
                <h1 class="text-2xl font-bold">Dashboard Instruktur</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Pantau performa kelas kamu
                </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <StatsCard
                    label="Total Kelas"
                    :value="stats.totalCourses"
                    color="brand"
                />
                <StatsCard
                    label="Total Siswa"
                    :value="stats.totalStudents"
                    color="blue"
                />
                <StatsCard
                    label="Total Pendapatan"
                    :value="formatPrice(stats.totalRevenue)"
                    color="green"
                />
            </div>

            <!-- Kelas list -->
            <div
                class="overflow-hidden rounded-2xl border border-border bg-card"
            >
                <SectionHeader
                    title="Kelas Saya"
                    href="/instructor/courses"
                    link-text="Kelola semua"
                />

                <EmptyState
                    v-if="stats.courses.length === 0"
                    message="Belum ada kelas."
                >
                    <template #action>
                        <Link
                            href="/instructor/courses/create"
                            class="text-sm text-brand hover:underline"
                        >
                            Buat kelas pertama →
                        </Link>
                    </template>
                </EmptyState>

                <div v-else>
                    <div
                        v-for="course in stats.courses"
                        :key="course.id"
                        class="flex items-center justify-between border-b border-border px-5 py-4 transition-colors last:border-0 hover:bg-muted/30"
                    >
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium">
                                {{ course.title }}
                            </p>
                            <p class="mt-0.5 text-sm text-muted-foreground">
                                {{ course.enrollments_count }} siswa terdaftar
                            </p>
                        </div>
                        <div class="ml-4 flex items-center gap-3">
                            <StatusBadge :status="course.status" />
                            <Link
                                :href="`/instructor/courses/${course.id}/manage`"
                                class="text-sm text-brand hover:underline"
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
