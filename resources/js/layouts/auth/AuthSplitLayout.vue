<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const slides = [
    {
        img: '/img/slide/learning-cuate.svg',
        alt: 'Ilustrasi Belajar',
        title: 'Belajar Bersama Ahlinya',
        desc: 'Kelas online berbasis bukti, dirancang khusus untuk anak berkebutuhan khusus.',
    },
    {
        img: '/img/slide/graduation-cuate.svg',
        alt: 'Ilustrasi Tentor',
        title: 'Tentor Bersertifikat',
        desc: 'Pendamping profesional siap membantu tumbuh kembang si kecil setiap hari.',
    },
    {
        img: '/img/slide/overview-cuate.svg',
        alt: 'Ilustrasi Grafik',
        title: 'Pantau Perkembangan Anak',
        desc: 'Asesmen berkala dan laporan kemajuan yang mudah dipahami orang tua.',
    },
];

const current = ref(0);
let timer: ReturnType<typeof setInterval>;

function goTo(i: number) {
    current.value = i;
    resetTimer();
}

function resetTimer() {
    clearInterval(timer);
    timer = setInterval(() => {
        current.value = (current.value + 1) % slides.length;
    }, 4000);
}

onMounted(() => resetTimer());
onUnmounted(() => clearInterval(timer));
</script>

<template>
    <div class="flex min-h-svh">
        <!-- Panel Kiri -->
        <aside
            class="relative hidden w-6/10 shrink-0 overflow-hidden bg-gradient-to-br from-primary to-primary/60 lg:flex lg:flex-col"
        >
            <!-- Decorative ring shapes -->
            <div
                class="pointer-events-none absolute top-16 right-16 h-40 w-40 rounded-full border border-white/15"
            ></div>
            <div
                class="pointer-events-none absolute top-20 right-20 h-28 w-28 rounded-full border border-white/10"
            ></div>
            <div
                class="pointer-events-none absolute bottom-20 left-10 h-32 w-32 rounded-full border border-white/15"
            ></div>
            <div
                class="pointer-events-none absolute bottom-16 left-16 h-20 w-20 rounded-full border border-white/10"
            ></div>

            <!-- Logo -->
            <div class="relative z-10 p-10">
                <Link :href="home()">
                    <img
                        src="/img/logo.png"
                        alt="Difafriends"
                        class="h-7 brightness-0 invert"
                    />
                </Link>
            </div>

            <!-- Slides -->
            <div
                class="relative z-10 flex flex-1 items-center justify-center px-12"
            >
                <div class="w-full max-w-sm rounded-3xl">
                    <!-- Ilustrasi -->
                    <Transition name="fade" mode="out-in">
                        <img
                            :key="current"
                            :src="slides[current].img"
                            :alt="slides[current].alt"
                            class="slide-img mx-auto"
                        />
                    </Transition>

                    <!-- Teks -->
                    <Transition name="fade" mode="out-in">
                        <div :key="'text-' + current">
                            <h2
                                class="mb-3 text-2xl font-bold tracking-tight text-white"
                            >
                                {{ slides[current].title }}
                            </h2>
                            <p class="text-sm leading-relaxed text-white/70">
                                {{ slides[current].desc }}
                            </p>
                        </div>
                    </Transition>

                    <!-- Dots -->
                    <div class="mt-8 flex items-center justify-center gap-2">
                        <button
                            v-for="(_, i) in slides"
                            :key="i"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="
                                current === i
                                    ? 'w-6 bg-white'
                                    : 'w-2 bg-white/40 hover:bg-white/60'
                            "
                            @click="goTo(i)"
                        />
                    </div>
                </div>
            </div>

            <!-- Badge platform — mirip hero landing -->
            <div class="relative z-10 flex justify-center pb-4">
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-semibold text-white"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                    Platform #1 Edukasi Inklusif Indonesia
                </span>
            </div>

            <!-- Footer -->
            <div
                class="relative z-10 px-12 py-4 text-center text-[11px] text-white/40"
            >
                © {{ new Date().getFullYear() }} DifaFriends
            </div>
        </aside>

        <!-- Panel Kanan — Form -->
        <main
            class="flex flex-1 flex-col items-center justify-center bg-white px-6 py-12 dark:bg-[#0a0a0a]"
        >
            <!-- Mobile logo -->
            <div class="mb-8 flex lg:hidden">
                <Link :href="home()">
                    <img
                        src="/img/logo.png"
                        alt="DifaFriends"
                        class="h-7 dark:hidden"
                    />
                    <img
                        src="/img/logo.png"
                        alt="DifaFriends"
                        class="hidden h-7 brightness-0 invert dark:block"
                    />
                </Link>
            </div>

            <!-- Form -->
            <div class="w-full max-w-[380px]">
                <slot />
            </div>

            <!-- Footer -->
            <p class="mt-10 text-[11px] text-gray-400 dark:text-gray-600">
                © {{ new Date().getFullYear() }} DifaFriends ·
                <a href="#" class="hover:text-gray-600 dark:hover:text-gray-400"
                    >Privasi</a
                >
                &nbsp;·&nbsp;
                <a href="#" class="hover:text-gray-600 dark:hover:text-gray-400"
                    >Syarat</a
                >
            </p>
        </main>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.4s ease,
        transform 0.4s ease;
}
.fade-enter-from {
    opacity: 0;
    transform: translateY(8px);
}
.fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

.slide-img {
    filter: none;
}

/* Floating animation for glow orbs */
@keyframes float-slow {
    0%,
    100% {
        transform: translateY(0px) scale(1);
    }
    50% {
        transform: translateY(-24px) scale(1.05);
    }
}
@keyframes float-mid {
    0%,
    100% {
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        transform: translate(-50%, calc(-50% - 16px)) scale(1.08);
    }
}
@keyframes float-reverse {
    0%,
    100% {
        transform: translateY(0px) scale(1);
    }
    50% {
        transform: translateY(20px) scale(0.95);
    }
}

aside > div:nth-child(1) {
    animation: float-slow 8s ease-in-out infinite;
}
aside > div:nth-child(2) {
    animation: float-reverse 10s ease-in-out infinite;
}
aside > div:nth-child(3) {
    animation: float-mid 12s ease-in-out infinite;
}
aside > div:nth-child(4) {
    animation: float-slow 7s ease-in-out infinite 1s;
}
aside > div:nth-child(5) {
    animation: float-reverse 9s ease-in-out infinite 2s;
}
</style>
