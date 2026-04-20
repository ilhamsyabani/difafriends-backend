<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Download,
    TrendingUp,
    Users,
    ShoppingCart,
    BookOpen,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';

interface MonthData {
    month: string;
    value: number;
}

interface TopCourse {
    name: string;
    total_orders: number;
    total_revenue: number;
}

interface Summary {
    total_revenue: number;
    total_orders: number;
    total_enrollments: number;
    new_users: number;
}

const props = defineProps<{
    year: number;
    availableYears: number[];
    revenueChart: MonthData[];
    enrollmentChart: MonthData[];
    userGrowth: MonthData[];
    topCourses: TopCourse[];
    summary: Summary;
}>();

const selectedYear = ref(props.year);

function changeYear(y: number) {
    router.get(
        '/admin/reports',
        { year: y },
        { preserveState: true, replace: true },
    );
}

function exportCsv() {
    window.location.href = `/admin/reports/export?year=${selectedYear.value}`;
}

function formatRp(value: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
}

function maxValue(data: MonthData[]): number {
    return Math.max(...data.map((d) => d.value), 1);
}

function barHeight(value: number, max: number): string {
    return `${Math.round((value / max) * 100)}%`;
}

const revenueMax = computed(() => maxValue(props.revenueChart));
const enrollMax = computed(() => maxValue(props.enrollmentChart));
const userMax = computed(() => maxValue(props.userGrowth));
const topRevenueMax = computed(() =>
    Math.max(...props.topCourses.map((c) => c.total_revenue), 1),
);
</script>

<template>
    <AppLayout>
        <Head title="Laporan & Analitik" />

        <div class="max-w-7xl p-6 sm:p-10">
            <div
                class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Laporan & Analitik
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Data aktivitas platform DifaFriends
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Select
                        v-model="selectedYear"
                        @change="changeYear(selectedYear)"
                    >
                        <SelectTrigger
                            class="mt-1 w-28 rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                        >
                            <SelectValue placeholder="Pilih tahun..." />
                        </SelectTrigger>
                        <SelectContent
                            class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                        >
                            <SelectGroup>
                                <SelectItem
                                    v-for="y in availableYears"
                                    :key="y"
                                    :value="y"
                                >
                                    {{ y }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>

                    <button
                        @click="exportCsv"
                        class="inline-flex h-[42px] items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-purple-700"
                    >
                        <Download class="h-4 w-4" />
                        Export CSV
                    </button>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div
                    v-for="card in [
                        {
                            label: 'Total Revenue',
                            value: formatRp(summary.total_revenue),
                            icon: TrendingUp,
                            color: 'purple',
                        },
                        {
                            label: 'Order Berhasil',
                            value: summary.total_orders,
                            icon: ShoppingCart,
                            color: 'blue',
                        },
                        {
                            label: 'Pendaftaran Kelas',
                            value: summary.total_enrollments,
                            icon: BookOpen,
                            color: 'green',
                        },
                        {
                            label: 'User Baru',
                            value: summary.new_users,
                            icon: Users,
                            color: 'amber',
                        },
                    ]"
                    :key="card.label"
                    class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <p
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            {{ card.label }}
                        </p>
                        <div
                            :class="[
                                'flex h-9 w-9 items-center justify-center rounded-xl',
                                card.color === 'purple'
                                    ? 'bg-purple-100 dark:bg-purple-900/30'
                                    : '',
                                card.color === 'blue'
                                    ? 'bg-blue-100 dark:bg-blue-900/30'
                                    : '',
                                card.color === 'green'
                                    ? 'bg-green-100 dark:bg-green-900/30'
                                    : '',
                                card.color === 'amber'
                                    ? 'bg-amber-100 dark:bg-amber-900/30'
                                    : '',
                            ]"
                        >
                            <component
                                :is="card.icon"
                                :class="[
                                    'h-5 w-5',
                                    card.color === 'purple'
                                        ? 'text-primary'
                                        : '',
                                    card.color === 'blue'
                                        ? 'text-blue-600'
                                        : '',
                                    card.color === 'green'
                                        ? 'text-green-600'
                                        : '',
                                    card.color === 'amber'
                                        ? 'text-amber-600'
                                        : '',
                                ]"
                            />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ card.value }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">Tahun {{ year }}</p>
                </div>
            </div>

            <div
                class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
            >
                <h2 class="mb-6 font-semibold text-gray-900 dark:text-white">
                    Revenue per Bulan (Rp)
                </h2>

                <div class="flex h-56 flex-col">
                    <div
                        class="flex flex-1 items-end gap-1.5 border-b border-gray-100 sm:gap-3 dark:border-gray-700"
                    >
                        <div
                            v-for="item in revenueChart"
                            :key="item.month"
                            class="group relative flex h-full w-full items-end justify-center"
                        >
                            <div
                                class="relative w-full rounded-t-md bg-purple-500 transition-all duration-300 hover:bg-primary dark:bg-primary dark:hover:bg-purple-500"
                                :style="{
                                    height: barHeight(item.value, revenueMax),
                                    minHeight: '4px',
                                }"
                            >
                                <div
                                    class="absolute bottom-full left-1/2 z-10 mb-2 hidden -translate-x-1/2 rounded-lg bg-gray-900 px-2 py-1 text-xs whitespace-nowrap text-white shadow-lg group-hover:block dark:bg-gray-100 dark:text-gray-900"
                                >
                                    {{ formatRp(item.value) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-1.5 pt-2 sm:gap-3">
                        <span
                            v-for="item in revenueChart"
                            :key="'label-' + item.month"
                            class="w-full truncate text-center text-xs font-medium text-gray-400"
                        >
                            {{ item.month }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div
                    class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
                >
                    <h2
                        class="mb-6 font-semibold text-gray-900 dark:text-white"
                    >
                        Pendaftaran Kelas per Bulan
                    </h2>

                    <div class="flex h-44 flex-col">
                        <div
                            class="flex flex-1 items-end gap-1.5 border-b border-gray-100 sm:gap-2 dark:border-gray-700"
                        >
                            <div
                                v-for="item in enrollmentChart"
                                :key="item.month"
                                class="group relative flex h-full w-full items-end justify-center"
                            >
                                <div
                                    class="relative w-full rounded-t-md bg-blue-500 transition-all duration-300 hover:bg-blue-600"
                                    :style="{
                                        height: barHeight(
                                            item.value,
                                            enrollMax,
                                        ),
                                        minHeight: '4px',
                                    }"
                                >
                                    <div
                                        class="absolute bottom-full left-1/2 z-10 mb-2 hidden -translate-x-1/2 rounded-lg bg-gray-900 px-2 py-1 text-xs whitespace-nowrap text-white shadow-lg group-hover:block dark:bg-gray-100 dark:text-gray-900"
                                    >
                                        {{ item.value }} pendaftar
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1.5 pt-2 sm:gap-2">
                            <span
                                v-for="item in enrollmentChart"
                                :key="'label-' + item.month"
                                class="w-full truncate text-center text-xs font-medium text-gray-400"
                            >
                                {{ item.month }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
                >
                    <h2
                        class="mb-6 font-semibold text-gray-900 dark:text-white"
                    >
                        Pengguna Baru per Bulan
                    </h2>

                    <div class="flex h-44 flex-col">
                        <div
                            class="flex flex-1 items-end gap-1.5 border-b border-gray-100 sm:gap-2 dark:border-gray-700"
                        >
                            <div
                                v-for="item in userGrowth"
                                :key="item.month"
                                class="group relative flex h-full w-full items-end justify-center"
                            >
                                <div
                                    class="relative w-full rounded-t-md bg-green-500 transition-all duration-300 hover:bg-green-600"
                                    :style="{
                                        height: barHeight(item.value, userMax),
                                        minHeight: '4px',
                                    }"
                                >
                                    <div
                                        class="absolute bottom-full left-1/2 z-10 mb-2 hidden -translate-x-1/2 rounded-lg bg-gray-900 px-2 py-1 text-xs whitespace-nowrap text-white shadow-lg group-hover:block dark:bg-gray-100 dark:text-gray-900"
                                    >
                                        {{ item.value }} user
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1.5 pt-2 sm:gap-2">
                            <span
                                v-for="item in userGrowth"
                                :key="'label-' + item.month"
                                class="w-full truncate text-center text-xs font-medium text-gray-400"
                            >
                                {{ item.month }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
            >
                <h2 class="mb-6 font-semibold text-gray-900 dark:text-white">
                    Top 10 Kelas — Revenue Tertinggi
                </h2>

                <div
                    v-if="topCourses.length === 0"
                    class="py-10 text-center text-sm text-gray-400"
                >
                    Belum ada data.
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="(course, i) in topCourses"
                        :key="i"
                        class="flex items-center gap-4"
                    >
                        <span
                            class="w-5 shrink-0 text-right text-sm font-bold text-gray-400"
                        >
                            {{ i + 1 }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <div
                                class="flex items-center justify-between text-sm"
                            >
                                <span
                                    class="truncate font-medium text-gray-800 dark:text-gray-200"
                                >
                                    {{ course.name }}
                                </span>
                                <span
                                    class="ml-4 shrink-0 font-semibold text-primary"
                                >
                                    {{ formatRp(course.total_revenue) }}
                                </span>
                            </div>
                            <div
                                class="mt-2 h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700"
                            >
                                <div
                                    class="h-full rounded-full bg-purple-500 transition-all duration-500"
                                    :style="{
                                        width: `${(course.total_revenue / topRevenueMax) * 100}%`,
                                    }"
                                />
                            </div>
                            <p class="mt-1 text-xs text-gray-400">
                                {{ course.total_orders }} transaksi
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
