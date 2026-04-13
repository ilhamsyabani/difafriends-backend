<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Download, Eye } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
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
    orders: {
        data: Array<{
            id: number;
            invoice_number: string;
            item_name: string;
            orderable_type: string;
            final_amount: number;
            status: string;
            created_at: string;
            user: {
                first_name: string;
                last_name: string;
                email: string;
                photo: string | null;
            };
        }>;
        links: any[];
        meta: any;
    };
    filters: {
        search?: string;
        status?: string;
        type?: string;
    };
}>();

// State untuk filter
const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const type = ref(props.filters.type ?? '');

// Watcher dengan Debounce untuk auto-submit filter
let filterTimeout: ReturnType<typeof setTimeout>;
watch([search, status, type], ([newSearch, newStatus, newType]) => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        router.get(
            '/admin/orders',
            { search: newSearch, status: newStatus, type: newType },
            { preserveState: true, replace: true, preserveScroll: true },
        );
    }, 300);
});

// Bikin URL Export dinamis
const exportUrl = computed(() => {
    const params = new URLSearchParams();

    if (search.value) {
params.append('search', search.value);
}

    if (status.value) {
params.append('status', status.value);
}

    if (type.value) {
params.append('type', type.value);
}

    return `/admin/orders/export?${params.toString()}`;
});

// Helper format Rupiah
const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
};

// Helper warna badge status untuk Light & Dark Mode
const statusColor = (val: string) => {
    const colors: Record<string, string> = {
        pending:
            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
        paid: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        expired: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        cancelled:
            'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        refunded:
            'bg-purple-100 text-primary-hover dark:bg-purple-900/30 dark:text-purple-400',
    };

    return (
        colors[val] ||
        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
    );
};
</script>

<template>
    <AppLayout>
        <Head title="Manajemen Transaksi" />

        <div class="max-w-7xl p-6 sm:p-10">
            <!-- Header & Action -->
            <div
                class="mb-8 flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
            >
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Daftar Transaksi
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total
                        {{ orders.meta?.total ?? orders.data.length }} transaksi
                        tercatat.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a
                        :href="exportUrl"
                        target="_blank"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                    >
                        <Download class="h-4 w-4" />
                        Export CSV
                    </a>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                <!-- Input Search -->
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
                        placeholder="Cari Invoice / User..."
                        class="rounded-xl pl-9 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    />
                </div>

                <!-- Status Filter -->
                <!-- Filter Status -->
                <Select v-model="status">
                    <SelectTrigger
                        class="mt-1 w-full rounded-xl border-transparent bg-gray-50 px-4 py-2.5 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 sm:w-48 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                    >
                        <SelectValue placeholder="Semua Status" />
                    </SelectTrigger>
                    <SelectContent
                        class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                    >
                        <SelectGroup>
                            <SelectItem value="all">Semua Status</SelectItem>
                            <SelectItem value="pending">Pending</SelectItem>
                            <SelectItem value="paid">Paid</SelectItem>
                            <SelectItem value="expired">Expired</SelectItem>
                            <SelectItem value="cancelled">Cancelled</SelectItem>
                            <SelectItem value="refunded">Refunded</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <!-- Filter Tipe -->
                <Select v-model="type">
                    <SelectTrigger
                        class="mt-1 w-full rounded-xl border-transparent bg-gray-50 px-4 py-2.5 text-sm transition-all focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 sm:w-48 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                    >
                        <SelectValue placeholder="Semua Tipe" />
                    </SelectTrigger>
                    <SelectContent
                        class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                    >
                        <SelectGroup>
                            <SelectItem value="all">Semua Tipe</SelectItem>
                            <SelectItem value="course">Kelas Online</SelectItem>
                            <SelectItem value="booking"
                                >Sesi Pendamping</SelectItem
                            >
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <button
                    v-if="search || status || type"
                    @click="
                        () => {
                            search = '';
                            status = '';
                            type = '';
                        }
                    "
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    Reset Filter
                </button>
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
                                <th
                                    class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                                >
                                    Invoice & Waktu
                                </th>
                                <th
                                    class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                                >
                                    Pembeli
                                </th>
                                <th
                                    class="px-5 py-4 text-left font-semibold text-gray-700 dark:text-gray-300"
                                >
                                    Item Pembelian
                                </th>
                                <th
                                    class="px-5 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                                >
                                    Total (Rp)
                                </th>
                                <th
                                    class="px-5 py-4 text-center font-semibold text-gray-700 dark:text-gray-300"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-5 py-4 text-right font-semibold text-gray-700 dark:text-gray-300"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 dark:divide-gray-800"
                        >
                            <tr
                                v-for="order in orders.data"
                                :key="order.id"
                                class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/50"
                            >
                                <!-- Cell: Invoice -->
                                <td class="px-5 py-4">
                                    <div
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ order.invoice_number }}
                                    </div>
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{
                                            new Date(
                                                order.created_at,
                                            ).toLocaleDateString('id-ID', {
                                                day: 'numeric',
                                                month: 'short',
                                                year: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit',
                                            })
                                        }}
                                    </div>
                                </td>

                                <!-- Cell: User -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img
                                            v-if="order.user.photo"
                                            :src="order.user.photo"
                                            alt="Avatar"
                                            class="h-8 w-8 shrink-0 rounded-full border border-gray-200 object-cover dark:border-gray-700"
                                        />
                                        <div
                                            v-else
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-purple-100 text-xs font-bold text-primary-hover dark:bg-purple-900/50 dark:text-purple-400"
                                        >
                                            {{
                                                (order.user.first_name?.[0] ||
                                                    '') +
                                                (order.user.last_name?.[0] ||
                                                    '')
                                            }}
                                        </div>
                                        <div>
                                            <div
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ order.user.first_name }}
                                                {{ order.user.last_name }}
                                            </div>
                                            <div
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {{ order.user.email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Cell: Item -->
                                <td class="px-5 py-4">
                                    <div
                                        class="line-clamp-1 font-medium text-gray-900 dark:text-white"
                                        :title="order.item_name"
                                    >
                                        {{ order.item_name }}
                                    </div>
                                    <span
                                        class="mt-1 inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600 uppercase dark:bg-gray-800 dark:text-gray-400"
                                    >
                                        {{
                                            order.orderable_type.includes(
                                                'Course',
                                            )
                                                ? 'Kelas Online'
                                                : 'Sesi Pendamping'
                                        }}
                                    </span>
                                </td>

                                <!-- Cell: Total -->
                                <td
                                    class="px-5 py-4 text-right font-medium text-gray-900 dark:text-white"
                                >
                                    {{ formatRupiah(order.final_amount) }}
                                </td>

                                <!-- Cell: Status -->
                                <td class="px-5 py-4 text-center">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium uppercase',
                                            statusColor(order.status),
                                        ]"
                                    >
                                        {{ order.status }}
                                    </span>
                                </td>

                                <!-- Cell: Aksi -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end">
                                        <Link
                                            :href="`/admin/orders/${order.id}`"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-primary dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-purple-400"
                                            title="Detail Transaksi"
                                        >
                                            <Eye class="h-3.5 w-3.5" />
                                            Detail
                                        </Link>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="orders.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-5 py-16 text-center text-sm text-gray-400 dark:text-gray-500"
                                >
                                    Tidak ada data transaksi yang ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="orders.links?.length > 3"
                class="mt-8 flex justify-end gap-1.5"
            >
                <Link
                    v-for="link in orders.links"
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
