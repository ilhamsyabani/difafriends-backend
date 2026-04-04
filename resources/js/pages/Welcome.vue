<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login, register } from '@/routes';

defineProps<{
    canRegister: boolean;
    categories: Array<{
        id: number;
        name: string;
        description: string;
        children: Array<{ id: number; name: string }>;
    }>;
    featuredCourses: Array<{
        id: number;
        title: string;
        thumbnail: string | null;
        price: number;
        discount_price: number | null;
        duration_minutes: number;
        instructor: { first_name: string; last_name: string };
        category: { name: string };
    }>;
}>();

function formatPrice(price: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
}

function formatDuration(minutes: number): string {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return hours > 0 ? `${hours}j ${mins}m` : `${mins}m`;
}
</script>

<template>
    <Head title="DifaFriends — Platform Edukasi Inklusif">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="min-h-screen bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100"
    >
        <!-- ── NAVBAR ──────────────────────────────────────── -->
        <header
            class="sticky top-0 z-50 border-b border-gray-100 bg-white/80 backdrop-blur-md dark:border-gray-800 dark:bg-gray-950/80"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-600"
                        >
                            <span class="text-sm font-bold text-white">DF</span>
                        </div>
                        <span
                            class="text-lg font-bold text-purple-700 dark:text-purple-400"
                        >
                            DifaFriends
                        </span>
                    </div>

                    <!-- Nav Links -->
                    <nav class="hidden items-center gap-6 text-sm md:flex">
                        <a
                            href="#categories"
                            class="text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-400"
                        >
                            Kategori
                        </a>
                        <a
                            href="/courses"
                            class="text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-400"
                        >
                            Kelas
                        </a>
                        <a
                            href="/companions"
                            class="text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-400"
                        >
                            Guru Pendamping
                        </a>
                        <a
                            href="#how-it-works"
                            class="text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-400"
                        >
                            Cara Kerja
                        </a>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="flex items-center gap-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="'/dashboard'"
                            class="text-sm font-medium text-purple-600 hover:text-purple-700"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="text-sm font-medium text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-400"
                            >
                                Masuk
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-purple-700"
                            >
                                Daftar Gratis
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- ── HERO ────────────────────────────────────────── -->
        <section
            class="relative overflow-hidden bg-gradient-to-br from-purple-50 to-white py-20 lg:py-32 dark:from-gray-900 dark:to-gray-950"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <!-- Badge -->
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full bg-purple-100 px-3 py-1 text-sm font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-300"
                    >
                        <span class="h-2 w-2 rounded-full bg-purple-500"></span>
                        Platform Edukasi Inklusif #1 Indonesia
                    </div>

                    <h1
                        class="mb-6 text-4xl leading-tight font-bold lg:text-6xl"
                    >
                        Belajar Bersama,
                        <span class="text-purple-600">Tumbuh Bersama</span>
                    </h1>

                    <p
                        class="mb-8 text-lg leading-relaxed text-gray-600 dark:text-gray-400"
                    >
                        Platform pembelajaran inklusif untuk anak-anak difabel.
                        Temukan kelas online berkualitas dan guru pendamping
                        profesional yang tepat untuk si kecil.
                    </p>

                    <div class="flex flex-col gap-4 sm:flex-row">
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-8 py-4 font-semibold text-white transition-all hover:bg-purple-700 hover:shadow-lg hover:shadow-purple-200"
                        >
                            Mulai Belajar Gratis
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"
                                />
                            </svg> </Link
                        ><a
                            href="#how-it-works"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 px-8 py-4 font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            Lihat Cara Kerja
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="mt-12 flex flex-wrap gap-8">
                        <div>
                            <div
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                500+
                            </div>
                            <div class="text-sm text-gray-500">Siswa Aktif</div>
                        </div>
                        <div>
                            <div
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                50+
                            </div>
                            <div class="text-sm text-gray-500">
                                Tentor Terverifikasi
                            </div>
                        </div>
                        <div>
                            <div
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                100+
                            </div>
                            <div class="text-sm text-gray-500">
                                Kelas Tersedia
                            </div>
                        </div>
                        <div>
                            <div
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                4.9
                            </div>
                            <div class="text-sm text-gray-500">
                                Rating Platform
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FEATURES ────────────────────────────────────── -->
        <section class="bg-white py-20 dark:bg-gray-950">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-14 text-center">
                    <h2 class="mb-4 text-3xl font-bold">
                        Mengapa DifaFriends?
                    </h2>
                    <p
                        class="mx-auto max-w-xl text-gray-500 dark:text-gray-400"
                    >
                        Kami memahami kebutuhan unik setiap anak difabel dan
                        menyediakan solusi pembelajaran yang tepat.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <div
                        class="group rounded-2xl border border-gray-100 p-6 transition-colors hover:border-purple-200 dark:border-gray-800 dark:hover:border-purple-800"
                    >
                        <div
                            class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 transition-colors group-hover:bg-purple-200 dark:bg-purple-900/30"
                        >
                            <svg
                                class="h-6 w-6 text-purple-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold">
                            Tentor Terverifikasi
                        </h3>
                        <p
                            class="text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Semua tentor dan guru pendamping diverifikasi
                            langsung oleh tim DifaFriends. Bersertifikat dan
                            berpengalaman.
                        </p>
                    </div>

                    <div
                        class="group rounded-2xl border border-gray-100 p-6 transition-colors hover:border-purple-200 dark:border-gray-800 dark:hover:border-purple-800"
                    >
                        <div
                            class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-teal-100 transition-colors group-hover:bg-teal-200 dark:bg-teal-900/30"
                        >
                            <svg
                                class="h-6 w-6 text-teal-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold">
                            Pantau Progress Real-time
                        </h3>
                        <p
                            class="text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Orang tua dapat memantau perkembangan anak secara
                            real-time. Laporan detail tersedia kapan saja.
                        </p>
                    </div>

                    <div
                        class="group rounded-2xl border border-gray-100 p-6 transition-colors hover:border-purple-200 dark:border-gray-800 dark:hover:border-purple-800"
                    >
                        <div
                            class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 transition-colors group-hover:bg-amber-200 dark:bg-amber-900/30"
                        >
                            <svg
                                class="h-6 w-6 text-amber-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold">
                            Pembayaran Aman
                        </h3>
                        <p
                            class="text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Transaksi aman via Midtrans. Tersedia transfer bank,
                            e-wallet GoPay, OVO, DANA, dan QRIS.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CATEGORIES ──────────────────────────────────── -->
        <section id="categories" class="bg-gray-50 py-20 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-14 text-center">
                    <h2 class="mb-4 text-3xl font-bold">
                        Kategori Pembelajaran
                    </h2>
                    <p
                        class="mx-auto max-w-xl text-gray-500 dark:text-gray-400"
                    >
                        Program pembelajaran yang dirancang khusus untuk
                        berbagai kebutuhan anak difabel.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="group cursor-pointer rounded-2xl border border-gray-100 bg-white p-6 transition-all hover:border-purple-200 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-purple-700"
                    >
                        <div
                            class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30"
                        >
                            <span class="text-sm font-bold text-purple-600">
                                {{ category.name.charAt(0) }}
                            </span>
                        </div>
                        <h3
                            class="mb-1 font-semibold transition-colors group-hover:text-purple-600"
                        >
                            {{ category.name }}
                        </h3>
                        <p
                            class="mb-3 line-clamp-2 text-xs text-gray-500 dark:text-gray-400"
                        >
                            {{ category.description }}
                        </p>
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="child in category.children.slice(0, 3)"
                                :key="child.id"
                                class="rounded-full bg-purple-50 px-2 py-0.5 text-xs text-purple-600 dark:bg-purple-900/20 dark:text-purple-400"
                            >
                                {{ child.name }}
                            </span>
                            <span
                                v-if="category.children.length > 3"
                                class="px-2 py-0.5 text-xs text-gray-400"
                            >
                                +{{ category.children.length - 3 }} lagi
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FEATURED COURSES ────────────────────────────── -->
        <section id="courses" class="bg-white py-20 dark:bg-gray-950">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-14 flex items-end justify-between">
                    <div>
                        <h2 class="mb-4 text-3xl font-bold">Kelas Unggulan</h2>
                        <p class="text-gray-500 dark:text-gray-400">
                            Kelas terpopuler yang dipilih oleh ribuan orang tua.
                        </p>
                    </div>
                    <a
                        href="#"
                        class="hidden items-center gap-1 text-sm font-medium text-purple-600 hover:text-purple-700 md:flex"
                    >
                        Lihat semua
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"
                            />
                        </svg>
                    </a>
                </div>

                <!-- Empty state kalau belum ada course -->
                <div
                    v-if="featuredCourses.length === 0"
                    class="py-16 text-center text-gray-400"
                >
                    <svg
                        class="mx-auto mb-4 h-16 w-16 opacity-30"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>
                    <p>Belum ada kelas yang tersedia.</p>
                </div>

                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="course in featuredCourses"
                        :key="course.id"
                        class="group cursor-pointer overflow-hidden rounded-2xl border border-gray-100 transition-all hover:shadow-lg dark:border-gray-800"
                    >
                        <!-- Thumbnail -->
                        <div
                            class="relative aspect-video overflow-hidden bg-purple-100 dark:bg-purple-900/30"
                        >
                            <img
                                v-if="course.thumbnail"
                                :src="`/storage/${course.thumbnail}`"
                                :alt="course.title"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center"
                            >
                                <svg
                                    class="h-12 w-12 text-purple-300"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1"
                                        d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"
                                    />
                                </svg>
                            </div>
                            <!-- Category badge -->
                            <span
                                class="absolute top-3 left-3 rounded-full bg-white/90 px-2 py-1 text-xs font-medium text-purple-700"
                            >
                                {{ course.category.name }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <h3
                                class="mb-1 line-clamp-2 font-semibold transition-colors group-hover:text-purple-600"
                            >
                                {{ course.title }}
                            </h3>
                            <p
                                class="mb-3 text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{ course.instructor.first_name }}
                                {{ course.instructor.last_name }}
                            </p>

                            <div class="flex items-center justify-between">
                                <div>
                                    <span
                                        v-if="course.discount_price"
                                        class="text-lg font-bold text-purple-600"
                                    >
                                        {{ formatPrice(course.discount_price) }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-lg font-bold text-purple-600"
                                    >
                                        {{
                                            course.price === 0
                                                ? 'Gratis'
                                                : formatPrice(course.price)
                                        }}
                                    </span>
                                    <span
                                        v-if="course.discount_price"
                                        class="ml-2 text-sm text-gray-400 line-through"
                                    >
                                        {{ formatPrice(course.price) }}
                                    </span>
                                </div>
                                <span
                                    class="flex items-center gap-1 text-xs text-gray-400"
                                >
                                    <svg
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    {{
                                        formatDuration(course.duration_minutes)
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── HOW IT WORKS ────────────────────────────────── -->
        <section id="how-it-works" class="bg-gray-50 py-20 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-14 text-center">
                    <h2 class="mb-4 text-3xl font-bold">
                        Cara Kerja DifaFriends
                    </h2>
                    <p
                        class="mx-auto max-w-xl text-gray-500 dark:text-gray-400"
                    >
                        Mulai perjalanan belajar si kecil dalam 3 langkah mudah.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <div
                        v-for="(step, index) in [
                            {
                                title: 'Daftar & Pilih Kelas',
                                desc: 'Buat akun gratis, browse katalog kelas sesuai kebutuhan anak, dan pilih program yang tepat.',
                            },
                            {
                                title: 'Bayar dengan Aman',
                                desc: 'Lakukan pembayaran melalui berbagai metode: transfer bank, GoPay, OVO, DANA, atau QRIS.',
                            },
                            {
                                title: 'Mulai Belajar',
                                desc: 'Akses materi kapan saja dan pantau progress perkembangan anak secara real-time di dashboard.',
                            },
                        ]"
                        :key="index"
                        class="text-center"
                    >
                        <div
                            class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-purple-600 text-xl font-bold text-white"
                        >
                            {{ index + 1 }}
                        </div>
                        <h3 class="mb-2 text-lg font-semibold">
                            {{ step.title }}
                        </h3>
                        <p
                            class="text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            {{ step.desc }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CTA BANNER ──────────────────────────────────── -->
        <section class="bg-purple-600 py-20">
            <div class="mx-auto max-w-4xl px-4 text-center">
                <h2 class="mb-4 text-3xl font-bold text-white">
                    Siap Memulai Perjalanan Belajar?
                </h2>
                <p class="mb-8 text-lg text-purple-200">
                    Bergabunglah dengan ratusan keluarga yang sudah
                    mempercayakan pendidikan inklusif anak mereka kepada
                    DifaFriends.
                </p>
                <Link
                    v-if="canRegister"
                    :href="register()"
                    class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-4 font-semibold text-purple-600 transition-colors hover:bg-purple-50"
                >
                    Daftar Sekarang — Gratis!
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3"
                        />
                    </svg>
                </Link>
            </div>
        </section>

        <!-- ── FOOTER ──────────────────────────────────────── -->
        <footer class="bg-gray-900 py-12 text-gray-400">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="flex flex-col items-center justify-between gap-4 md:flex-row"
                >
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-7 w-7 items-center justify-center rounded-md bg-purple-600"
                        >
                            <span class="text-xs font-bold text-white">DF</span>
                        </div>
                        <span class="font-semibold text-white"
                            >DifaFriends</span
                        >
                    </div>
                    <p class="text-sm">
                        © 2026 DifaFriends. Platform Edukasi Inklusif Indonesia.
                    </p>
                    <div class="flex items-center gap-4 text-sm">
                        <a href="#" class="transition-colors hover:text-white"
                            >Kebijakan Privasi</a
                        >
                        <a href="#" class="transition-colors hover:text-white"
                            >Syarat & Ketentuan</a
                        >
                        <a href="#" class="transition-colors hover:text-white"
                            >Kontak</a
                        >
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
