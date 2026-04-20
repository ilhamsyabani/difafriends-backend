<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, MapPin, Wallet, CheckCircle2, Users } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import GuestLayout from '@/layouts/GuestLayout.vue';

const props = defineProps<{
    companions: {
        data: Array<{
            id: number;
            first_name: string;
            last_name: string;
            photo: string | null;
            city: string | null;
            bio: string | null;
            bookings_as_tutor_count: number;
            schedules: Array<{ price: number }>;
        }>;
        links: any[];
        meta: any;
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search ?? '');
let timeout: ReturnType<typeof setTimeout>;

watch(search, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            '/companions',
            { search: val || undefined },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 400);
});

function minPrice(schedules: Array<{ price: number }>): string {
    if (!schedules.length) {
        return 'Hubungi untuk harga';
    }

    const min = Math.min(...schedules.map((s) => s.price));

    if (min === 0) {
        return 'Gratis';
    }

    return (
        'Mulai ' +
        new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(min)
    );
}

// Helper untuk inisial nama jika foto tidak ada
function getInitials(firstName: string, lastName: string): string {
    const f = firstName ? firstName.charAt(0) : '';
    const l = lastName ? lastName.charAt(0) : '';

    return (f + l).toUpperCase();
}
</script>

<template>
    <GuestLayout>
        <Head title="Guru Pendamping — DifaFriends" />

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <!-- Header & Search -->
            <div
                class="mb-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
            >
                <div>
                    <h1
                        class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        Guru Pendamping
                    </h1>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">
                        Temukan guru pendamping profesional dan berpengalaman
                        untuk si kecil.
                    </p>
                </div>

                <div class="relative w-full md:w-80 lg:w-96">
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                    >
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <Input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama guru pendamping..."
                        class="h-10 w-full rounded-xl bg-white pl-10 shadow-sm dark:bg-gray-900"
                    />
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="companions.data.length === 0"
                class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50 py-20 dark:border-gray-800 dark:bg-gray-900/50"
            >
                <div
                    class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-200 text-gray-400 dark:bg-gray-800 dark:text-gray-500"
                >
                    <Users class="h-7 w-7" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Tidak Ditemukan
                </h3>
                <p
                    class="mt-1 max-w-sm text-center text-sm text-gray-500 dark:text-gray-400"
                >
                    Maaf, tidak ada guru pendamping yang cocok dengan kata kunci
                    pencarian Anda saat ini.
                </p>
            </div>

            <!-- Grid Cards -->
            <div
                v-else
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <Link
                    v-for="companion in companions.data"
                    :key="companion.id"
                    :href="`/companions/${companion.id}`"
                    class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] transition-all duration-300 hover:-translate-y-1 hover:border-purple-200 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900 dark:hover:border-purple-700"
                >
                    <div class="flex flex-1 flex-col p-6">
                        <!-- Avatar -->
                        <div
                            class="mx-auto mb-5 flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-purple-50 bg-purple-100 dark:border-purple-900/30 dark:bg-purple-900/50"
                        >
                            <img
                                v-if="companion.photo"
                                :src="`/storage/${companion.photo}`"
                                :alt="`${companion.first_name} ${companion.last_name}`"
                                class="h-full w-full object-cover"
                            />
                            <span
                                v-else
                                class="text-2xl font-bold tracking-wider text-primary dark:text-purple-400"
                            >
                                {{
                                    getInitials(
                                        companion.first_name,
                                        companion.last_name,
                                    )
                                }}
                            </span>
                        </div>

                        <!-- Info -->
                        <h3
                            class="text-center text-lg font-bold text-gray-900 transition-colors group-hover:text-primary dark:text-white dark:group-hover:text-purple-400"
                        >
                            {{ companion.first_name }} {{ companion.last_name }}
                        </h3>

                        <div
                            v-if="companion.city"
                            class="mt-1.5 flex items-center justify-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                        >
                            <MapPin class="h-3.5 w-3.5" />
                            <span>{{ companion.city }}</span>
                        </div>

                        <p
                            v-if="companion.bio"
                            class="mt-4 line-clamp-2 text-center text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            {{ companion.bio }}
                        </p>
                    </div>

                    <!-- Card Footer (Harga & Pengalaman) -->
                    <div
                        class="mt-auto border-t border-gray-100 bg-gray-50/80 p-5 dark:border-gray-800 dark:bg-gray-800/50"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col gap-1">
                                <span
                                    class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400"
                                >
                                    <Wallet class="h-3.5 w-3.5" /> Tarif
                                </span>
                                <span
                                    class="text-sm font-bold text-primary dark:text-purple-400"
                                >
                                    {{ minPrice(companion.schedules) }}
                                </span>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span
                                    class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400"
                                >
                                    <CheckCircle2 class="h-3.5 w-3.5" />
                                    Pengalaman
                                </span>
                                <span
                                    class="text-sm font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ companion.bookings_as_tutor_count }} sesi
                                </span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div
                v-if="companions.links?.length > 3"
                class="mt-10 flex justify-center gap-1.5"
            >
                <Link
                    v-for="link in companions.links"
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
    </GuestLayout>
</template>
