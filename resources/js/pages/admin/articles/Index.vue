<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Search,
    Plus,
    Eye,
    FilePenLine,
    Trash2,
    Image as ImageIcon,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    articles: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            thumbnail: string | null;
            status: string;
            created_at: string;
            author: {
                first_name: string;
                last_name: string;
                photo: string | null;
            };
        }>;
        links: any[];
        meta: any;
    };
    filters: {
        search?: string;
        status?: string;
    };
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;
watch([search, status], ([newSearch, newStatus]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            '/admin/articles',
            {
                search: newSearch || undefined,
                status: newStatus === 'all' ? undefined : newStatus,
            },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 300);
});

function destroy(id: number) {
    if (confirm('Yakin ingin menghapus artikel ini secara permanen?')) {
        router.delete(`/admin/articles/${id}`);
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Artikel" />

        <div class="max-w-7xl p-6 sm:p-10">
            <!-- Header & Action -->
            <div
                class="mb-8 flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Manajemen Artikel
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total
                        {{ articles.meta?.total ?? articles.data.length }}
                        artikel di platform.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        href="/admin/articles/create"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                    >
                        <Plus class="h-4 w-4" />
                        Tulis Artikel Baru
                    </Link>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative w-full sm:w-72">
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                    >
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Cari judul artikel..."
                        class="rounded-xl pl-9 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    />
                </div>

                <Select v-model="status">
                    <SelectTrigger
                        class="w-full rounded-xl border-gray-200 bg-white shadow-sm sm:w-48 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >
                        <SelectValue placeholder="Semua Status" />
                    </SelectTrigger>
                    <SelectContent
                        class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                    >
                        <SelectGroup>
                            <SelectItem value="all">Semua Status</SelectItem>
                            <SelectItem value="published"
                                >Tayang (Published)</SelectItem
                            >
                            <SelectItem value="draft">Draft</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <button
                    v-if="search || (status && status !== 'all')"
                    @click="
                        () => {
                            search = '';
                            status = 'all';
                        }
                    "
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    Reset Filter
                </button>
            </div>

            <!-- Table Container -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="border-b border-gray-100 bg-gray-50/50 text-gray-700 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-300"
                        >
                            <tr>
                                <th class="px-5 py-4 font-semibold">Artikel</th>
                                <th class="px-5 py-4 font-semibold">Penulis</th>
                                <th class="px-5 py-4 text-center font-semibold">
                                    Status
                                </th>
                                <th class="px-5 py-4 text-center font-semibold">
                                    Tanggal
                                </th>
                                <th class="px-5 py-4 text-right font-semibold">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 dark:divide-gray-800"
                        >
                            <tr
                                v-for="article in articles.data"
                                :key="article.id"
                                class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/50"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="flex h-12 w-16 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
                                        >
                                            <img
                                                v-if="article.thumbnail"
                                                :src="`/storage/${article.thumbnail}`"
                                                class="h-full w-full object-cover"
                                            />
                                            <ImageIcon
                                                v-else
                                                class="h-5 w-5 text-gray-400"
                                            />
                                        </div>
                                        <div>
                                            <div
                                                class="line-clamp-1 font-bold text-gray-900 dark:text-white"
                                                :title="article.title"
                                            >
                                                {{ article.title }}
                                            </div>
                                            <div
                                                class="mt-0.5 text-xs text-gray-500"
                                            >
                                                /articles/{{ article.slug }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div
                                        class="font-medium text-gray-900 dark:text-gray-200"
                                    >
                                        {{ article.author.first_name }}
                                        {{ article.author.last_name }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium tracking-wider uppercase',
                                            article.status === 'published'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                        ]"
                                    >
                                        {{ article.status }}
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-4 text-center text-gray-500 dark:text-gray-400"
                                >
                                    {{
                                        new Date(
                                            article.created_at,
                                        ).toLocaleDateString('id-ID', {
                                            day: 'numeric',
                                            month: 'short',
                                            year: 'numeric',
                                        })
                                    }}
                                </td>
                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <a
                                            :href="`/articles/${article.slug}`"
                                            target="_blank"
                                            class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400"
                                            title="Lihat Artikel"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </a>
                                        <Link
                                            :href="`/admin/articles/${article.id}/edit`"
                                            class="p-2 text-gray-400 hover:text-purple-600 dark:hover:text-purple-400"
                                            title="Edit Artikel"
                                        >
                                            <FilePenLine class="h-4 w-4" />
                                        </Link>
                                        <button
                                            @click="destroy(article.id)"
                                            class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400"
                                            title="Hapus Artikel"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="articles.data.length === 0">
                                <td
                                    colspan="5"
                                    class="px-5 py-12 text-center text-sm text-gray-400"
                                >
                                    Belum ada artikel yang ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Wrapper (Gunakan komponen pagination mu disini) -->
            <div
                v-if="articles.links?.length > 3"
                class="mt-8 flex justify-end gap-1.5"
            >
                <!-- eslint-disable-next-line vue/no-v-text-v-html-on-component -->
                <Link
                    v-for="link in articles.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    :class="[
                        'rounded-xl px-3.5 py-2 text-sm font-medium transition-colors',
                        link.active
                            ? 'bg-purple-600 text-white shadow-sm'
                            : link.url
                              ? 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700'
                              : 'cursor-not-allowed border border-transparent text-gray-400 dark:text-gray-600',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
