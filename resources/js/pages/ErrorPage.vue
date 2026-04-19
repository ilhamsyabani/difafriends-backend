<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import {
    Lock,
    Search,
    Clock,
    Server,
    Wrench,
    Timer,
    AlertTriangle,
    ArrowLeft,
    Home,
} from 'lucide-vue-next';

const props = defineProps<{ status: number }>();

type ErrorConfig = {
    iconComponent: any;
    title: string;
    description: string;
    accent: string;
};

const errorConfig = computed((): ErrorConfig => {
    const configs: Record<number, ErrorConfig> = {
        403: {
            iconComponent: Lock,
            title: 'Akses Ditolak',
            description:
                'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi administrator jika Anda merasa ini adalah kesalahan.',
            accent: 'orange',
        },
        404: {
            iconComponent: Search,
            title: 'Halaman Tidak Ditemukan',
            description:
                'Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau tidak pernah ada. Periksa kembali URL atau kembali ke beranda.',
            accent: 'orange', // Diubah dari purple ke orange
        },
        419: {
            iconComponent: Clock,
            title: 'Sesi Kedaluwarsa',
            description:
                'Sesi Anda telah habis. Silakan muat ulang halaman dan coba lagi untuk melanjutkan.',
            accent: 'orange',
        },
        500: {
            iconComponent: Server,
            title: 'Kesalahan Server',
            description:
                'Terjadi kesalahan pada server kami. Tim teknis kami telah diberi tahu dan sedang menangani masalah ini.',
            accent: 'red',
        },
        503: {
            iconComponent: Wrench,
            title: 'Sedang Dalam Pemeliharaan',
            description:
                'Kami sedang melakukan pemeliharaan sistem untuk meningkatkan layanan. Silakan kembali beberapa saat lagi.',
            accent: 'blue',
        },
        504: {
            iconComponent: Timer,
            title: 'Waktu Habis',
            description:
                'Server tidak merespons dalam waktu yang ditentukan. Periksa koneksi internet Anda dan coba lagi.',
            accent: 'orange',
        },
    };

    return (
        configs[props.status] ?? {
            iconComponent: AlertTriangle,
            title: 'Terjadi Kesalahan',
            description:
                'Sesuatu yang tidak terduga terjadi. Silakan coba lagi atau hubungi dukungan jika masalah berlanjut.',
            accent: 'gray',
        }
    );
});

const accentClasses = computed(() => {
    const map: Record<
        string,
        {
            bg: string;
            text: string;
            ring: string;
            badge: string;
            gradient: string;
        }
    > = {
        orange: {
            bg: 'from-orange-500/10 via-amber-500/5 to-transparent',
            text: 'text-orange-500 dark:text-orange-400',
            ring: 'ring-orange-200 dark:ring-orange-900',
            badge: 'bg-orange-50 text-orange-600 dark:bg-orange-950/50 dark:text-orange-400',
            gradient: 'from-orange-500 to-amber-500',
        },
        red: {
            bg: 'from-red-500/10 via-rose-500/5 to-transparent',
            text: 'text-red-500 dark:text-red-400',
            ring: 'ring-red-200 dark:ring-red-900',
            badge: 'bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400',
            gradient: 'from-red-500 to-rose-500',
        },
        blue: {
            bg: 'from-blue-500/10 via-sky-500/5 to-transparent',
            text: 'text-blue-500 dark:text-blue-400',
            ring: 'ring-blue-200 dark:ring-blue-900',
            badge: 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400',
            gradient: 'from-blue-500 to-sky-500',
        },
        gray: {
            bg: 'from-gray-500/10 to-transparent',
            text: 'text-gray-500 dark:text-gray-400',
            ring: 'ring-gray-200 dark:ring-gray-700',
            badge: 'bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
            gradient: 'from-gray-500 to-gray-600',
        },
    };

    return map[errorConfig.value.accent] ?? map['orange'];
});

// Efek typing untuk deskripsi
const typedDescription = ref('');
const fullDescription = computed(() => errorConfig.value.description);

onMounted(() => {
    let i = 0;
    typedDescription.value = '';
    const interval = setInterval(() => {
        if (i < fullDescription.value.length) {
            typedDescription.value += fullDescription.value[i];
            i++;
        } else {
            clearInterval(interval);
        }
    }, 20);
});
</script>

<template>
    <Head :title="`${status} — ${errorConfig.title}`" />

    <div
        class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 px-4 py-16 dark:from-gray-950 dark:to-gray-900"
    >
        <!-- Background decorative blobs with animation -->
        <div
            class="pointer-events-none absolute inset-0 overflow-hidden"
            aria-hidden="true"
        >
            <div
                :class="[
                    'absolute -top-40 -right-40 h-[600px] w-[600px] animate-pulse rounded-full bg-gradient-to-br opacity-40 blur-3xl',
                    accentClasses.bg,
                ]"
            />
            <div
                class="absolute -bottom-40 -left-40 h-[500px] w-[500px] animate-pulse rounded-full bg-gradient-to-tr from-orange-500/10 via-amber-500/5 to-transparent opacity-40 blur-3xl delay-1000"
            />
        </div>

        <div class="relative z-10 w-full max-w-lg text-center">
            <!-- Icon circle with subtle bounce -->
            <!-- <div class="mb-8 flex animate-bounce justify-center">
                <div
                    :class="[
                        'flex h-24 w-24 items-center justify-center rounded-full ring-8 transition-all duration-300 hover:scale-105',
                        accentClasses.ring,
                        'bg-white/70 backdrop-blur-sm dark:bg-gray-900/70',
                    ]"
                >
                    <component
                        :is="errorConfig.iconComponent"
                        :class="['h-10 w-10', accentClasses.text]"
                        :stroke-width="1.5"
                    />
                </div>
            </div> -->

            <!-- Status badge with scale effect -->
            <div class="mb-5 flex justify-center">
                <span
                    :class="[
                        'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold tracking-widest uppercase transition-transform duration-300 hover:scale-105',
                        accentClasses.badge,
                    ]"
                >
                    Error {{ status }}
                </span>
            </div>

            <!-- Error code (large) -->
            <div class="mb-4 select-none">
                <span
                    :class="[
                        'text-[7rem] leading-none font-black tabular-nums opacity-10 dark:opacity-[0.07]',
                    ]"
                    >{{ status }}</span
                >
            </div>

            <!-- Title -->
            <h1
                class="mt-2 mb-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white"
            >
                {{ errorConfig.title }}
            </h1>

            <!-- Description with typing effect -->
            <p
                class="mx-auto mb-10 min-h-[80px] max-w-sm text-base leading-relaxed text-gray-500 dark:text-gray-400"
            >
                {{ typedDescription }}
                <span
                    class="ml-1 inline-block h-4 w-1 animate-pulse bg-current"
                ></span>
            </p>

            <!-- Actions -->
            <div
                class="flex flex-col items-center justify-center gap-3 sm:flex-row"
            >
                <!-- Back button -->
                <button
                    type="button"
                    onclick="history.back()"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white/80 px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm backdrop-blur-sm transition-all hover:scale-105 hover:bg-gray-50 hover:shadow-md dark:border-gray-700 dark:bg-gray-900/80 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </button>

                <!-- Home button with gradient -->
                <Link
                    href="/"
                    :class="[
                        'inline-flex items-center gap-2 rounded-xl bg-gradient-to-r px-6 py-3 text-sm font-semibold text-white shadow-md transition-all hover:scale-105 hover:shadow-lg',
                        accentClasses.gradient,
                    ]"
                >
                    <Home class="h-4 w-4" />
                    Ke Beranda
                </Link>
            </div>

            <!-- Brand footer -->
            <div
                class="mt-16 flex items-center justify-center gap-2 opacity-60"
            >
                <img src="/img/logo.png" alt="DifaFriends" class="h-5" />
            </div>
        </div>
    </div>
</template>
