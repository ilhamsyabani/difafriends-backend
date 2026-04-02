<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';

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

const search   = ref(props.filters.search ?? '');
const category = ref(props.filters.category ?? '');
const price    = ref(props.filters.price ?? '');
const sort     = ref(props.filters.sort ?? 'latest');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 400);
});
watch([category, price, sort], () => applyFilters());

function applyFilters() {
    router.get('/courses', {
        search:   search.value   || undefined,
        category: category.value || undefined,
        price:    price.value    || undefined,
        sort:     sort.value !== 'latest' ? sort.value : undefined,
    }, { preserveState: true, replace: true });
}

function formatPrice(p: number): string {
    if (p === 0) return 'Gratis';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2">Katalog Kelas</h1>
                <p class="text-gray-500 dark:text-gray-400">
                    {{ courses.meta?.total ?? courses.data.length }} kelas tersedia
                </p>
            </div>

            <!-- ✅ Layout utama: sidebar 1/4 + konten 3/4 -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                <!-- ── SIDEBAR ─────────────────────────────── -->
                <aside class="lg:col-span-1 space-y-6">

                    <div>
                        <label class="block text-sm font-medium mb-2">Cari Kelas</label>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Nama kelas..."
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Kategori</label>
                        <div class="space-y-1">
                            <button
                                @click="category = ''"
                                :class="['w-full text-left text-sm px-3 py-2 rounded-lg transition-colors',
                                    category === ''
                                        ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 font-medium'
                                        : 'hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-400']"
                            >Semua Kategori</button>

                            <template v-for="cat in categories" :key="cat.id">
                                <button
                                    @click="category = cat.slug"
                                    :class="['w-full text-left text-sm px-3 py-2 rounded-lg transition-colors font-medium',
                                        category === cat.slug
                                            ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300'
                                            : 'hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300']"
                                >{{ cat.name }}</button>
                                <button
                                    v-for="child in cat.children"
                                    :key="child.id"
                                    @click="category = child.slug"
                                    :class="['w-full text-left text-sm px-3 py-2 pl-6 rounded-lg transition-colors',
                                        category === child.slug
                                            ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'
                                            : 'hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400']"
                                >{{ child.name }}</button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Harga</label>
                        <div class="space-y-1">
                            <button
                                v-for="opt in [
                                    { label: 'Semua Harga', value: '' },
                                    { label: 'Gratis', value: 'free' },
                                    { label: 'Berbayar', value: 'paid' },
                                ]"
                                :key="opt.value"
                                @click="price = opt.value"
                                :class="['w-full text-left text-sm px-3 py-2 rounded-lg transition-colors',
                                    price === opt.value
                                        ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 font-medium'
                                        : 'hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-400']"
                            >{{ opt.label }}</button>
                        </div>
                    </div>

                    <button
                        v-if="search || category || price || sort !== 'latest'"
                        @click="search = ''; category = ''; price = ''; sort = 'latest'"
                        class="w-full text-sm text-red-500 hover:text-red-600 py-2"
                    >Reset Filter</button>

                </aside>

                <!-- ── KONTEN ──────────────────────────────── -->
                <div class="lg:col-span-3 min-w-0">

                    <!-- Sort Bar -->
                    <div class="flex items-center justify-between mb-6 gap-4">
                        <p class="text-sm text-gray-500 shrink-0">
                            Menampilkan {{ courses.data.length }} kelas
                        </p>
                        <select
                            v-model="sort"
                            class="shrink-0 text-sm border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        >
                            <option value="latest">Terbaru</option>
                            <option value="popular">Terpopuler</option>
                            <option value="price_low">Harga Terendah</option>
                            <option value="price_high">Harga Tertinggi</option>
                        </select>
                    </div>

                    <!-- Empty State -->
                    <div v-if="courses.data.length === 0" class="text-center py-20 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="font-medium">Kelas tidak ditemukan</p>
                        <p class="text-sm mt-1">Coba ubah filter pencarian kamu</p>
                    </div>

                    <!-- ✅ Grid course — 2 kolom cukup untuk area 3/4 -->
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <Link
                            v-for="course in courses.data"
                            :key="course.id"
                            :href="`/courses/${course.slug}`"
                            class="group rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden hover:shadow-lg hover:border-purple-200 dark:hover:border-purple-800 transition-all"
                        >
                            <div class="aspect-video bg-purple-50 dark:bg-purple-900/20 relative overflow-hidden">
                                <img
                                    v-if="course.thumbnail"
                                    :src="`/storage/${course.thumbnail}`"
                                    :alt="course.title"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <svg class="w-10 h-10 text-purple-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                    </svg>
                                </div>
                                <span class="absolute top-3 left-3 text-xs bg-white/90 dark:bg-gray-900/90 text-purple-700 dark:text-purple-300 font-medium px-2 py-1 rounded-full">
                                    {{ course.category.name }}
                                </span>
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-sm mb-1 line-clamp-2 group-hover:text-purple-600 transition-colors">
                                    {{ course.title }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    {{ course.instructor.first_name }} {{ course.instructor.last_name }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-baseline gap-2 min-w-0">
                                        <span class="font-bold text-purple-600 truncate">
                                            {{ formatPrice(course.discount_price ?? course.price) }}
                                        </span>
                                        <span v-if="course.discount_price" class="text-xs text-gray-400 line-through shrink-0">
                                            {{ formatPrice(course.price) }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-400 flex items-center gap-1 shrink-0 ml-2">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ formatDuration(course.duration_minutes) }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="courses.links?.length > 3" class="flex justify-center gap-1 mt-10">
                        <Link
                            v-for="link in courses.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            :class="['px-3 py-2 text-sm rounded-lg transition-colors',
                                link.active
                                    ? 'bg-purple-600 text-white font-medium'
                                    : link.url
                                        ? 'border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800'
                                        : 'text-gray-300 dark:text-gray-600 cursor-not-allowed']"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
