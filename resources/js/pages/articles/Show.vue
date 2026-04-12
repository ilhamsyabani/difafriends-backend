<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { useFormatters } from '@/composables/useFormatters';

const { assetUrl } = useFormatters();

defineProps<{
    article: {
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
            bio: string | null;
        };
    };
    // Artikel terbaru untuk direkomendasikan di Sidebar
    latestArticles: Array<{
        id: number;
        title: string;
        slug: string;
        thumbnail: string | null;
        created_at: string;
    }>;
}>();

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function getInitials(first: string, last: string) {
    return (first.charAt(0) + last.charAt(0)).toUpperCase();
}
</script>

<template>
    <GuestLayout>
        <Head :title="`${article.title} — DifaFriends Blog`" />

        <div class="min-h-screen bg-white py-12 lg:py-20 dark:bg-gray-950">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                    <!-- ── KONTEN UTAMA ARTIKEL ─────────────────────────────── -->
                    <article class="lg:col-span-8">
                        <!-- Header Artikel -->
                        <header class="mb-10">
                            <div
                                class="mb-4 flex items-center gap-2 text-sm font-medium text-purple-600 dark:text-purple-400"
                            >
                                <span
                                    class="rounded-full bg-purple-100 px-3 py-1 dark:bg-purple-900/30"
                                    >Pendidikan Inklusif</span
                                >
                                <span class="text-gray-400 dark:text-gray-600"
                                    >&bull;</span
                                >
                                <span
                                    class="text-gray-500 dark:text-gray-400"
                                    >{{ formatDate(article.created_at) }}</span
                                >
                            </div>

                            <h1
                                class="mb-6 text-3xl leading-tight font-extrabold text-gray-900 md:text-4xl lg:text-5xl dark:text-white"
                            >
                                {{ article.title }}
                            </h1>

                            <div
                                class="flex items-center gap-4 border-t border-b border-gray-100 py-4 dark:border-gray-800"
                            >
                                <img
                                    v-if="article.author.photo"
                                    :src="assetUrl(article.author.photo)"
                                    class="h-12 w-12 rounded-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 text-lg font-bold text-purple-600 dark:bg-gray-800"
                                >
                                    {{
                                        getInitials(
                                            article.author.first_name,
                                            article.author.last_name,
                                        )
                                    }}
                                </div>
                                <div>
                                    <p
                                        class="font-bold text-gray-900 dark:text-white"
                                    >
                                        Ditulis oleh
                                        {{ article.author.first_name }}
                                        {{ article.author.last_name }}
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Tim DifaFriends
                                    </p>
                                </div>
                            </div>
                        </header>

                        <!-- Thumbnail Utama -->
                        <div
                            class="mb-10 overflow-hidden rounded-3xl bg-gray-100 dark:bg-gray-800"
                        >
                            <img
                                v-if="article.thumbnail"
                                :src="assetUrl(article.thumbnail)"
                                :alt="article.title"
                                class="aspect-video w-full object-cover"
                            />
                            <div
                                v-else
                                class="flex aspect-video w-full items-center justify-center bg-purple-50 dark:bg-purple-900/20"
                            >
                                <span
                                    class="text-4xl font-bold text-purple-200 dark:text-purple-900/50"
                                    >DifaFriends</span
                                >
                            </div>
                        </div>

                        <!-- Isi Konten Artikel (v-html) -->
                        <div
                            class="article-content text-gray-700 dark:text-gray-300"
                            v-html="article.content"
                        ></div>

                        <!-- Footer Artikel (Share dll) -->
                        <div
                            class="mt-12 flex items-center justify-between border-t border-gray-100 pt-6 dark:border-gray-800"
                        >
                            <p
                                class="font-semibold text-gray-900 dark:text-white"
                            >
                                Bagikan artikel ini:
                            </p>
                            <div class="flex gap-3">
                                <button
                                    class="rounded-full bg-blue-50 p-3 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40"
                                >
                                    <svg
                                        class="h-5 w-5 fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                                        />
                                    </svg>
                                </button>
                                <button
                                    class="rounded-full bg-green-50 p-3 text-green-600 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40"
                                >
                                    <svg
                                        class="h-5 w-5 fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.876 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </article>

                    <!-- ── SIDEBAR (Artikel Terkait / Terbaru) ────────────────────── -->
                    <aside class="lg:col-span-4 lg:pl-8">
                        <div
                            class="sticky top-28 rounded-3xl border border-gray-100 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-900/50"
                        >
                            <h3
                                class="mb-6 border-b border-gray-200 pb-3 text-xl font-bold text-gray-900 dark:border-gray-800 dark:text-white"
                            >
                                Artikel Terbaru
                            </h3>
                            <div class="flex flex-col gap-6">
                                <Link
                                    v-for="recent in latestArticles"
                                    :key="recent.id"
                                    :href="`/articles/${recent.slug}`"
                                    class="group flex gap-4"
                                >
                                    <img
                                        v-if="recent.thumbnail"
                                        :src="assetUrl(recent.thumbnail)"
                                        class="h-20 w-24 shrink-0 rounded-xl bg-gray-200 object-cover"
                                    />
                                    <div
                                        v-else
                                        class="h-20 w-24 shrink-0 rounded-xl bg-purple-100"
                                    ></div>

                                    <div
                                        class="flex flex-col justify-between py-1"
                                    >
                                        <h4
                                            class="line-clamp-2 text-sm leading-tight font-bold text-gray-900 transition-colors group-hover:text-purple-600 dark:text-white dark:group-hover:text-purple-400"
                                        >
                                            {{ recent.title }}
                                        </h4>
                                        <span class="text-xs text-gray-500">{{
                                            formatDate(recent.created_at)
                                        }}</span>
                                    </div>
                                </Link>
                            </div>

                            <Link
                                href="/articles"
                                class="mt-8 block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-center text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Lihat Semua Artikel
                            </Link>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style>
/* CSS Kustom untuk konten yang di-generate dari Quill Editor (Rich Text) */
.article-content {
    font-size: 1.125rem; /* 18px untuk baca yang nyaman */
    line-height: 1.8;
}
.article-content p {
    margin-bottom: 1.5rem;
}
.article-content h2 {
    font-size: 1.875rem;
    font-weight: 800;
    margin-top: 2.5rem;
    margin-bottom: 1.25rem;
    color: #111827; /* gray-900 */
}
.dark .article-content h2,
.dark .article-content h3 {
    color: #f9fafb; /* gray-50 */
}
.article-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #1f2937;
}
.article-content ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}
.article-content ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}
.article-content li {
    margin-bottom: 0.5rem;
}
.article-content a {
    color: #7c3aed; /* purple-600 */
    text-decoration: underline;
}
.article-content a:hover {
    color: #6d28d9; /* purple-700 */
}
.dark .article-content a {
    color: #a78bfa; /* purple-400 */
}
.article-content img {
    border-radius: 1rem;
    margin-top: 2rem;
    margin-bottom: 2rem;
    max-width: 100%;
    height: auto;
}
.article-content blockquote {
    border-left: 4px solid #7c3aed;
    padding-left: 1rem;
    font-style: italic;
    color: #4b5563; /* gray-600 */
    background: #f9fafb; /* gray-50 */
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}
.dark .article-content blockquote {
    background: #1f2937;
    color: #d1d5db;
}
</style>
