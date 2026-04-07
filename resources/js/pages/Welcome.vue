<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
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
    // Props baru untuk Tentor / Companion
    companions: Array<{
        id: number;
        first_name: string;
        last_name: string;
        photo: string | null;
        bio: string;
        starting_price: number;
    }>;
}>();

const isMobileMenuOpen = ref(false);
const activeServiceTab = ref(0);

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
    <Head title="Difafriends — Platform Edukasi Inklusif">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="min-h-screen bg-white font-sans text-gray-900 dark:bg-gray-950 dark:text-gray-100"
    >
        <header
            class="sticky top-0 z-50 border-b border-gray-100 bg-white/90 shadow-sm backdrop-blur-md dark:border-gray-800 dark:bg-gray-950/90"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-20 items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Link
                            href="/"
                            class="flex items-center gap-2 focus:outline-none"
                        >
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-600 shadow-md"
                            >
                                <span class="text-sm font-bold text-white"
                                    >DF</span
                                >
                            </div>
                            <span
                                class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white"
                            >
                                Difafriends
                            </span>
                        </Link>
                    </div>

                    <nav
                        class="hidden items-center gap-8 text-sm font-medium lg:flex"
                    >
                        <a href="/" class="text-purple-600 transition-colors"
                            >Home</a
                        >
                        <a
                            href="#article"
                            class="text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-300 dark:hover:text-purple-400"
                            >Artikel</a
                        >
                        <a
                            href="#service"
                            class="text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-300 dark:hover:text-purple-400"
                            >Intervensi</a
                        >

                        <div class="group relative py-4">
                            <button
                                class="inline-flex items-center gap-1 text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-300"
                            >
                                Pelatihan
                                <svg
                                    class="h-4 w-4 transition-transform group-hover:rotate-180"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                                    ></path>
                                </svg>
                            </button>
                            <div
                                class="ring-opacity-5 absolute top-full left-0 hidden w-48 overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black group-hover:block dark:bg-gray-800 dark:ring-gray-700"
                            >
                                <div class="py-2">
                                    <a
                                        href="/pelatihan-guru"
                                        class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                        >Pelatihan Guru</a
                                    >
                                    <a
                                        href="/pelatihan-ortu"
                                        class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                        >Pelatihan Orang Tua</a
                                    >
                                </div>
                            </div>
                        </div>

                        <a
                            href="/companions"
                            class="text-gray-600 transition-colors hover:text-purple-600 dark:text-gray-300 dark:hover:text-purple-400"
                            >Tentor</a
                        >
                    </nav>

                    <div class="hidden items-center gap-4 lg:flex">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="'/dashboard'"
                            class="text-sm font-semibold text-purple-600 hover:text-purple-700"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="text-sm font-semibold text-gray-700 transition-colors hover:text-purple-600 dark:text-gray-300"
                            >
                                Masuk
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow"
                            >
                                Daftar Gratis
                            </Link>
                        </template>
                    </div>

                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none lg:hidden"
                    >
                        <svg
                            v-if="!isMobileMenuOpen"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div
                v-show="isMobileMenuOpen"
                class="border-t border-gray-100 bg-white lg:hidden dark:border-gray-800 dark:bg-gray-950"
            >
                <div class="space-y-1 px-4 pt-2 pb-3">
                    <a
                        href="/"
                        class="block rounded-md bg-purple-50 px-3 py-2 text-base font-medium text-purple-600 dark:bg-purple-900/20"
                        >Home</a
                    >
                    <a
                        href="#article"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-purple-600 dark:text-gray-300"
                        >Artikel</a
                    >
                    <a
                        href="#service"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-purple-600 dark:text-gray-300"
                        >Intervensi</a
                    >
                    <a
                        href="/pelatihan-guru"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-purple-600 dark:text-gray-300"
                        >Pelatihan Guru</a
                    >
                    <a
                        href="/pelatihan-ortu"
                        class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-purple-600 dark:text-gray-300"
                        >Pelatihan Orang Tua</a
                    >
                </div>
                <div
                    class="flex flex-col gap-3 border-t border-gray-100 px-5 pt-4 pb-4"
                >
                    <template v-if="!$page.props.auth.user">
                        <Link
                            :href="login()"
                            class="w-full rounded-xl border border-gray-300 px-4 py-2 text-center text-sm font-medium text-gray-700"
                            >Masuk</Link
                        >
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="w-full rounded-xl bg-purple-600 px-4 py-2 text-center text-sm font-medium text-white shadow-sm"
                            >Daftar Gratis</Link
                        >
                    </template>
                    <Link
                        v-else
                        :href="'/dashboard'"
                        class="w-full rounded-xl bg-purple-600 px-4 py-2 text-center text-sm font-medium text-white shadow-sm"
                        >Dashboard</Link
                    >
                </div>
            </div>
        </header>

        <section
            class="relative overflow-hidden bg-purple-50/50 py-16 lg:py-24 dark:bg-gray-900"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div class="max-w-2xl">
                        <h1
                            class="mb-6 text-4xl leading-tight font-extrabold text-gray-900 lg:text-5xl dark:text-white"
                        >
                            Layanan Intervensi Anak Berkebutuhan Khusus
                        </h1>
                        <p
                            class="mb-8 text-lg text-gray-600 dark:text-gray-400"
                        >
                            Difafriends adalah platform yang dirancang untuk
                            membantu orangtua dan guru dalam mengoptimalkan
                            intervensi bagi anak berkebutuhan khusus secara
                            inklusif dan profesional.
                        </p>
                        <Link
                            :href="register()"
                            class="inline-flex items-center justify-center rounded-xl bg-purple-600 px-8 py-4 text-base font-semibold text-white shadow-lg transition-all hover:bg-purple-700 hover:shadow-purple-200"
                        >
                            Coba Layanan Sekarang
                        </Link>
                    </div>
                    <div
                        class="relative overflow-hidden rounded-[40px] shadow-2xl"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=2070&auto=format&fit=crop"
                            alt="Pendidikan Inklusif"
                            class="h-full w-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </section>

        <section id="service" class="py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-16 text-center">
                    <h2
                        class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white"
                    >
                        Layanan Kami
                    </h2>
                </div>

                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div
                        class="relative order-2 overflow-hidden rounded-[40px] shadow-2xl lg:order-1"
                    >
                        <div
                            v-show="activeServiceTab === 0"
                            class="transition-opacity duration-500"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1588072432836-e10032774350?q=80&w=2072&auto=format&fit=crop"
                                alt="Asesmen Anak"
                                class="aspect-video w-full bg-gray-100 object-cover"
                            />
                            <div class="bg-white p-6 dark:bg-gray-800">
                                <p class="text-gray-600 dark:text-gray-300">
                                    Lakukan booking asesmen anak melalui website
                                    kami untuk mendapatkan pemahaman mendalam
                                    tentang kebutuhan dan potensi anak Anda.
                                </p>
                            </div>
                        </div>
                        <div
                            v-show="activeServiceTab === 1"
                            class="transition-opacity duration-500"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1606092195730-5d1460981b05?q=80&w=2070&auto=format&fit=crop"
                                alt="Pendampingan Belajar"
                                class="aspect-video w-full bg-gray-100 object-cover"
                            />
                            <div class="bg-white p-6 dark:bg-gray-800">
                                <p class="text-gray-600 dark:text-gray-300">
                                    Pesan guru bimbel terbaik melalui website
                                    kami dan pilih dari profil guru yang
                                    tersedia untuk membantu anak Anda belajar
                                    lebih efektif.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="order-1 lg:order-2">
                        <h2
                            class="mb-4 text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            Intervensi
                        </h2>
                        <p class="mb-8 text-gray-600 dark:text-gray-400">
                            Kami membantu Anda dalam mengembangkan potensi anak
                            Anda melalui intervensi belajar yang berkualitas.
                            Serta melakukan pendampingan dalam belajar.
                        </p>

                        <div class="flex flex-col space-y-4">
                            <button
                                @click="activeServiceTab = 0"
                                :class="[
                                    'flex items-center gap-4 rounded-2xl border-2 p-4 text-left transition-all',
                                    activeServiceTab === 0
                                        ? 'border-purple-600 bg-purple-50 dark:bg-purple-900/20'
                                        : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-900',
                                ]"
                            >
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white shadow-sm dark:bg-gray-800"
                                >
                                    <svg
                                        class="h-6 w-6 text-purple-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                        ></path>
                                    </svg>
                                </div>
                                <span
                                    :class="[
                                        'text-lg font-semibold',
                                        activeServiceTab === 0
                                            ? 'text-purple-700 dark:text-purple-300'
                                            : 'text-gray-700 dark:text-gray-300',
                                    ]"
                                    >Asesmen Anak</span
                                >
                            </button>

                            <button
                                @click="activeServiceTab = 1"
                                :class="[
                                    'flex items-center gap-4 rounded-2xl border-2 p-4 text-left transition-all',
                                    activeServiceTab === 1
                                        ? 'border-purple-600 bg-purple-50 dark:bg-purple-900/20'
                                        : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-900',
                                ]"
                            >
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white shadow-sm dark:bg-gray-800"
                                >
                                    <svg
                                        class="h-6 w-6 text-purple-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                        ></path>
                                    </svg>
                                </div>
                                <span
                                    :class="[
                                        'text-lg font-semibold',
                                        activeServiceTab === 1
                                            ? 'text-purple-700 dark:text-purple-300'
                                            : 'text-gray-700 dark:text-gray-300',
                                    ]"
                                    >Pendampingan Belajar Anak</span
                                >
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-24 grid items-center gap-12 lg:grid-cols-2">
                    <div>
                        <h2
                            class="mb-4 text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            Pelatihan
                        </h2>
                        <p class="mb-6 text-gray-600 dark:text-gray-400">
                            Difafriends membantu Anda dalam mengembangkan
                            keterampilan untuk menjadi fasilitator yang baik
                            bagi anak difabel dengan melatih dan memberikan
                            keterampilan yang dibutuhkan.
                        </p>
                        <ul class="space-y-4">
                            <li
                                class="flex items-center gap-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                <svg
                                    class="h-6 w-6 shrink-0 text-purple-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                <a
                                    href="/pelatihan-guru"
                                    class="transition-colors hover:text-purple-600"
                                    >Pelatihan Guru</a
                                >
                            </li>
                            <li
                                class="flex items-center gap-3 font-medium text-gray-700 dark:text-gray-300"
                            >
                                <svg
                                    class="h-6 w-6 shrink-0 text-purple-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                <a
                                    href="/pelatihan-ortu"
                                    class="transition-colors hover:text-purple-600"
                                    >Pelatihan Orang Tua</a
                                >
                            </li>
                        </ul>
                    </div>
                    <div
                        class="relative overflow-hidden rounded-[40px] shadow-xl"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070&auto=format&fit=crop"
                            alt="Pelatihan Image"
                            class="aspect-[4/3] w-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ── KELAS UNGGULAN (Dynamic dari Vue) ────────────────────────────── -->
        <section
            v-if="featuredCourses.length > 0"
            class="bg-white py-20 dark:bg-gray-800"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-12 flex items-end justify-between">
                    <div>
                        <h2
                            class="mb-4 text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            Kelas Unggulan
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Kelas terpopuler yang dipilih oleh ribuan orang tua.
                        </p>
                    </div>
                    <a
                        href="/courses"
                        class="hidden items-center gap-1 font-medium text-purple-600 hover:text-purple-700 md:flex"
                        >Lihat semua &rarr;</a
                    >
                </div>
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="course in featuredCourses"
                        :key="course.id"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all hover:shadow-xl dark:border-gray-800 dark:bg-gray-950"
                    >
                        <div
                            class="relative aspect-video overflow-hidden bg-gray-100"
                        >
                            <img
                                v-if="course.thumbnail"
                                :src="`/storage/${course.thumbnail}`"
                                :alt="course.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <span
                                class="absolute top-4 left-4 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-purple-700 shadow-sm backdrop-blur"
                            >
                                {{ course.category.name }}
                            </span>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <h3
                                class="mb-2 line-clamp-2 text-xl font-bold text-gray-900 transition-colors group-hover:text-purple-600 dark:text-white"
                            >
                                {{ course.title }}
                            </h3>
                            <p
                                class="mb-4 text-sm text-gray-500 dark:text-gray-400"
                            >
                                Oleh {{ course.instructor.first_name }}
                                {{ course.instructor.last_name }}
                            </p>
                            <div
                                class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-800"
                            >
                                <div>
                                    <span
                                        v-if="course.discount_price"
                                        class="text-lg font-extrabold text-purple-600"
                                        >{{
                                            formatPrice(course.discount_price)
                                        }}</span
                                    >
                                    <span
                                        v-else
                                        class="text-lg font-extrabold text-purple-600"
                                        >{{
                                            course.price === 0
                                                ? 'Gratis'
                                                : formatPrice(course.price)
                                        }}</span
                                    >
                                    <span
                                        v-if="course.discount_price"
                                        class="ml-2 text-sm text-gray-400 line-through"
                                        >{{ formatPrice(course.price) }}</span
                                    >
                                </div>
                                <span
                                    class="text-sm font-medium text-gray-500"
                                    >{{
                                        formatDuration(course.duration_minutes)
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── PEMESANAN TENTOR (Statik dari HTML) ───────────────────────── -->
        <section class="bg-slate-100 py-20 dark:bg-gray-950">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2
                    class="mb-12 text-center text-3xl font-bold text-gray-900 dark:text-white"
                >
                    Pemesanan Tentor
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Looping Data Companion -->
                    <div
                        v-for="companion in companions"
                        :key="companion.id"
                        class="flex flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                    >
                        <!-- Foto Tentor (Dengan fallback jika belum upload foto) -->
                        <img
                            v-if="companion.photo"
                            :src="companion.photo"
                            :alt="companion.first_name"
                            class="mb-4 h-32 w-full rounded-xl bg-gray-100 object-cover"
                        />
                        <div
                            v-else
                            class="mb-4 flex h-32 w-full items-center justify-center rounded-xl bg-gray-200 text-4xl font-bold text-gray-400 dark:bg-gray-700"
                        >
                            {{ companion.first_name.charAt(0) }}
                        </div>

                        <div class="mb-4 flex items-center justify-between">
                            <div
                                class="flex items-center gap-1 text-yellow-400"
                            >
                                <svg
                                    class="h-4 w-4 fill-current"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    ></path>
                                </svg>
                                <span
                                    class="ml-1 text-xs font-bold text-gray-700 dark:text-gray-300"
                                >
                                    5.0 (32 Ulasan)
                                </span>
                            </div>
                        </div>

                        <!-- Nama Tentor -->
                        <h3
                            class="mb-2 text-xl font-bold text-gray-900 dark:text-white"
                        >
                            {{ companion.first_name }} {{ companion.last_name }}
                        </h3>

                        <!-- Bio Tentor -->
                        <p
                            class="mb-4 line-clamp-2 flex-1 text-sm text-gray-600 dark:text-gray-400"
                        >
                            {{ companion.bio }}
                        </p>

                        <!-- Harga Tentor -->
                        <p class="mb-6 font-bold text-gray-900 dark:text-white">
                            {{ formatPrice(companion.starting_price) }} / Jam
                        </p>

                        <!-- Tombol WA Dinamis -->
                        <a
                            :href="`https://wa.me/6285159540559?text=Saya%20tertarik%20buat%20diskusi%20sama%20kak%20${companion.first_name}%20dong%20..!`"
                            target="_blank"
                            class="block w-full rounded-xl bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white transition-colors hover:bg-gray-800"
                        >
                            Dapatkan 1 Sesi Gratis
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── ARTIKEL ────────────────────────────────────────────────── -->
        <section id="article" class="bg-white py-20 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2
                    class="mb-10 text-3xl font-bold text-gray-800 dark:text-white"
                >
                    Artikel
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Card 1 -->
                    <div
                        class="flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1532&auto=format&fit=crop"
                            class="h-48 w-full object-cover"
                        />
                        <div class="flex flex-1 flex-col p-6">
                            <span
                                class="mb-3 inline-block self-start rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700"
                                >Pendidikan</span
                            >
                            <h3
                                class="mb-2 text-lg leading-snug font-bold text-gray-900 dark:text-white"
                            >
                                <a
                                    href="https://www.kompasiana.com/annisna/669f747134777c6a09312a42/inklusivitas-dalam-pendidikan-merayakan-hari-anak-nasional-dengan-memperjuangkan-hak-anak-berkebutuhan-khusus"
                                    target="_blank"
                                    class="transition-colors hover:text-purple-600"
                                >
                                    Inklusivitas dalam Pendidikan: Merayakan
                                    Hari Anak Nasional
                                </a>
                            </h3>
                            <p class="mb-6 flex-1 text-sm text-gray-500">
                                Hari Anak Nasional merupakan momentum untuk
                                memperjuangkan hak anak berkebutuhan khusus...
                            </p>
                            <div
                                class="mt-auto flex items-center gap-4 text-xs text-gray-400"
                            >
                                <span>21 Sep, 2020</span>
                                <span>&bull;</span>
                                <span>10 Min Read</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div
                        class="flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1529245814698-dd66c442bfef?q=80&w=1470&auto=format&fit=crop"
                            class="h-48 w-full object-cover"
                        />
                        <div class="flex flex-1 flex-col p-6">
                            <span
                                class="mb-3 inline-block self-start rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700"
                                >Pendidikan</span
                            >
                            <h3
                                class="mb-2 text-lg leading-snug font-bold text-gray-900 dark:text-white"
                            >
                                <a
                                    href="https://www.kompasiana.com/annisna/647f446c08a8b52f4b2ad622/penting-pendidikan-seks-bagi-anak-berkebutuhan-khusus"
                                    target="_blank"
                                    class="transition-colors hover:text-purple-600"
                                >
                                    Penting! Pendidikan Seks bagi Anak
                                    Berkebutuhan Khusus
                                </a>
                            </h3>
                            <p class="mb-6 flex-1 text-sm text-gray-500">
                                Kasus pemerkosaan remaja 15 tahun di Sulawesi
                                Tengah menjadi peringatan pentingnya edukasi...
                            </p>
                            <div
                                class="mt-auto flex items-center gap-4 text-xs text-gray-400"
                            >
                                <span>21 Sep, 2020</span>
                                <span>&bull;</span>
                                <span>10 Min Read</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div
                        class="flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950"
                    >
                        <img
                            src="/images/posts/post-13.png"
                            class="h-48 w-full bg-gray-100 object-cover"
                        />
                        <div class="flex flex-1 flex-col p-6">
                            <span
                                class="mb-3 inline-block self-start rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700"
                                >Pendidikan</span
                            >
                            <h3
                                class="mb-2 text-lg leading-snug font-bold text-gray-900 dark:text-white"
                            >
                                <a
                                    href="https://www.kompasiana.com/annisna/647732868221990a672c5493/activity-daily-living-bagi-"
                                    target="_blank"
                                    class="transition-colors hover:text-purple-600"
                                >
                                    Activity Daily Living bagi Anak Berkebutuhan
                                    Khusus
                                </a>
                            </h3>
                            <p class="mb-6 flex-1 text-sm text-gray-500">
                                Activities Daily Living (ADLs) adalah sekelompok
                                aktivitas rutinitas harian yang sangat
                                krusial...
                            </p>
                            <div
                                class="mt-auto flex items-center gap-4 text-xs text-gray-400"
                            >
                                <span>21 Sep, 2020</span>
                                <span>&bull;</span>
                                <span>10 Min Read</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CTA BERLANGGANAN ──────────────────────────────────────── -->
        <section class="px-4 py-16">
            <div
                class="mx-auto max-w-7xl rounded-[40px] bg-gradient-to-r from-purple-600 to-indigo-700 px-8 py-16 text-center text-white shadow-2xl md:px-16 lg:text-left"
            >
                <div class="grid items-center gap-8 lg:grid-cols-2">
                    <div>
                        <h2 class="mb-6 text-4xl font-extrabold">
                            Daftar Langganan Premium
                        </h2>
                        <a
                            href="https://wa.me/6285159540559?text=Saya%20tertarik%20buat%20coba%20layanan%20Difafriends%20dong%20kak..!"
                            target="_blank"
                            class="inline-block rounded-xl bg-white px-8 py-4 font-bold text-purple-600 transition-all hover:bg-gray-100 hover:shadow-lg"
                        >
                            Mulai Berlangganan
                        </a>
                    </div>
                    <div>
                        <p class="text-lg leading-relaxed text-purple-100">
                            Ingin memberikan yang terbaik untuk perkembangan
                            anak Anda? Berlangganan layanan premium kami dan
                            nikmati berbagai fasilitas eksklusif yang dirancang
                            untuk mendukung guru dan siswa, termasuk siswa
                            disabilitas.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── ANGGOTA TIM ────────────────────────────────────────────── -->
        <section class="bg-gray-50 py-20 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto mb-16 max-w-2xl text-center">
                    <h2
                        class="mb-4 text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        Kenali Kami Lebih Dekat
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Kami adalah tim yang berdedikasi untuk memberikan yang
                        terbaik. Mari berkenalan dengan para ahli di balik
                        kesuksesan kami.
                    </p>
                </div>

                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="flex flex-col items-center text-center">
                        <img
                            src="/images/users/avatar-1.png"
                            alt="Danang Pradana"
                            class="mb-6 h-32 w-32 rounded-full object-cover shadow-lg ring-4 ring-white dark:ring-gray-800"
                        />
                        <div
                            class="w-full rounded-2xl bg-white p-6 shadow-sm dark:bg-gray-800"
                        >
                            <h5
                                class="text-lg font-bold text-gray-900 dark:text-white"
                            >
                                Danang Pradana (CAND) M.B.A
                            </h5>
                            <p class="mt-1 text-sm font-medium text-purple-600">
                                Co-founder & COO
                            </p>
                            <p class="mt-3 text-xs text-gray-500">
                                6 tahun berpengalaman di bidang manajemen
                                kewirausahaan dan keuangan
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <img
                            src="/images/users/avatar-2.png"
                            alt="Annis Na'immatun"
                            class="mb-6 h-32 w-32 rounded-full object-cover shadow-lg ring-4 ring-white dark:ring-gray-800"
                        />
                        <div
                            class="w-full rounded-2xl bg-white p-6 shadow-sm dark:bg-gray-800"
                        >
                            <h5
                                class="text-lg font-bold text-gray-900 dark:text-white"
                            >
                                Annis Na'immatun S.P.d
                            </h5>
                            <p class="mt-1 text-sm font-medium text-purple-600">
                                Founder & CEO
                            </p>
                            <p class="mt-3 text-xs text-gray-500">
                                3 tahun berpengalaman di bidang pengelolaan
                                kelas dan pendidikan khusus
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <img
                            src="/images/users/user-3.png"
                            alt="Ilham Syabani"
                            class="mb-6 h-32 w-32 rounded-full object-cover shadow-lg ring-4 ring-white dark:ring-gray-800"
                        />
                        <div
                            class="w-full rounded-2xl bg-white p-6 shadow-sm dark:bg-gray-800"
                        >
                            <h5
                                class="text-lg font-bold text-gray-900 dark:text-white"
                            >
                                Ilham Syabani
                            </h5>
                            <p class="mt-1 text-sm font-medium text-purple-600">
                                Head of Technology
                            </p>
                            <p class="mt-3 text-xs text-gray-500">
                                Berpengalaman pengembangan teknologi
                                pembelajaran
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white px-4 py-16 dark:bg-gray-950">
            <div
                class="mx-auto max-w-7xl rounded-[40px] bg-gradient-to-r from-purple-600 to-indigo-700 px-8 py-16 text-center text-white shadow-2xl md:px-16 lg:text-left"
            >
                <div class="grid items-center gap-8 lg:grid-cols-2">
                    <div>
                        <h2 class="mb-6 text-4xl font-extrabold">
                            Daftar Langganan Premium
                        </h2>
                        <a
                            href="https://difapreneur.com/register"
                            target="_blank"
                            class="inline-block rounded-xl bg-white px-8 py-4 font-bold text-purple-600 transition-all hover:bg-gray-100 hover:shadow-lg"
                        >
                            Mulai Berlangganan
                        </a>
                    </div>
                    <div>
                        <p class="text-lg leading-relaxed text-purple-100">
                            Ingin memberikan yang terbaik untuk perkembangan
                            anak Anda? Berlangganan layanan premium kami dan
                            nikmati berbagai fasilitas eksklusif yang dirancang
                            untuk mendukung guru dan siswa, termasuk siswa
                            disabilitas.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <footer
            class="border-t border-gray-900 bg-gray-950 py-16 text-gray-400"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    class="grid gap-12 border-b border-gray-800 pb-12 md:grid-cols-2 lg:grid-cols-4"
                >
                    <div>
                        <div class="mb-6 flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-600"
                            >
                                <span class="text-sm font-bold text-white"
                                    >DF</span
                                >
                            </div>
                            <span class="text-lg font-bold text-white"
                                >DifaFriends</span
                            >
                        </div>
                        <p class="mb-6 text-sm leading-relaxed">
                            Platform pembelajaran inklusif yang dirancang untuk
                            mendukung anak berkebutuhan khusus, orang tua, dan
                            pendidik.
                        </p>
                    </div>

                    <div>
                        <h6 class="mb-6 font-bold text-white">Tautan Cepat</h6>
                        <ul class="space-y-3 text-sm">
                            <li>
                                <a
                                    href="/about"
                                    class="transition-colors hover:text-purple-400"
                                    >Tentang Kami</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#service"
                                    class="transition-colors hover:text-purple-400"
                                    >Layanan</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#article"
                                    class="transition-colors hover:text-purple-400"
                                    >Artikel</a
                                >
                            </li>
                            <li>
                                <a
                                    href="/contact"
                                    class="transition-colors hover:text-purple-400"
                                    >Kontak</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h6 class="mb-6 font-bold text-white">Kontak</h6>
                        <ul class="space-y-3 text-sm">
                            <li>Surabaya, Indonesia</li>
                            <li>difafriends@gmail.com</li>
                        </ul>
                    </div>

                    <div>
                        <h6 class="mb-6 font-bold text-white">Sosial Media</h6>
                        <div class="flex gap-4">
                            <a
                                href="#"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-800 transition-all hover:bg-purple-600 hover:text-white"
                            >
                                <svg
                                    class="h-5 w-5 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="#"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-800 transition-all hover:bg-purple-600 hover:text-white"
                            >
                                <svg
                                    class="h-5 w-5 fill-current"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="pt-8 text-center text-sm">
                    <p>
                        Designed & Developed by DifaFriends &copy;
                        {{ new Date().getFullYear() }}
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
