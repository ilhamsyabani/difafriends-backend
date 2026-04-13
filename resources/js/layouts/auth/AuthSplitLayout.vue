<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
// import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

const page = usePage();
const appName = (page.props.name as string) || 'DifaFriends';
// const appLogo = page.props.appLogo as string | null;

defineProps<{
    title?: string;
    description?: string;
}>();

const features = [
    'Kurikulum khusus untuk anak berkebutuhan khusus',
    'Pendampingan oleh terapis profesional',
    'Laporan perkembangan berkala',
    'Komunitas orang tua & pendidik inklusif',
];
</script>

<template>
    <div class="min-h-svh lg:grid lg:grid-cols-2">
        <!-- Left: Brand Panel -->
        <div
            class="from-pimary relative hidden overflow-hidden bg-gradient-to-br from-primary/60 via-primary/90 to-orange-500 lg:flex lg:flex-col"
        >
            <!-- Decorative blobs -->
            <div
                class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-white/5 blur-3xl"
            />
            <div
                class="absolute -right-32 -bottom-32 h-80 w-80 rounded-full bg-orange-100/10 blur-3xl"
            />
            <div
                class="absolute top-1/2 right-0 h-64 w-64 -translate-y-1/2 rounded-full bg-white/[0.03] blur-2xl"
            />

            <!-- Logo -->
            <div class="relative z-10 p-10">
                <Link :href="home()" class="flex w-fit items-center gap-3">
                    <img
                        src="/img/logo.png"
                        alt="DifaFriends"
                        class="h-6 object-contain"
                    />
                </Link>
            </div>

            <!-- Center content -->
            <div
                class="relative z-10 flex flex-1 flex-col justify-center px-10"
            >
                <div class="mb-10">
                    <div
                        class="mb-5 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-medium text-orange-50 ring-1 ring-white/20"
                    >
                        ✦ Platform Edukasi Inklusif
                    </div>
                    <h2
                        class="mb-4 text-4xl leading-tight font-bold tracking-tight text-white"
                    >
                        Mendampingi Setiap Langkah
                        <span class="text-orange-100">Tumbuh Kembang ABK</span>
                    </h2>
                    <p class="text-base leading-relaxed text-orange-50/80">
                        Bergabunglah dengan ribuan orang tua dan pendidik yang
                        telah mempercayakan perkembangan anak mereka kepada
                        Difafriends.
                    </p>
                </div>

                <ul class="space-y-4">
                    <li
                        v-for="feature in features"
                        :key="feature"
                        class="flex items-center gap-3 text-sm text-orange-100"
                    >
                        <div
                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-orange-100/25 ring-1 ring-orange-300/30"
                        >
                            <svg
                                class="h-3 w-3 text-orange-200"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                        {{ feature }}
                    </li>
                </ul>
            </div>

            <!-- Bottom stats -->
            <div class="relative z-10 border-t border-white/10 p-10">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-white">500+</div>
                        <div class="mt-0.5 text-xs text-orange-300">
                            Pengguna Aktif
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-white">50+</div>
                        <div class="mt-0.5 text-xs text-orange-300">
                            Kelas Tersedia
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-white">30+</div>
                        <div class="mt-0.5 text-xs text-orange-300">
                            Terapis Profesional
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Form Panel -->
        <div
            class="flex flex-col items-center justify-center bg-background px-6 py-12 lg:px-16"
        >
            <!-- Mobile logo -->
            <div class="mb-8 flex lg:hidden">
                <Link :href="home()" class="flex items-center gap-2">
                    <img
                        src="/img/logo.png"
                        alt="DifaFriends"
                        class="h-6 object-contain"
                    />
                </Link>
            </div>

            <div class="w-full max-w-sm">
                <div class="mb-8 space-y-2">
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
