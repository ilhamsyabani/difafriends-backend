<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useFormatters } from '@/composables/useFormatters';
import GuestLayout from '@/layouts/GuestLayout.vue';

const { assetUrl } = useFormatters();

const props = defineProps<{
    articles: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            thumbnail: string | null;
            content: string;
            created_at: string;
            author: {
                first_name: string;
                last_name: string;
                photo: string | null;
            };
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
        meta: { total: number; current_page: number; last_page: number };
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search ?? '');

// Fitur pencarian otomatis (Debounce 400ms agar tidak spam request)
let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            '/articles',
            { search: value || undefined },
            { preserveState: true, replace: true },
        );
    }, 400);
});

// Format Tanggal (Contoh: 12 April 2026)
function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

// Menghapus tag HTML dari konten Quill untuk dijadikan cuplikan (Excerpt)
function stripHtml(html: string) {
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;

    return tmp.textContent || tmp.innerText || '';
}
</script>

<template>
    <GuestLayout>
        <Head title="Blog & Artikel Edukasi — DifaFriends" />

        <div class="min-h-screen bg-white dark:bg-gray-950">
            <!-- ── HERO SECTION BLOG ───────────────────────────────── -->
            <section
                class="relative bg-purple-50/50 px-4 py-20 text-center sm:px-6 lg:px-8 lg:py-28 dark:bg-gray-900/50"
            >
                <div class="mx-auto max-w-3xl">
                    <h1
                        class="mb-6 text-4xl font-extrabold tracking-tight text-gray-900 md:text-5xl dark:text-white"
                    >
                        Jelajahi Wawasan Seputar <br />
                        <span class="text-primary dark:text-purple-400"
                            >Pendidikan Inklusif</span
                        >
                    </h1>
                    <p class="mb-10 text-lg text-gray-600 dark:text-gray-400">
                        Temukan artikel, panduan, dan tips terbaru dari para
                        ahli untuk mendukung perkembangan anak berkebutuhan
                        khusus.
                    </p>

                    <!-- Search Bar -->
                    <div
                        class="relative mx-auto flex max-w-xl items-center rounded-2xl shadow-sm"
                    >
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5"
                        >
                            <Search class="h-5 w-5 text-gray-400" />
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari topik artikel..."
                            class="w-full rounded-2xl border-transparent bg-white py-4 pr-4 pl-12 text-base text-gray-900 placeholder-gray-500 shadow-[0_2px_15px_-3px_rgba(6,81,237,0.08)] transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                        />
                    </div>
                </div>
            </section>

            <!-- ── DAFTAR ARTIKEL ──────────────────────────────────── -->
            <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div
                    class="mb-8 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800"
                >
                    <h2
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ search ? 'Hasil Pencarian' : 'Artikel Terbaru' }}
                    </h2>
                    <p
                        class="text-sm font-medium text-gray-500 dark:text-gray-400"
                    >
                        {{ articles.meta?.total ?? articles.data.length }}
                        Artikel
                    </p>
                </div>

                <!-- Empty State -->
                <div
                    v-if="articles.data.length === 0"
                    class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-gray-200 bg-gray-50/50 py-20 dark:border-gray-800 dark:bg-gray-900/50"
                >
                    <div
                        class="mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800"
                    >
                        <svg
                            class="h-8 w-8 text-gray-400 dark:text-gray-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                            />
                        </svg>
                    </div>
                    <h3
                        class="text-lg font-bold text-gray-900 dark:text-white"
                    >
                        Artikel tidak ditemukan
                    </h3>
                    <p
                        class="mt-2 max-w-sm text-center text-sm text-gray-500 dark:text-gray-400"
                    >
                        Coba gunakan kata kunci pencarian yang lain.
                    </p>
                    <button
                        v-if="search"
                        @click="search = ''"
                        class="mt-6 text-sm font-medium text-primary hover:text-primary-hover dark:text-purple-400"
                    >
                        Reset Pencarian
                    </button>
                </div>

                <!-- Grid Artikel -->
                <div v-else class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="article in articles.data"
                        :key="article.id"
                        :href="`/articles/${article.slug}`"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-purple-200 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900 dark:hover:border-purple-800"
                    >
                        <!-- Thumbnail -->
                        <div
                            class="relative aspect-[16/10] w-full overflow-hidden bg-gray-100 dark:bg-gray-800"
                        >
                            <img
                                v-if="article.thumbnail"
                                :src="assetUrl(article.thumbnail)"
                                :alt="article.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center bg-purple-50 dark:bg-purple-900/20"
                            >
                                <span
                                    class="text-2xl font-bold text-purple-200 dark:text-purple-900/50"
                                    >DifaFriends</span
                                >
                            </div>
                            <!-- Badge Kategori (Bisa diganti dinamis nanti jika ada tabel relasi kategory artikel) -->
                            <span
                                class="text-primary-hover absolute top-4 left-4 rounded-full bg-white/90 px-3 py-1.5 text-xs font-bold shadow-sm backdrop-blur dark:bg-gray-900/90 dark:text-purple-300"
                            >
                                Pendidikan
                            </span>
                        </div>

                        <!-- Konten -->
                        <div class="flex flex-1 flex-col p-6">
                            <h3
                                class="mb-3 line-clamp-2 text-xl leading-snug font-bold text-gray-900 transition-colors group-hover:text-primary dark:text-white dark:group-hover:text-purple-400"
                            >
                                {{ article.title }}
                            </h3>
                            <p
                                class="mb-6 line-clamp-3 flex-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                            >
                                {{ stripHtml(article.content) }}
                            </p>

                            <!-- Meta Footer -->
                            <div
                                class="mt-auto flex items-center justify-between border-t border-gray-100 pt-5 dark:border-gray-800"
                            >
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="article.author.photo"
                                        :src="assetUrl(article.author.photo)"
                                        class="h-8 w-8 rounded-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-xs font-bold text-primary dark:bg-gray-800"
                                    >
                                        {{
                                            article.author.first_name.charAt(0)
                                        }}
                                    </div>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ article.author.first_name }}
                                    </span>
                                </div>
                                <span
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatDate(article.created_at) }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Pagination -->
                <div
                    v-if="articles.links?.length > 3"
                    class="mt-16 flex flex-wrap justify-center gap-2"
                >
                    <Link
                        v-for="link in articles.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'rounded-xl px-4 py-2.5 text-sm font-medium shadow-sm transition-all',
                            link.active
                                ? 'bg-primary text-white hover:bg-purple-700'
                                : link.url
                                  ? 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'
                                  : 'cursor-not-allowed border border-gray-100 bg-gray-50 text-gray-400 shadow-none dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-600',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </section>
        </div>
    </GuestLayout>
</template>
