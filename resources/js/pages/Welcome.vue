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

    <div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">

        <!-- ── NAVBAR ──────────────────────────────────────── -->
        <header class="sticky top-0 z-50 border-b border-gray-100 dark:border-gray-800 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-sm">DF</span>
                        </div>
                        <span class="font-bold text-lg text-purple-700 dark:text-purple-400">
                            DifaFriends
                        </span>
                    </div>

                    <!-- Nav Links -->
                    <nav class="hidden md:flex items-center gap-6 text-sm">
                        <a href="#categories" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors">
                            Kategori
                        </a>
                        <a href="#courses" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors">
                            Kelas
                        </a>
                        <a href="#how-it-works" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors">
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
                                class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors"
                            >
                                Masuk
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="text-sm font-medium bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors"
                            >
                                Daftar Gratis
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- ── HERO ────────────────────────────────────────── -->
        <section class="relative overflow-hidden bg-gradient-to-br from-purple-50 to-white dark:from-gray-900 dark:to-gray-950 py-20 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-sm font-medium px-3 py-1 rounded-full mb-6">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        Platform Edukasi Inklusif #1 Indonesia
                    </div>

                    <h1 class="text-4xl lg:text-6xl font-bold leading-tight mb-6">
                        Belajar Bersama,
                        <span class="text-purple-600">Tumbuh Bersama</span>
                    </h1>

                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                        Platform pembelajaran inklusif untuk anak-anak difabel. Temukan kelas online berkualitas dan guru pendamping profesional yang tepat untuk si kecil.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-flex items-center justify-center gap-2 bg-purple-600 text-white font-semibold px-8 py-4 rounded-xl hover:bg-purple-700 transition-all hover:shadow-lg hover:shadow-purple-200"
                        >
                            Mulai Belajar Gratis
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </Link><a href="#how-it-works" class="inline-flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-medium px-8 py-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Lihat Cara Kerja
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="flex flex-wrap gap-8 mt-12">
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">500+</div>
                            <div class="text-sm text-gray-500">Siswa Aktif</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">50+</div>
                            <div class="text-sm text-gray-500">Tentor Terverifikasi</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">100+</div>
                            <div class="text-sm text-gray-500">Kelas Tersedia</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">4.9</div>
                            <div class="text-sm text-gray-500">Rating Platform</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FEATURES ────────────────────────────────────── -->
        <section class="py-20 bg-white dark:bg-gray-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <h2 class="text-3xl font-bold mb-4">Mengapa DifaFriends?</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        Kami memahami kebutuhan unik setiap anak difabel dan menyediakan solusi pembelajaran yang tepat.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="p-6 rounded-2xl border border-gray-100 dark:border-gray-800 hover:border-purple-200 dark:hover:border-purple-800 transition-colors group">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4 group-hover:bg-purple-200 transition-colors">
                            <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Tentor Terverifikasi</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Semua tentor dan guru pendamping diverifikasi langsung oleh tim DifaFriends. Bersertifikat dan berpengalaman.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl border border-gray-100 dark:border-gray-800 hover:border-purple-200 dark:hover:border-purple-800 transition-colors group">
                        <div class="w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center mb-4 group-hover:bg-teal-200 transition-colors">
                            <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Pantau Progress Real-time</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Orang tua dapat memantau perkembangan anak secara real-time. Laporan detail tersedia kapan saja.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl border border-gray-100 dark:border-gray-800 hover:border-purple-200 dark:hover:border-purple-800 transition-colors group">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4 group-hover:bg-amber-200 transition-colors">
                            <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Pembayaran Aman</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Transaksi aman via Midtrans. Tersedia transfer bank, e-wallet GoPay, OVO, DANA, dan QRIS.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CATEGORIES ──────────────────────────────────── -->
        <section id="categories" class="py-20 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <h2 class="text-3xl font-bold mb-4">Kategori Pembelajaran</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        Program pembelajaran yang dirancang khusus untuk berbagai kebutuhan anak difabel.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:border-purple-200 dark:hover:border-purple-700 hover:shadow-md transition-all cursor-pointer group"
                    >
                        <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4">
                            <span class="text-purple-600 font-bold text-sm">
                                {{ category.name.charAt(0) }}
                            </span>
                        </div>
                        <h3 class="font-semibold mb-1 group-hover:text-purple-600 transition-colors">
                            {{ category.name }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 line-clamp-2">
                            {{ category.description }}
                        </p>
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="child in category.children.slice(0, 3)"
                                :key="child.id"
                                class="text-xs bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 px-2 py-0.5 rounded-full"
                            >
                                {{ child.name }}
                            </span>
                            <span
                                v-if="category.children.length > 3"
                                class="text-xs text-gray-400 px-2 py-0.5"
                            >
                                +{{ category.children.length - 3 }} lagi
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FEATURED COURSES ────────────────────────────── -->
        <section id="courses" class="py-20 bg-white dark:bg-gray-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between mb-14">
                    <div>
                        <h2 class="text-3xl font-bold mb-4">Kelas Unggulan</h2>
                        <p class="text-gray-500 dark:text-gray-400">
                            Kelas terpopuler yang dipilih oleh ribuan orang tua.
                        </p>
                    </div>
                    <a href="#" class="hidden md:flex items-center gap-1 text-sm text-purple-600 hover:text-purple-700 font-medium">
                        Lihat semua
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                <!-- Empty state kalau belum ada course -->
                <div v-if="featuredCourses.length === 0" class="text-center py-16 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p>Belum ada kelas yang tersedia.</p>
                </div>

                <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="course in featuredCourses"
                        :key="course.id"
                        class="rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden hover:shadow-lg transition-all group cursor-pointer"
                    >
                        <!-- Thumbnail -->
                        <div class="aspect-video bg-purple-100 dark:bg-purple-900/30 relative overflow-hidden">
                            <img
                                v-if="course.thumbnail"
                                :src="`/storage/${course.thumbnail}`"
                                :alt="course.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                </svg>
                            </div>
                            <!-- Category badge -->
                            <span class="absolute top-3 left-3 text-xs bg-white/90 text-purple-700 font-medium px-2 py-1 rounded-full">
                                {{ course.category.name }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <h3 class="font-semibold mb-1 line-clamp-2 group-hover:text-purple-600 transition-colors">
                                {{ course.title }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                {{ course.instructor.first_name }} {{ course.instructor.last_name }}
                            </p>

                            <div class="flex items-center justify-between">
                                <div>
                                    <span v-if="course.discount_price" class="text-lg font-bold text-purple-600">
                                        {{ formatPrice(course.discount_price) }}
                                    </span>
                                    <span v-else class="text-lg font-bold text-purple-600">
                                        {{ course.price === 0 ? 'Gratis' : formatPrice(course.price) }}
                                    </span>
                                    <span v-if="course.discount_price" class="text-sm text-gray-400 line-through ml-2">
                                        {{ formatPrice(course.price) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ formatDuration(course.duration_minutes) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── HOW IT WORKS ────────────────────────────────── -->
        <section id="how-it-works" class="py-20 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <h2 class="text-3xl font-bold mb-4">Cara Kerja DifaFriends</h2>
                    <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
                        Mulai perjalanan belajar si kecil dalam 3 langkah mudah.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div v-for="(step, index) in [
                        { title: 'Daftar & Pilih Kelas', desc: 'Buat akun gratis, browse katalog kelas sesuai kebutuhan anak, dan pilih program yang tepat.' },
                        { title: 'Bayar dengan Aman', desc: 'Lakukan pembayaran melalui berbagai metode: transfer bank, GoPay, OVO, DANA, atau QRIS.' },
                        { title: 'Mulai Belajar', desc: 'Akses materi kapan saja dan pantau progress perkembangan anak secara real-time di dashboard.' },
                    ]" :key="index" class="text-center">
                        <div class="w-14 h-14 rounded-full bg-purple-600 text-white font-bold text-xl flex items-center justify-center mx-auto mb-4">
                            {{ index + 1 }}
                        </div>
                        <h3 class="font-semibold text-lg mb-2">{{ step.title }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">{{ step.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CTA BANNER ──────────────────────────────────── -->
        <section class="py-20 bg-purple-600">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-white mb-4">
                    Siap Memulai Perjalanan Belajar?
                </h2>
                <p class="text-purple-200 mb-8 text-lg">
                    Bergabunglah dengan ratusan keluarga yang sudah mempercayakan pendidikan inklusif anak mereka kepada DifaFriends.
                </p>
                <Link
                    v-if="canRegister"
                    :href="register()"
                    class="inline-flex items-center gap-2 bg-white text-purple-600 font-semibold px-8 py-4 rounded-xl hover:bg-purple-50 transition-colors"
                >
                    Daftar Sekarang — Gratis!
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </Link>
            </div>
        </section>

        <!-- ── FOOTER ──────────────────────────────────────── -->
        <footer class="py-12 bg-gray-900 text-gray-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-md bg-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-xs">DF</span>
                        </div>
                        <span class="font-semibold text-white">DifaFriends</span>
                    </div>
                    <p class="text-sm">
                        © 2026 DifaFriends. Platform Edukasi Inklusif Indonesia.
                    </p>
                    <div class="flex items-center gap-4 text-sm">
                        <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                        <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                        <a href="#" class="hover:text-white transition-colors">Kontak</a>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</template>
