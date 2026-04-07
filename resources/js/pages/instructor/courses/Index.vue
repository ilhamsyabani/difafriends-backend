<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    FilePenLine,
    Trash2,
    Plus,
    Search,
    Star,
    UsersRound,
    BookText,
    Flame,
    Filter,
    SquareChartGantt,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import SortIcon from '@/components/SortIcon.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    categories: Array<{ id: number; name: string }>;
    courses: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            thumbnail: string | null;
            price: string | number;
            discount_price: string | number | null;
            status: string;
            is_featured: boolean;
            enrollments_count: number;
            lectures_count: number;
            reviews_avg_rating: number | null;
            category: { id: number; name: string } | null;
            instructor: {
                id: number;
                first_name: string;
                last_name: string;
                photo: string | null;
            } | null;
        }>;
        links: any[];
        meta: any;
    };
    filters: {
        search?: string;
        sort?: string;
        direction?: string;
        category_id?: string;
        status?: string;
        rating?: string;
    };

    // console.log(categories);
}>();

// Mengatasi error TypeScript pada flash message
const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string },
);

// State untuk input pencarian
const search = ref(props.filters.search ?? '');
const categoryId = ref(props.filters.category_id ?? '');
const status = ref(props.filters.status ?? '');
const rating = ref(props.filters.rating ?? '');

// Watcher dengan Debounce untuk pencarian dan filter
let searchTimeout: ReturnType<typeof setTimeout>;
watch(
    [search, categoryId, status, rating],
    ([newSearch, newCat, newStatus, newRating]) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/instructor/courses',
                {
                    search: newSearch,
                    category_id: newCat,
                    status: newStatus,
                    rating: newRating,
                    sort: props.filters.sort,
                    direction: props.filters.direction,
                },
                {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                },
            );
        }, 300);
    },
);

// Fungsi untuk menangani klik pada header tabel
function sortBy(field: string) {
    let direction = 'asc';

    if (props.filters.sort === field && props.filters.direction === 'asc') {
        direction = 'desc';
    }

    router.get(
        '/instructor/courses',
        {
            search: search.value,
            category_id: categoryId.value,
            status: status.value,
            rating: rating.value,
            sort: field,
            direction: direction,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        },
    );
}

function destroy(id: number) {
    if (confirm('Yakin hapus Kelas ini?')) {
        router.delete(`/instructor/courses/${id}`);
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Kelola Kelas" />

        <div class="max-w-7xl p-6 sm:p-10">
            <!-- Header & Action -->
            <div
                class="mb-8 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
            >
                <!-- Judul & Info Kiri -->
                <div class="shrink-0">
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Kelas Saya
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total
                        {{ courses.meta?.total ?? courses.data.length }} kelas
                        tersedia.
                    </p>
                </div>

                <!-- Area Filter & Tambah Data Kanan -->
                <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64 lg:w-48 xl:w-64">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <Input
                            v-model="search"
                            type="text"
                            autocomplete="off"
                            placeholder="Cari judul kelas..."
                            class="h-10.5 w-full rounded-xl bg-white pl-9 shadow-sm dark:bg-gray-900"
                        />
                    </div>

                    <!-- Filter Kategori -->
                    <div class="relative w-full flex-1 sm:w-auto sm:flex-none">
                        <select
                            v-model="categoryId"
                            class="w-full appearance-none rounded-xl border border-gray-200 bg-white px-4 py-2.5 pr-8 text-sm text-gray-700 shadow-sm transition-colors focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        >
                            <option value="">Semua Kategori</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.name }}
                            </option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                        >
                            <Filter class="h-3.5 w-3.5 text-gray-400" />
                        </div>
                    </div>

                    <!-- Filter Status -->
                    <div class="relative w-full flex-1 sm:w-auto sm:flex-none">
                        <select
                            v-model="status"
                            class="w-full appearance-none rounded-xl border border-gray-200 bg-white px-4 py-2.5 pr-8 text-sm text-gray-700 shadow-sm transition-colors focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        >
                            <option value="">Semua Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Archived</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                        >
                            <Filter class="h-3.5 w-3.5 text-gray-400" />
                        </div>
                    </div>

                    <!-- Filter Rating -->
                    <div class="relative w-full flex-1 sm:w-auto sm:flex-none">
                        <select
                            v-model="rating"
                            class="w-full appearance-none rounded-xl border border-gray-200 bg-white px-4 py-2.5 pr-8 text-sm text-gray-700 shadow-sm transition-colors focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        >
                            <option value="">Semua Rating</option>
                            <option value="4.5">4.5 Ke atas</option>
                            <option value="4.0">4.0 Ke atas</option>
                            <option value="3.0">3.0 Ke atas</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                        >
                            <Star class="h-3.5 w-3.5 text-gray-400" />
                        </div>
                    </div>

                    <!-- Tombol Kelas Baru -->
                    <Link
                        href="/instructor/courses/create"
                        class="inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:outline-none sm:w-auto dark:focus:ring-offset-gray-900"
                    >
                        <Plus class="h-4 w-4" />
                        Kelas Baru
                    </Link>
                </div>
            </div>

            <!-- Flash message dengan desain modern -->
            <div
                v-if="flash.success"
                class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-400"
            >
                {{ flash.success }}
            </div>
            <div
                v-if="flash.error"
                class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400"
            >
                {{ flash.error }}
            </div>

            <!-- Table Container (Senada dengan form) -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead
                            class="border-b border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/50"
                        >
                            <tr>
                                <th
                                    @click="sortBy('title')"
                                    class="group cursor-pointer px-5 py-4 text-left font-semibold text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    <div class="flex items-center gap-2">
                                        Kursus
                                        <SortIcon
                                            :isActive="filters.sort === 'title'"
                                            :direction="
                                                filters.direction ?? 'asc'
                                            "
                                        />
                                    </div>
                                </th>

                                <th
                                    @click="sortBy('price')"
                                    class="group cursor-pointer px-5 py-4 text-left font-semibold text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    <div class="flex items-center gap-2">
                                        Harga
                                        <SortIcon
                                            :isActive="filters.sort === 'price'"
                                            :direction="
                                                filters.direction ?? 'asc'
                                            "
                                        />
                                    </div>
                                </th>

                                <th
                                    class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                                >
                                    Statistik
                                </th>

                                <th
                                    @click="sortBy('status')"
                                    class="group cursor-pointer px-5 py-4 text-left font-semibold text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    <div class="flex items-center gap-2">
                                        Status
                                        <SortIcon
                                            :isActive="
                                                filters.sort === 'status'
                                            "
                                            :direction="
                                                filters.direction ?? 'asc'
                                            "
                                        />
                                    </div>
                                </th>

                                <th class="px-5 py-4 text-right"></th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-gray-100 dark:divide-gray-800"
                        >
                            <tr
                                v-for="course in courses.data"
                                :key="course.id"
                                class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/50"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="h-14 w-20 shrink-0 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
                                        >
                                            <img
                                                v-if="course.thumbnail"
                                                :src="`/storage/${course.thumbnail}`"
                                                :alt="course.title"
                                                class="h-full w-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-full w-full items-center justify-center text-gray-400"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    />
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="flex flex-col">
                                            <span
                                                class="line-clamp-1 font-medium text-gray-900 dark:text-white"
                                                :title="course.title"
                                            >
                                                {{ course.title }}
                                            </span>
                                            <div
                                                class="mt-1 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                <span
                                                    class="rounded bg-purple-50 px-1.5 py-0.5 font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400"
                                                >
                                                    {{
                                                        course.category?.name ??
                                                        'Tanpa Kelas'
                                                    }}
                                                </span>
                                                <span
                                                    v-if="course.instructor"
                                                    class="flex items-center gap-1"
                                                >
                                                    • Oleh
                                                    {{
                                                        course.instructor
                                                            .first_name
                                                    }}
                                                    {{
                                                        course.instructor
                                                            .last_name
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div v-if="Number(course.price) === 0">
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400"
                                        >
                                            Gratis
                                        </span>
                                    </div>
                                    <div v-else class="flex flex-col">
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            Rp
                                            {{
                                                Number(
                                                    course.discount_price ??
                                                        course.price,
                                                ).toLocaleString('id-ID')
                                            }}
                                        </span>
                                        <span
                                            v-if="course.discount_price"
                                            class="text-xs text-gray-400 line-through"
                                        >
                                            Rp
                                            {{
                                                Number(
                                                    course.price,
                                                ).toLocaleString('id-ID')
                                            }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        class="flex flex-col gap-1 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        <div class="flex items-center gap-1.5">
                                            <UsersRound class="h-4 w-4" />
                                            <span
                                                class="font-medium text-gray-700 dark:text-gray-300"
                                                >{{
                                                    course.enrollments_count
                                                }}</span
                                            >
                                            siswa
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <Star class="h-4 w-4" />
                                            <span
                                                class="font-medium text-gray-700 dark:text-gray-300"
                                                >{{
                                                    course.reviews_avg_rating ??
                                                    '0.0'
                                                }}</span
                                            >
                                            rating
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <BookText class="h-4 w-4" />
                                            <span
                                                class="font-medium text-gray-700 dark:text-gray-300"
                                                >{{
                                                    course.lectures_count
                                                }}</span
                                            >
                                            materi
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        class="flex flex-col items-start gap-2"
                                    >
                                        <span
                                            :class="[
                                                'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                                course.status === 'published'
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                    : course.status === 'draft'
                                                      ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-500'
                                                      : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                            ]"
                                        >
                                            {{
                                                course.status
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                course.status.slice(1)
                                            }}
                                        </span>

                                        <span
                                            v-if="course.is_featured"
                                            class="items-top inline-flex gap-1 text-xs font-semibold text-orange-500"
                                        >
                                            <Flame class="h-3 w-3" />
                                            Unggulan
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-center gap-3"
                                    >
                                        <Link
                                            :href="`/instructor/courses/${course.id}/manage`"
                                            class="text-gray-400 transition-colors hover:text-purple-600 dark:hover:text-purple-400"
                                            title="Manage Kursus"
                                        >
                                            <span class="sr-only">Manage</span>
                                            <SquareChartGantt class="h-4 w-4" />
                                        </Link>
                                        <Link
                                            :href="`/instructor/courses/${course.id}/edit`"
                                            class="text-gray-400 transition-colors hover:text-purple-600 dark:hover:text-purple-400"
                                            title="Edit Kursus"
                                        >
                                            <span class="sr-only">Edit</span>
                                            <FilePenLine class="h-4 w-4" />
                                        </Link>

                                        <button
                                            @click="destroy(course.id)"
                                            class="text-gray-400 transition-colors hover:text-red-500 dark:hover:text-red-400"
                                            title="Hapus Kursus"
                                        >
                                            <span class="sr-only">Hapus</span>
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="courses.data.length === 0">
                                <td
                                    colspan="5"
                                    class="px-5 py-16 text-center text-sm text-gray-400 dark:text-gray-500"
                                >
                                    Belum ada kursus atau pencarian tidak
                                    ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="courses.links?.length > 3"
                class="mt-8 flex justify-end gap-1.5"
            >
                <Link
                    v-for="link in courses.links"
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
    </AppLayout>
</template>
