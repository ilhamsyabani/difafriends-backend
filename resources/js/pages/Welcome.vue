<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, HandHeart, Presentation, Rocket } from 'lucide-vue-next';
import { useFormatters } from '@/composables/useFormatters';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { register } from '@/routes';
import { computed } from 'vue';

const { assetUrl, formatDate, formatPrice } = useFormatters();

defineProps<{
    categories: Array<{
        id: number;
        name: string;
        description: string;
        children: Array<{ id: number; name: string }>;
    }>;
    featuredCourses: Array<{
        id: number;
        title: string;
        slug: string;
        thumbnail: string | null;
        price: number;
        discount_price: number | null;
        duration_minutes: number;
        instructor: { first_name: string; last_name: string };
        category: { name: string };
    }>;
    companions: Array<{
        id: number;
        first_name: string;
        last_name: string;
        photo: string | null;
        bio: string;
        starting_price: number;
        city?: string;
    }>;
    articles: Array<{
        id: number;
        title: string;
        slug: string;
        author: { first_name: string; last_name: string };
        thumbnail: string | null;
        content: string;
        created_at: string;
    }>;
    galleries: Array<{
        id: number;
        filename: string;
        alt: string;
        path: string;
    }>;
}>();

const page = usePage();
const isAdmin = computed(() => (page.props.auth as any)?.user?.role === 'admin');

function getInitials(firstName: string, lastName: string): string {
    return (
        (firstName?.charAt(0) ?? '') + (lastName?.charAt(0) ?? '')
    ).toUpperCase();
}

// SSR-safe: hindari document.createElement (gagal saat Inertia render server-side)
function stripHtml(html: string): string {
    if (!html) {
        return '';
    }

    return html
        .replace(/<[^>]*>/g, ' ')
        .replace(/&nbsp;/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

const partners = [
    { name: 'Lembaga Pengelola Dana Pendidikan', logo: '/partners/lpdp.png' },
    { name: 'Badan Riset dan Inovasi Nasional', logo: '/partners/brin.png' },
    { name: 'Ayah Bunda Istimewa', logo: '/partners/ab.png' },
    { name: 'Tumbuh Pertama', logo: '/partners/tp.png' },
];

const services = [
    {
        title: 'Kelas Parenting',
        description:
            'Membangun koneksi lebih dalam antara orang tua dan anak dengan metode pengasuhan terkini.',
        icon: HandHeart,
        color: 'from-pink-500 to-rose-500',
        shadow: 'shadow-rose-100',
    },
    {
        title: 'Pelatihan Guru',
        description:
            'Transformasi metode mengajar yang lebih interaktif untuk menciptakan kelas yang hidup.',
        icon: Presentation,
        color: 'from-cyan-500 to-primary',
        shadow: 'shadow-cyan-100',
    },
    {
        title: 'Pendampingan Belajar',
        description:
            'Melejitkan potensi akademik dan karakter anak melalui pendekatan personal yang menyenangkan.',
        icon: Rocket,
        color: 'from-amber-400 to-orange-500',
        shadow: 'shadow-amber-100',
    },
];

const pageTitle =
    'Difafriends — Pendidikan Inklusif & Pendampingan Anak Berkebutuhan Khusus';
const pageDescription =
    'Platform edukasi inklusif terpercaya untuk membantu orangtua dan guru dalam mengoptimalkan perkembangan anak berkebutuhan khusus melalui pendampingan personal & profesional.';
</script>

<template>
    <GuestLayout>
        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" :content="pageDescription" />
            <meta property="og:type" content="website" />
            <meta property="og:title" :content="pageTitle" />
            <meta property="og:description" :content="pageDescription" />
            <meta property="og:locale" content="id_ID" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="pageTitle" />
            <meta name="twitter:description" :content="pageDescription" />
        </Head>

        <div class="min-h-screen bg-white font-sans text-slate-800">
            <!-- Hero Section -->
            <section
                class="relative overflow-hidden bg-white pt-20 pb-12 lg:pt-32 lg:pb-16"
            >
                <div
                    aria-hidden="true"
                    class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-primary/5 blur-3xl"
                ></div>
                <div
                    aria-hidden="true"
                    class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-secondary/5 blur-3xl"
                ></div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2"
                    >
                        <div class="text-center lg:text-left">
                            <p
                                class="mb-6 inline-flex items-center rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary"
                            >
                                <span
                                    aria-hidden="true"
                                    class="mr-2 flex h-2 w-2 rounded-full bg-primary motion-safe:animate-pulse"
                                ></span>
                                <span
                                    >Setiap anak dapat berkembang optimal</span
                                >
                            </p>
                            <h1
                                class="text-4xl font-black leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl"
                            >
                                Difafriends
                            </h1>
                            <p
                                class="mt-8 text-xl leading-relaxed text-slate-600"
                            >
                                Difafriends hadir sebagai sahabat bagi orangtua
                                dan guru untuk mendukung perkembangan optimal
                                anak berkebutuhan khusus melalui pendekatan
                                personal dan profesional.
                            </p>
                            <div
                                class="mt-10 flex flex-col items-center gap-4 sm:flex-row lg:justify-start"
                            >
                                <Link
                                    :href="register()"
                                    class="w-full rounded-2xl bg-primary px-10 py-5 text-center font-bold text-white shadow-xl shadow-primary/20 transition-all hover:translate-y-px hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none sm:w-auto"
                                >
                                    Mulai Sekarang
                                </Link>
                                <a
                                    href="#layanan"
                                    class="w-full rounded-2xl border-2 border-slate-200 bg-white px-10 py-5 text-center font-bold text-slate-700 transition-all hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none sm:w-auto"
                                >
                                    Pelajari Layanan
                                </a>
                            </div>
                        </div>
                        <div class="relative">
                            <div
                                aria-hidden="true"
                                class="absolute -inset-4 rounded-[2rem] bg-gradient-to-tr from-primary/10 to-transparent blur-2xl"
                            ></div>
                            <img
                                class="relative w-full drop-shadow-2xl"
                                src="/images/hero-img.png"
                                alt="Ilustrasi anak belajar bersama pendamping di lingkungan inklusif Difafriends"
                                fetchpriority="high"
                                decoding="async"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Services Section -->
            <section id="layanan" class="bg-slate-50 py-16 lg:py-18">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <p
                            class="text-base font-bold tracking-widest text-primary uppercase"
                        >
                            Layanan Kami
                        </p>
                        <h2
                            class="mt-4 text-3xl font-extrabold text-slate-900 lg:text-4xl"
                        >
                            Dukungan Menyeluruh untuk Buah Hati
                        </h2>
                        <div
                            aria-hidden="true"
                            class="mx-auto mt-6 h-1.5 w-24 rounded-full bg-primary/20"
                        >
                            <div
                                class="h-full w-12 rounded-full bg-primary"
                            ></div>
                        </div>
                    </div>

                    <ul class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <li
                            v-for="(service, index) in services"
                            :key="index"
                            class="group relative h-full overflow-hidden rounded-3xl border border-slate-100 bg-white p-10 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/5"
                        >
                            <div
                                aria-hidden="true"
                                :class="[
                                    'mb-10 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br shadow-lg transition-all duration-500 group-hover:scale-110 group-hover:rotate-3',
                                    service.color,
                                    service.shadow,
                                ]"
                            >
                                <component
                                    :is="service.icon"
                                    class="h-10 w-10 text-white"
                                    stroke-width="2.5"
                                />
                            </div>

                            <h3
                                class="mb-4 text-2xl font-bold text-slate-900 transition-colors group-hover:text-primary"
                            >
                                {{ service.title }}
                            </h3>
                            <p class="leading-relaxed text-slate-600">
                                {{ service.description }}
                            </p>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Categories — hanya tampil kalau ada data -->
            <!-- <section
                v-if="categories.length > 0"
                id="kategori"
                class="bg-white py-16 lg:py-20"
                aria-labelledby="kategori-title"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-12 text-center">
                        <h2
                            id="kategori-title"
                            class="text-3xl font-extrabold text-slate-900 lg:text-4xl"
                        >
                            Jelajahi Kategori
                        </h2>
                        <p class="mt-4 text-lg text-slate-600">
                            Pilih bidang yang paling sesuai dengan kebutuhan
                            anak.
                        </p>
                    </div>
                    <ul class="flex flex-wrap justify-center gap-3">
                        <li v-for="category in categories" :key="category.id">
                            <Link
                                :href="`/courses?category=${category.id}`"
                                class="inline-block rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition-all hover:border-primary hover:bg-primary hover:text-white focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none"
                            >
                                {{ category.name }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </section> -->

            <!-- Featured Courses — hanya tampil kalau ada data -->
            <!-- <section
                v-if="featuredCourses.length > 0"
                id="kelas"
                class="bg-slate-50 py-24 lg:py-32"
                aria-labelledby="kelas-title"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 flex items-end justify-between">
                        <div>
                            <h2
                                id="kelas-title"
                                class="text-3xl font-extrabold text-slate-900 lg:text-4xl"
                            >
                                Kelas Pilihan
                            </h2>
                            <p class="mt-4 text-lg text-slate-600">
                                Kelas pilihan kami untuk mendukung tumbuh
                                kembang anak.
                            </p>
                        </div>
                        <Link
                            href="/courses"
                            class="hidden text-sm font-bold text-primary hover:underline focus-visible:underline md:block"
                        >
                            Lihat Semua
                        </Link>
                    </div>

                    <ul
                        class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <li
                            v-for="course in featuredCourses"
                            :key="course.id"
                            class="group flex flex-col overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm transition-all hover:shadow-xl hover:shadow-primary/5"
                        >
                            <div
                                class="relative aspect-video overflow-hidden bg-slate-100"
                            >
                                <img
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    :src="
                                        course.thumbnail
                                            ? assetUrl(course.thumbnail)
                                            : '/images/placeholder-image.jpg'
                                    "
                                    :alt="`Thumbnail kelas: ${course.title}`"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <span
                                    class="mb-3 self-start rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary"
                                >
                                    {{ course.category.name }}
                                </span>
                                <h3
                                    class="mb-2 text-lg leading-snug font-bold text-slate-900"
                                >
                                    <Link
                                        :href="`/courses/${course.slug}`"
                                        class="line-clamp-2 transition-colors hover:text-primary focus-visible:text-primary focus-visible:outline-none"
                                    >
                                        {{ course.title }}
                                    </Link>
                                </h3>
                                <p class="mb-4 text-sm text-slate-500">
                                    oleh {{ course.instructor.first_name }}
                                    {{ course.instructor.last_name }}
                                </p>
                                <div
                                    class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4"
                                >
                                    <p class="text-lg font-bold text-primary">
                                        {{
                                            formatPrice(
                                                course.discount_price ??
                                                    course.price,
                                            )
                                        }}
                                    </p>
                                    <Link
                                        :href="`/courses/${course.slug}`"
                                        class="text-sm font-bold text-primary hover:underline focus-visible:underline focus-visible:outline-none"
                                    >
                                        Detail
                                        <span class="sr-only"
                                            >kelas {{ course.title }}</span
                                        >
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section> -->

            <!-- Workshop Gallery -->
            <section
                id="pelatihan"
                class="bg-slate-100 py-24 lg:py-32"
                aria-labelledby="galeri-title"
            >
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div
                        class="mb-16 flex flex-col items-center justify-between gap-6 lg:flex-row lg:items-end"
                    >
                        <div class="text-center lg:text-left">
                            <div
                                class="mb-4 flex items-center justify-center gap-2 lg:justify-start"
                            >
                                <span
                                    aria-hidden="true"
                                    class="h-px w-8 bg-primary"
                                ></span>
                                <span
                                    class="text-sm font-bold tracking-[0.2em] text-primary uppercase"
                                >
                                    Dokumentasi
                                </span>
                            </div>
                            <h2
                                id="galeri-title"
                                class="text-3xl font-extrabold text-slate-900 lg:text-4xl"
                            >
                                Galeri
                                <span class="text-primary">Workshop</span>
                            </h2>
                        </div>
                        <Link
                            v-if="isAdmin"
                            href="/admin/gallery"
                            class="text-sm font-medium text-primary hover:underline"
                        >
                            Kelola Galeri →
                        </Link>
                    </div>

                    <!-- Real Data from Gallery -->
                    <ul
                        v-if="galleries && galleries.length > 0"
                        class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <li
                            v-for="gallery in galleries"
                            :key="gallery.id"
                            class="group relative aspect-[4/3] overflow-hidden rounded-[2rem] bg-slate-100 shadow-xl shadow-slate-200/50"
                        >
                            <img
                                class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                :src="assetUrl(gallery.path)"
                                :alt="gallery.alt"
                                loading="lazy"
                                decoding="async"
                            />
                            <figcaption
                                class="absolute right-0 bottom-0 left-0 bg-gradient-to-t from-slate-900/90 to-transparent p-6 translate-y-2 opacity-0 transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100"
                            >
                                <p class="text-lg font-bold text-white">
                                    {{ gallery.alt }}
                                </p>
                            </figcaption>
                        </li>
                    </ul>

                    <!-- Fallback placeholder if no galleries yet -->
                    <ul v-else class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <li
                            class="group relative aspect-[4/3] overflow-hidden rounded-[2rem] bg-slate-100 shadow-xl shadow-slate-200/50"
                        >
                            <img
                                class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                src="/images/workshop/workshop-1.png"
                                alt="Pelatihan Guru Inklusif Difafriends"
                                loading="lazy"
                                decoding="async"
                            />
                            <figcaption
                                class="absolute right-0 bottom-0 left-0 bg-gradient-to-t from-slate-900/90 to-transparent p-6"
                            >
                                <p class="text-lg font-bold text-white">
                                    Pelatihan Guru Inklusif
                                </p>
                            </figcaption>
                        </li>
                        <li
                            class="group relative aspect-[4/3] overflow-hidden rounded-[2rem] bg-slate-100 shadow-xl shadow-slate-200/50"
                        >
                            <img
                                class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                src="/images/workshop/workshop-2.png"
                                alt="Seminar Parenting ABK Difafriends"
                                loading="lazy"
                                decoding="async"
                            />
                            <figcaption
                                class="absolute right-0 bottom-0 left-0 bg-gradient-to-t from-slate-900/90 to-transparent p-6"
                            >
                                <p class="text-lg font-bold text-white">
                                    Seminar Parenting ABK
                                </p>
                            </figcaption>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Articles -->
            <section
                v-if="articles.length > 0"
                id="article"
                class="bg-white py-24 lg:py-32"
                aria-labelledby="artikel-title"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 flex items-end justify-between">
                        <div>
                            <h2
                                id="artikel-title"
                                class="text-3xl font-extrabold text-slate-900 lg:text-4xl"
                            >
                                Artikel & Wawasan
                            </h2>
                            <p class="mt-4 text-lg text-slate-600">
                                Informasi terkini seputar dunia pendidikan anak
                                berkebutuhan khusus.
                            </p>
                        </div>
                        <Link
                            href="/articles"
                            class="hidden text-sm font-bold text-primary hover:underline focus-visible:underline md:block"
                        >
                            Lihat Blog
                        </Link>
                    </div>

                    <ul
                        class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <li
                            v-for="article in articles"
                            :key="article.id"
                            class="group flex flex-col overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm transition-all hover:shadow-xl hover:shadow-primary/5"
                        >
                            <div
                                class="relative aspect-video overflow-hidden bg-slate-100"
                            >
                                <img
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    :src="
                                        article.thumbnail
                                            ? assetUrl(article.thumbnail)
                                            : '/images/placeholder-image.jpg'
                                    "
                                    :alt="`Ilustrasi artikel: ${article.title}`"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                            <div class="flex flex-1 flex-col p-8">
                                <span
                                    class="mb-3 text-xs font-bold tracking-widest text-primary uppercase"
                                >
                                    Wawasan
                                </span>
                                <h3
                                    class="mb-4 text-xl leading-snug font-bold text-slate-900"
                                >
                                    <Link
                                        :href="`/articles/${article.slug}`"
                                        class="line-clamp-2 transition-colors hover:text-primary focus-visible:text-primary focus-visible:outline-none"
                                    >
                                        {{ article.title }}
                                    </Link>
                                </h3>
                                <p
                                    class="mb-8 line-clamp-3 text-sm leading-relaxed text-slate-500"
                                >
                                    {{ stripHtml(article.content) }}
                                </p>
                                <div
                                    class="mt-auto flex items-center justify-between border-t border-slate-50 pt-6"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            aria-hidden="true"
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary"
                                        >
                                            {{
                                                getInitials(
                                                    article.author.first_name,
                                                    article.author.last_name,
                                                )
                                            }}
                                        </div>
                                        <span
                                            class="text-xs font-medium text-slate-700"
                                        >
                                            {{ article.author.first_name }}
                                        </span>
                                    </div>
                                    <time
                                        :datetime="article.created_at"
                                        class="text-[10px] font-bold tracking-tighter text-slate-400 uppercase"
                                    >
                                        {{ formatDate(article.created_at) }}
                                    </time>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Companions -->
            <section
                v-if="companions.length > 0"
                class="bg-slate-50 py-24 lg:py-32"
                aria-labelledby="pendamping-title"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <h2
                            id="pendamping-title"
                            class="text-3xl font-extrabold text-slate-900 lg:text-4xl"
                        >
                            Booking Tentor Pendamping
                        </h2>
                        <p class="mt-4 text-lg text-slate-600">
                            Pilih pendamping terbaik yang berpengalaman dan
                            bersertifikasi.
                        </p>
                    </div>

                    <ul
                        class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <li
                            v-for="companion in companions"
                            :key="companion.id"
                            class="group relative overflow-hidden rounded-3xl bg-white p-4 shadow-sm transition-all hover:shadow-xl"
                        >
                            <div
                                class="relative aspect-square overflow-hidden rounded-2xl bg-slate-100"
                            >
                                <img
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    :src="
                                        companion.photo
                                            ? assetUrl(companion.photo)
                                            : '/images/tentor/tentor-1.png'
                                    "
                                    :alt="`Foto pendamping ${companion.first_name} ${companion.last_name}`"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-slate-900">
                                    <Link
                                        :href="`/companions/${companion.id}`"
                                        class="transition-colors hover:text-primary focus-visible:text-primary focus-visible:outline-none"
                                    >
                                        {{ companion.first_name }}
                                        {{ companion.last_name }}
                                    </Link>
                                </h3>
                                <p
                                    v-if="companion.city"
                                    class="mt-1 text-sm text-slate-500"
                                >
                                    {{ companion.city }}
                                </p>
                                <p
                                    class="mt-2 line-clamp-2 text-sm text-slate-500"
                                >
                                    {{ companion.bio }}
                                </p>
                                <div
                                    class="mt-6 flex items-center justify-between"
                                >
                                    <div>
                                        <p
                                            class="text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                        >
                                            Mulai Dari
                                        </p>
                                        <p
                                            class="text-lg font-bold text-primary"
                                        >
                                            {{
                                                formatPrice(
                                                    companion.starting_price,
                                                )
                                            }}<span
                                                class="text-xs font-normal text-slate-400"
                                                >/jam</span
                                            >
                                        </p>
                                    </div>
                                    <Link
                                        :href="`/companions/${companion.id}`"
                                        class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white transition-all hover:bg-primary focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none"
                                    >
                                        Booking
                                        <span class="sr-only">
                                            pendamping
                                            {{ companion.first_name }}
                                        </span>
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Partners Marquee -->
            <section
                class="border-y border-slate-100 bg-white py-16"
                aria-labelledby="mitra-title"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2
                        id="mitra-title"
                        class="mb-8 text-center text-xs font-bold tracking-[0.2em] text-slate-400 uppercase"
                    >
                        Mitra Kami
                    </h2>

                    <div class="relative flex overflow-hidden">
                        <div
                            aria-hidden="true"
                            class="pointer-events-none absolute left-0 z-10 h-full w-24 bg-gradient-to-r from-white to-transparent lg:w-40"
                        ></div>
                        <div
                            aria-hidden="true"
                            class="pointer-events-none absolute right-0 z-10 h-full w-24 bg-gradient-to-l from-white to-transparent lg:w-40"
                        ></div>

                        <ul
                            class="motion-safe:animate-marquee flex items-center gap-8 py-2 whitespace-nowrap lg:gap-12"
                        >
                            <li
                                v-for="(partner, index) in [
                                    ...partners,
                                    ...partners,
                                    ...partners,
                                    ...partners,
                                ]"
                                :key="index"
                                class="flex h-24 w-48 items-center justify-center opacity-50 grayscale transition-all hover:opacity-100 hover:grayscale-0 lg:h-28 lg:w-60"
                            >
                                <img
                                    :src="partner.logo"
                                    :alt="`Logo ${partner.name}`"
                                    :title="partner.name"
                                    class="h-full w-full object-contain"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="bg-slate-50 py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="relative overflow-hidden rounded-[2.5rem] bg-primary px-8 py-12 text-center shadow-2xl shadow-cyan-200 lg:px-16 lg:py-16"
                    >
                        <div
                            aria-hidden="true"
                            class="absolute top-0 right-0 -mt-16 -mr-16 h-48 w-48 rounded-full bg-white/10 blur-3xl"
                        ></div>
                        <div
                            aria-hidden="true"
                            class="absolute bottom-0 left-0 -mb-16 -ml-16 h-48 w-48 rounded-full bg-black/5 blur-3xl"
                        ></div>

                        <div class="relative z-10 mx-auto max-w-2xl">
                            <h2
                                class="text-2xl font-bold text-white lg:text-3xl"
                            >
                                Siap Memberikan yang Terbaik untuk Si Kecil?
                            </h2>
                            <p
                                class="lg:text-md mt-4 text-base leading-relaxed text-white"
                            >
                                Bergabunglah dengan ratusan orang tua lainnya
                                dan mulai perjalanan transformatif bersama
                                Difafriends.
                            </p>

                            <div
                                class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
                            >
                                <Link
                                    :href="register()"
                                    class="w-full rounded-xl bg-white px-8 py-3.5 text-sm font-bold text-primary shadow-lg transition-all hover:bg-cyan-50 hover:shadow-xl focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary focus-visible:outline-none active:scale-95 sm:w-auto"
                                >
                                    Daftar Sekarang
                                </Link>
                                <a
                                    href="#layanan"
                                    class="group inline-flex w-full items-center justify-center gap-2 rounded-xl border-2 border-white/30 px-8 py-3.5 text-sm font-bold text-white transition-all hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-primary focus-visible:outline-none active:scale-95 sm:w-auto"
                                >
                                    Lihat Layanan
                                    <ArrowRight
                                        aria-hidden="true"
                                        class="h-4 w-4 transition-transform group-hover:translate-x-1"
                                    />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>

<style scoped>
@keyframes marquee {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

.animate-marquee {
    animation: marquee 40s linear infinite;
}

.animate-marquee:hover {
    animation-play-state: paused;
}

@media (prefers-reduced-motion: reduce) {
    .animate-marquee {
        animation: none;
    }
}
</style>
