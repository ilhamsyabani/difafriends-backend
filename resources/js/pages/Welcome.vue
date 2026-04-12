<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { register } from '@/routes';
import { useFormatters } from '@/composables/useFormatters';

const { assetUrl } = useFormatters();

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
    articles: Array<{
        id: number;
        title: string;
        author: { first_name: string; last_name: string };
        thumbnail: string | null;
        content: string;
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

// Helper untuk inisial nama jika foto tidak ada
function getInitials(firstName: string, lastName: string): string {
    const f = firstName ? firstName.charAt(0) : '';
    const l = lastName ? lastName.charAt(0) : '';
    return (f + l).toUpperCase();
}

function stripHtml(html: string) {
    if (!html) return '';
    let tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}
</script>

<template>
    <GuestLayout>
        <Head title="Difafriends — Platform Edukasi Inklusif">
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        </Head>

        <div
            class="min-h-screen bg-white font-sans text-gray-900 dark:bg-gray-950 dark:text-gray-100"
        >
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
                                        Lakukan booking asesmen anak melalui
                                        website kami untuk mendapatkan pemahaman
                                        mendalam tentang kebutuhan dan potensi
                                        anak Anda.
                                    </p>
                                </div>
                            </div>
                            <div
                                v-show="activeServiceTab === 1"
                                class="transition-opacity duration-500"
                            >
                                <img
                                    src="https://plus.unsplash.com/premium_photo-1663126319781-f4de55c7ebd4?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt="Pendampingan Belajar"
                                    class="aspect-video w-full bg-gray-100 object-cover"
                                />
                                <div class="bg-white p-6 dark:bg-gray-800">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Pesan guru bimbel terbaik melalui
                                        website kami dan pilih dari profil guru
                                        yang tersedia untuk membantu anak Anda
                                        belajar lebih efektif.
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
                                Kami membantu Anda dalam mengembangkan potensi
                                anak Anda melalui intervensi belajar yang
                                berkualitas. Serta melakukan pendampingan dalam
                                belajar.
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
                                Kelas terpopuler yang dipilih oleh ribuan orang
                                tua.
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
                                    :src="assetUrl(course.thumbnail)"
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
                                                formatPrice(
                                                    course.discount_price,
                                                )
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
                                            >{{
                                                formatPrice(course.price)
                                            }}</span
                                        >
                                    </div>
                                    <span
                                        class="text-sm font-medium text-gray-500"
                                        >{{
                                            formatDuration(
                                                course.duration_minutes,
                                            )
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── PEMESANAN TENTOR ───────────────────────── -->
            <section class="bg-slate-100 py-20 dark:bg-gray-950">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Header Section -->
                    <div class="mb-12 flex items-end justify-between">
                        <div>
                            <h2
                                class="mb-4 text-3xl font-bold text-gray-900 dark:text-white"
                            >
                                Pemesanan Tentor
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                Pilih pendamping profesional yang siap membantu
                                belajar anak Anda.
                            </p>
                        </div>
                        <a
                            href="/companions"
                            class="hidden items-center gap-1 font-medium text-purple-600 hover:text-purple-700 md:flex"
                        >
                            Lihat semua &rarr;
                        </a>
                    </div>

                    <div
                        v-if="companions.length === 0"
                        class="py-10 text-center text-gray-500"
                    >
                        Belum ada tentor yang tersedia.
                    </div>

                    <div v-else class="grid gap-8 md:grid-cols-3">
                        <!-- Looping Data Companion -->
                        <Link
                            v-for="companion in companions"
                            :key="companion.id"
                            :href="`/companions/${companion.id}`"
                            class="group flex flex-col overflow-hidden rounded-[24px] border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-purple-200 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900 dark:hover:border-purple-800"
                        >
                            <!-- Avatar / Foto -->
                            <div
                                class="relative mb-5 h-40 w-full overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800"
                            >
                                <img
                                    v-if="companion.photo"
                                    :src="companion.photo"
                                    :alt="companion.first_name"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-5xl font-bold tracking-wider text-purple-300 dark:text-gray-600"
                                >
                                    {{
                                        getInitials(
                                            companion.first_name,
                                            companion.last_name,
                                        )
                                    }}
                                </div>

                                <!-- Badge Rating -->
                                <div
                                    class="absolute top-3 left-3 flex items-center gap-1 rounded-full bg-white/90 px-2 py-1 text-xs font-bold text-yellow-500 shadow-sm backdrop-blur dark:bg-gray-900/90"
                                >
                                    <svg
                                        class="h-3.5 w-3.5 fill-current"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                        ></path>
                                    </svg>
                                    5.0
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col">
                                <!-- Nama -->
                                <h3
                                    class="mb-1 text-lg font-bold text-gray-900 transition-colors group-hover:text-purple-600 dark:text-white dark:group-hover:text-purple-400"
                                >
                                    {{ companion.first_name }}
                                    {{ companion.last_name }}
                                </h3>

                                <!-- Lokasi -->
                                <div
                                    class="mb-4 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    <svg
                                        class="h-3.5 w-3.5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                    </svg>
                                    <span class="truncate">{{
                                        companion.city || 'Online / Remote'
                                    }}</span>
                                </div>

                                <!-- Bio -->
                                <p
                                    class="mb-6 line-clamp-2 flex-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                                >
                                    {{ companion.bio }}
                                </p>

                                <!-- Harga -->
                                <div
                                    class="mt-auto border-t border-gray-100 pt-4 dark:border-gray-800"
                                >
                                    <p
                                        class="mb-0.5 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Mulai dari
                                    </p>
                                    <p
                                        class="text-lg font-extrabold text-purple-600 dark:text-purple-400"
                                    >
                                        {{
                                            formatPrice(
                                                companion.starting_price,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- ── ARTIKEL ────────────────────────────────────────────────── -->
            <!-- ── ARTIKEL TERBARU ────────────────────────────────────────── -->
            <section id="article" class="bg-white py-20 dark:bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Header Section -->
                    <div class="mb-12 flex items-end justify-between">
                        <div>
                            <h2
                                class="mb-4 text-3xl font-bold text-gray-900 dark:text-white"
                            >
                                Artikel Terbaru
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                Wawasan dan tips seputar pendidikan anak
                                berkebutuhan khusus.
                            </p>
                        </div>
                        <a
                            href="/articles"
                            class="hidden items-center gap-1 font-medium text-purple-600 hover:text-purple-700 md:flex"
                        >
                            Lihat semua &rarr;
                        </a>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="articles?.length === 0"
                        class="py-10 text-center text-gray-500"
                    >
                        Belum ada artikel yang dipublikasikan.
                    </div>

                    <!-- Grid Artikel -->
                    <div v-else class="grid gap-8 md:grid-cols-3">
                        <Link
                            v-for="article in articles"
                            :key="article.id"
                            :href="`/articles/${article.slug}`"
                            class="group flex flex-col overflow-hidden rounded-[24px] border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-purple-200 hover:shadow-xl dark:border-gray-800 dark:bg-gray-950 dark:hover:border-purple-800"
                        >
                            <!-- Thumbnail -->
                            <div
                                class="relative aspect-[16/10] w-full overflow-hidden bg-gray-100 dark:bg-gray-800"
                            >
                                <img
                                    v-if="article.thumbnail"
                                    :src="assetUrl(article.thumbnail)"
                                    :alt="article.title"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center bg-purple-50 dark:bg-purple-900/20"
                                >
                                    <span
                                        class="text-2xl font-bold text-purple-200 dark:text-purple-900/50"
                                        >DifaFriends</span
                                    >
                                </div>
                                <span
                                    class="absolute top-4 left-4 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-purple-700 shadow-sm backdrop-blur dark:bg-gray-900/90 dark:text-purple-300"
                                >
                                    Pendidikan
                                </span>
                            </div>

                            <!-- Konten -->
                            <div class="flex flex-1 flex-col p-6">
                                <h3
                                    class="mb-3 line-clamp-2 text-xl leading-snug font-bold text-gray-900 transition-colors group-hover:text-purple-600 dark:text-white dark:group-hover:text-purple-400"
                                >
                                    {{ article.title }}
                                </h3>

                                <p
                                    class="mb-6 line-clamp-3 flex-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                                >
                                    {{ stripHtml(article.content) }}
                                </p>

                                <!-- Meta Footer -->
                                <div
                                    class="mt-auto flex items-center justify-between border-t border-gray-100 pt-5 dark:border-gray-800"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <!-- Avatar Penulis -->
                                        <div
                                            class="flex h-7 w-7 items-center justify-center rounded-full bg-purple-100 text-[10px] font-bold text-purple-600 dark:bg-purple-900/30"
                                        >
                                            {{
                                                article.author?.first_name?.charAt(
                                                    0,
                                                ) || 'A'
                                            }}
                                        </div>
                                        <span
                                            class="text-xs font-semibold text-gray-900 dark:text-gray-300"
                                        >
                                            {{ article.author?.first_name }}
                                            {{ article.author?.last_name }}
                                        </span>
                                    </div>
                                    <span
                                        class="text-[11px] font-medium text-gray-400"
                                    >
                                        {{
                                            new Date(
                                                article.created_at,
                                            ).toLocaleDateString('id-ID', {
                                                day: 'numeric',
                                                month: 'short',
                                            })
                                        }}
                                    </span>
                                </div>
                            </div>
                        </Link>
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
                                nikmati berbagai fasilitas eksklusif yang
                                dirancang untuk mendukung guru dan siswa,
                                termasuk siswa disabilitas.
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
                            Kami adalah tim yang berdedikasi untuk memberikan
                            yang terbaik. Mari berkenalan dengan para ahli di
                            balik kesuksesan kami.
                        </p>
                    </div>

                    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="flex flex-col items-center text-center">
                            <img
                                src="/img/avatar-1.png"
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
                                <p
                                    class="mt-1 text-sm font-medium text-purple-600"
                                >
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
                                src="/img/avatar-2.png"
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
                                <p
                                    class="mt-1 text-sm font-medium text-purple-600"
                                >
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
                                <p
                                    class="mt-1 text-sm font-medium text-purple-600"
                                >
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
        </div>
    </GuestLayout>
</template>
