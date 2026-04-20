<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useFormatters } from '@/composables/useFormatters';
import GuestLayout from '@/layouts/GuestLayout.vue';

const { assetUrl } = useFormatters();

const props = defineProps<{
    courses: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            thumbnail: string | null;
            price: number;
            discount_price: number | null;
            duration_minutes: number;
            instructor: { first_name: string; last_name: string };
            category: { name: string };
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
        meta: { total: number; current_page: number; last_page: number };
    };
    categories: Array<{
        id: number;
        name: string;
        slug: string;
        children: Array<{ id: number; name: string; slug: string }>;
    }>;
    filters: {
        search?: string;
        category?: string;
        price?: string;
        sort?: string;
    };
}>();

const search = ref(props.filters.search ?? '');
const category = ref(props.filters.category ?? '');
const price = ref(props.filters.price ?? '');
const sort = ref(props.filters.sort ?? 'latest');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 400);
});
watch([category, price, sort], () => applyFilters());

function applyFilters() {
    router.get(
        '/courses',
        {
            search: search.value || undefined,
            category: category.value || undefined,
            price: price.value || undefined,
            sort: sort.value !== 'latest' ? sort.value : undefined,
        },
        { preserveState: true, replace: true },
    );
}

function formatPrice(p: number): string {
    if (p === 0) {
        return 'Gratis';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(p);
}

function formatDuration(minutes: number): string {
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;

    return h > 0 ? `${h}j ${m}m` : `${m}m`;
}
</script>

<template>
    <GuestLayout>
        <Head title="Katalog Kelas — DifaFriends" />

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="mb-2 text-3xl font-bold">Katalog Kelas</h1>
                <p class="text-gray-500 dark:text-gray-400">
                    {{ courses.meta?.total ?? courses.data.length }} kelas
                    tersedia
                </p>
            </div>

            <!-- ✅ Layout utama: sidebar 1/4 + konten 3/4 -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
                <!-- ── SIDEBAR ─────────────────────────────── -->
                <aside class="space-y-6 lg:col-span-1">
                    <div>
                        <label class="mb-2 block text-sm font-medium"
                            >Cari Kelas</label
                        >
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Nama kelas..."
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium"
                            >Kategori</label
                        >
                        <div class="space-y-1">
                            <button
                                @click="category = ''"
                                :class="[
                                    'w-full rounded-lg px-3 py-2 text-left text-sm transition-colors',
                                    category === ''
                                        ? 'text-primary-hover bg-purple-100 font-medium dark:bg-purple-900/30'
                                        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800',
                                ]"
                            >
                                Semua Kategori
                            </button>

                            <template v-for="cat in categories" :key="cat.id">
                                <button
                                    @click="category = cat.slug"
                                    :class="[
                                        'w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition-colors',
                                        category === cat.slug
                                            ? 'text-primary-hover bg-purple-100 dark:bg-purple-900/30 dark:text-purple-300'
                                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800',
                                    ]"
                                >
                                    {{ cat.name }}
                                </button>
                                <button
                                    v-for="child in cat.children"
                                    :key="child.id"
                                    @click="category = child.slug"
                                    :class="[
                                        'w-full rounded-lg px-3 py-2 pl-6 text-left text-sm transition-colors',
                                        category === child.slug
                                            ? 'bg-purple-100 text-primary dark:bg-purple-900/30 dark:text-purple-400'
                                            : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800',
                                    ]"
                                >
                                    {{ child.name }}
                                </button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium"
                            >Harga</label
                        >
                        <div class="space-y-1">
                            <button
                                v-for="opt in [
                                    { label: 'Semua Harga', value: '' },
                                    { label: 'Gratis', value: 'free' },
                                    { label: 'Berbayar', value: 'paid' },
                                ]"
                                :key="opt.value"
                                @click="price = opt.value"
                                :class="[
                                    'w-full rounded-lg px-3 py-2 text-left text-sm transition-colors',
                                    price === opt.value
                                        ? 'text-primary-hover bg-purple-100 font-medium dark:bg-purple-900/30'
                                        : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800',
                                ]"
                            >
                                {{ opt.label }}
                            </button>
                        </div>
                    </div>

                    <button
                        v-if="search || category || price || sort !== 'latest'"
                        @click="
                            search = '';
                            category = '';
                            price = '';
                            sort = 'latest';
                        "
                        class="w-full py-2 text-sm text-red-500 hover:text-red-600"
                    >
                        Reset Filter
                    </button>
                </aside>

                <!-- ── KONTEN ──────────────────────────────── -->
                <div class="min-w-0 lg:col-span-3">
                    <!-- Sort Bar -->
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <p class="shrink-0 text-sm text-gray-500">
                            Menampilkan {{ courses.data.length }} kelas
                        </p>
                        <Select v-model="sort">
                            <SelectTrigger
                                class="mt-1 w-60 rounded-xl border-transparent bg-gray-50 px-4 py-5 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-900 dark:text-white dark:focus:bg-gray-900"
                            >
                                <SelectValue placeholder="Sort by..." />
                            </SelectTrigger>

                            <SelectContent
                                class="rounded-xl border-gray-100 shadow-lg dark:border-purple-50"
                            >
                                <SelectGroup>
                                    <SelectItem value="latest">
                                        Terbaru
                                    </SelectItem>
                                    <SelectItem value="popular">
                                        Popular
                                    </SelectItem>
                                    <SelectItem value="price_low">
                                        Harga Terendah
                                    </SelectItem>
                                    <SelectItem value="price_high">
                                        Harga Tertinggi
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="courses.data.length === 0"
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
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <p class="font-medium">Kelas tidak ditemukan</p>
                        <p class="mt-1 text-sm">
                            Coba ubah filter pencarian kamu
                        </p>
                    </div>

                    <!-- ✅ Grid course — 2 kolom cukup untuk area 3/4 -->
                    <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <Link
                            v-for="course in courses.data"
                            :key="course.id"
                            :href="`/courses/${course.slug}`"
                            class="group overflow-hidden rounded-2xl border border-gray-100 transition-all hover:border-purple-200 hover:shadow-lg dark:border-gray-800 dark:hover:border-purple-800"
                        >
                            <div
                                class="relative aspect-video overflow-hidden bg-purple-50 dark:bg-purple-900/20"
                            >
                                <img
                                    v-if="course.thumbnail"
                                    :src="assetUrl(course.thumbnail)"
                                    :alt="course.title"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
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
                                    class="text-primary-hover absolute top-3 left-3 rounded-full bg-white/90 px-2 py-1 text-xs font-medium dark:bg-gray-900/90 dark:text-purple-300"
                                >
                                    {{ course.category.name }}
                                </span>
                            </div>

                            <div class="p-4">
                                <h3
                                    class="mb-1 line-clamp-2 text-sm font-semibold transition-colors group-hover:text-primary"
                                >
                                    {{ course.title }}
                                </h3>
                                <p
                                    class="mb-3 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ course.instructor.first_name }}
                                    {{ course.instructor.last_name }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <div
                                        class="flex min-w-0 items-baseline gap-2"
                                    >
                                        <span
                                            class="truncate font-bold text-primary"
                                        >
                                            {{
                                                formatPrice(
                                                    course.discount_price ??
                                                        course.price,
                                                )
                                            }}
                                        </span>
                                        <span
                                            v-if="course.discount_price"
                                            class="shrink-0 text-xs text-gray-400 line-through"
                                        >
                                            {{ formatPrice(course.price) }}
                                        </span>
                                    </div>
                                    <span
                                        class="ml-2 flex shrink-0 items-center gap-1 text-xs text-gray-400"
                                    >
                                        <svg
                                            class="h-3 w-3"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        {{
                                            formatDuration(
                                                course.duration_minutes,
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="courses.links?.length > 3"
                        class="mt-10 flex justify-center gap-1"
                    >
                        <Link
                            v-for="link in courses.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            :class="[
                                'rounded-lg px-3 py-2 text-sm transition-colors',
                                link.active
                                    ? 'bg-primary font-medium text-white'
                                    : link.url
                                      ? 'border border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800'
                                      : 'cursor-not-allowed text-gray-300 dark:text-gray-600',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
