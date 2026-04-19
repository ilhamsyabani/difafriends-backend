<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { FilePenLine, Trash2, Plus, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import SortIcon from '@/components/SortIcon.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            first_name: string;
            last_name: string;
            email: string;
            photo: string | null;
            birth_date: string;
            gender: string;
            address: string;
            city: string;
            country: string;
            is_active: boolean;
            role: string;
        }>;
        links: any[];
        meta: any;
    };
    filters: {
        search?: string;
        sort?: string;
        direction?: string;
    };
}>();

// Mengatasi error TypeScript pada flash message
const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string },
);

// State untuk input pencarian
const search = ref(props.filters.search ?? '');

// Watcher dengan Debounce untuk pencarian
let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            '/admin/users',
            {
                search: value,
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
});

// Fungsi untuk menangani klik pada header tabel
function sortBy(field: string) {
    let direction = 'asc';

    if (props.filters.sort === field && props.filters.direction === 'asc') {
        direction = 'desc';
    }

    router.get(
        '/admin/users',
        {
            search: search.value,
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
    if (confirm('Yakin ingin menghapus pengguna ini?')) {
        router.delete(`/admin/users/${id}`);
    }
}

// Helper untuk menampilkan inisial jika foto tidak ada
function getInitials(firstName: string, lastName: string) {
    return `${firstName?.charAt(0) ?? ''}${lastName?.charAt(0) ?? ''}`.toUpperCase();
}

// Helper untuk mengambil nama role
// function getRoleName(role: any) {
//     if (!role) return '—';
//     return typeof role === 'object' ? role.name : role;
// }
</script>

<template>
    <AppLayout>
        <Head title="Kelola Pengguna" />

        <div class="max-w-7xl p-6 sm:p-10">
            <!-- Header & Action -->
            <div
                class="mb-8 flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Kelola Pengguna
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total
                        {{ users.meta?.total ?? users.data.length }} pengguna
                        terdaftar.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <!-- Input Filter/Search dengan Ikon Kaca Pembesar -->
                    <div class="relative w-full sm:w-64">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <Input
                            v-model="search"
                            type="text"
                            autocomplete="off"
                            placeholder="Cari nama atau email..."
                            class="rounded-xl pl-9 shadow-sm"
                        />
                    </div>

                    <Link
                        href="/admin/users/create"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-orange-500 hover:shadow focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                    >
                        <Plus class="h-4 w-4" />
                        Pengguna Baru
                    </Link>
                </div>
            </div>

            <!-- Flash message -->
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

            <!-- Table Container -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead
                            class="border-b border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/50"
                        >
                            <tr>
                                <!-- Kolom Pengguna -->
                                <th
                                    @click="sortBy('first_name')"
                                    class="group cursor-pointer px-5 py-4 text-left font-semibold text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    <div class="flex items-center gap-2">
                                        Pengguna
                                        <SortIcon
                                            :isActive="
                                                filters.sort === 'first_name'
                                            "
                                            :direction="
                                                filters.direction ?? 'asc'
                                            "
                                        />
                                    </div>
                                </th>

                                <!-- Kolom Kontak -->
                                <th
                                    @click="sortBy('email')"
                                    class="group cursor-pointer px-5 py-4 text-left font-semibold text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    <div class="flex items-center gap-2">
                                        Kontak
                                        <SortIcon
                                            :isActive="filters.sort === 'email'"
                                            :direction="
                                                filters.direction ?? 'asc'
                                            "
                                        />
                                    </div>
                                </th>

                                <!-- Kolom Peran/Role -->
                                <th
                                    class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                                >
                                    Peran
                                </th>

                                <!-- Kolom Status -->
                                <th
                                    @click="sortBy('is_active')"
                                    class="group cursor-pointer px-5 py-4 text-left font-semibold text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                                >
                                    <div class="flex items-center gap-2">
                                        Status
                                        <SortIcon
                                            :isActive="
                                                filters.sort === 'is_active'
                                            "
                                            :direction="
                                                filters.direction ?? 'asc'
                                            "
                                        />
                                    </div>
                                </th>

                                <th class="px-5 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 dark:divide-gray-800"
                        >
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/50"
                            >
                                <!-- Cell: Pengguna (Avatar + Nama) -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar -->
                                        <img
                                            v-if="user.photo"
                                            :src="user.photo"
                                            alt="Avatar"
                                            class="h-10 w-10 shrink-0 rounded-full border border-gray-200 object-cover dark:border-gray-700"
                                        />
                                        <!-- Fallback Inisial Jika Tidak Ada Foto -->
                                        <div
                                            v-else
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-purple-100 text-sm font-bold text-primary-hover dark:bg-purple-900/50 dark:text-purple-400"
                                        >
                                            {{
                                                getInitials(
                                                    user.first_name,
                                                    user.last_name,
                                                )
                                            }}
                                        </div>

                                        <!-- Nama & Lokasi -->
                                        <div>
                                            <div
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ user.first_name }}
                                                {{ user.last_name }}
                                            </div>
                                            <div
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {{
                                                    user.city
                                                        ? `${user.city}, `
                                                        : ''
                                                }}{{ user.country ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Cell: Kontak -->
                                <td
                                    class="px-5 py-4 text-gray-500 dark:text-gray-400"
                                >
                                    {{ user.email }}
                                </td>

                                <!-- Cell: Peran -->
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300"
                                    >
                                        {{ user.role }}
                                    </span>
                                </td>

                                <!-- Cell: Status -->
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                            user.is_active
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        ]"
                                    >
                                        {{
                                            user.is_active
                                                ? 'Aktif'
                                                : 'Tidak Aktif'
                                        }}
                                    </span>
                                </td>

                                <!-- Cell: Aksi -->
                                <td class="px-5 py-4">
                                    <div
                                        class="flex items-center justify-end gap-3"
                                    >
                                        <Link
                                            :href="`/admin/users/${user.id}/edit`"
                                            class="text-gray-400 transition-colors hover:text-primary dark:hover:text-primary"
                                            title="Edit Pengguna"
                                        >
                                            <span class="sr-only">Edit</span>
                                            <FilePenLine class="h-4 w-4" />
                                        </Link>
                                        <button
                                            @click="destroy(user.id)"
                                            class="text-gray-400 transition-colors hover:text-red-500 dark:hover:text-red-400"
                                            title="Hapus Pengguna"
                                        >
                                            <span class="sr-only">Hapus</span>
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="users.data.length === 0">
                                <td
                                    colspan="5"
                                    class="px-5 py-16 text-center text-sm text-gray-400 dark:text-gray-500"
                                >
                                    Belum ada pengguna atau pencarian tidak
                                    ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="users.links?.length > 3"
                class="mt-8 flex justify-end gap-1.5"
            >
                <Link
                    v-for="link in users.links"
                    :key="link.label"
                    :href="link.url ?? '#'"
                    :class="[
                        'rounded-xl px-3.5 py-2 text-sm font-medium transition-colors',
                        link.active
                            ? 'bg-primary text-white shadow-sm'
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
