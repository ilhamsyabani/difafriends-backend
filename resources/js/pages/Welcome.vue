<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { HandHeart, Presentation, Rocket } from 'lucide-vue-next';
import { Image as ImageIcon, ArrowRight } from 'lucide-vue-next';
import { ref } from 'vue';
import { useFormatters } from '@/composables/useFormatters';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { register } from '@/routes';

const { assetUrl, formatDate, formatPrice } = useFormatters();

defineProps<{
    canRegister?: boolean;
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
}>();

function getInitials(firstName: string, lastName: string): string {
    return (
        (firstName?.charAt(0) ?? '') + (lastName?.charAt(0) ?? '')
    ).toUpperCase();
}

function stripHtml(html: string): string {
    if (!html) return '';
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

const stats = [
    { value: '100+', label: 'Anak Terbantu' },
    { value: '10+', label: 'Tentor Bersertifikat' },
    { value: '4', label: 'Kelas Tersedia' },
    { value: '4.9', label: 'Rating Rata-rata' },
] as const;

const partners = [
    { name: 'LPDP', logo: '/partners/lpdp.png' },
    { name: 'BRIN', logo: '/partners/brin.png' },
    { name: 'AB', logo: '/partners/ab.png' },
    { name: 'TP', logo: '/partners/tp.png' },
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
</script>

<template>
    <GuestLayout>
        <div class="min-h-screen bg-slate-50/30 font-sans text-slate-800">
            <Head>
                <title>
                    Difafriends - Pendidikan Inklusif & Pendampingan ABK
                </title>
                <meta
                    name="description"
                    content="Platform edukasi inklusif terpercaya untuk membantu orangtua dan guru dalam mengoptimalkan perkembangan anak berkebutuhan khusus."
                />
            </Head>

            <!-- Hero Section -->
            <section
                class="relative overflow-hidden bg-white pt-20 pb-12 lg:pt-32 lg:pb-16"
            >
                <div
                    class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-primary/5 blur-3xl"
                ></div>
                <div
                    class="absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-secondary/5 blur-3xl"
                ></div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2"
                    >
                        <div class="text-center lg:text-left">
                            <div
                                class="mb-6 inline-flex items-center rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary"
                            >
                                <span
                                    class="mr-2 flex h-2 w-2 animate-pulse rounded-full bg-primary"
                                ></span>
                                <span>Setiap Anak dapat bekembang optimal</span>
                            </div>
                            <h1
                                class="text-4xl leading-tight font-extrabold tracking-tight text-primary sm:text-5xl lg:text-6xl"
                            >
                                Difa<span class="text-orange-400">friends</span>
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
                                    class="w-full rounded-2xl bg-primary px-10 py-5 text-center font-bold text-white shadow-xl shadow-primary/20 transition-all hover:translate-y-px hover:bg-primary/90 sm:w-auto"
                                >
                                    Mulai Sekarang
                                </Link>
                                <a
                                    href="#layanan"
                                    class="w-full rounded-2xl border-2 border-slate-200 bg-white px-10 py-5 text-center font-bold text-slate-700 transition-all hover:bg-slate-50 sm:w-auto"
                                >
                                    Pelajari Layanan
                                </a>
                            </div>
                        </div>
                        <div class="relative">
                            <div
                                class="absolute -inset-4 rounded-[2rem] bg-gradient-to-tr from-primary/10 to-transparent blur-2xl"
                            ></div>
                            <img
                                class="relative w-full drop-shadow-2xl"
                                src="/images/hero-img.png"
                                alt="DifaFriends Hero"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->

            <!-- Services Section -->
            <section class="py-16 lg:py-18" id="layanan">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <h2
                            class="text-base font-bold tracking-widest text-primary uppercase"
                        >
                            Layanan Kami
                        </h2>
                        <p
                            class="mt-4 text-3xl font-extrabold text-slate-900 lg:text-4xl"
                        >
                            Dukungan Menyeluruh untuk Buah Hati
                        </p>
                        <div
                            class="mx-auto mt-6 h-1.5 w-24 rounded-full bg-primary/20"
                        >
                            <div
                                class="h-full w-12 rounded-full bg-primary"
                            ></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <div
                            v-for="(service, index) in services"
                            :key="index"
                            class="group relative h-full overflow-hidden rounded-3xl border border-slate-100 bg-white p-10 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/5"
                        >
                            <div
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
                            <p class="mb-8 leading-relaxed text-slate-600">
                                {{ service.description }}
                            </p>

                            <div
                                class="flex items-center text-sm font-bold tracking-widest text-primary uppercase"
                            >
                                Selengkapnya
                                <svg
                                    class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="3"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"
                                    />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Workshop Gallery -->
            <section class="bg-slate-100 py-24 lg:py-32" id="pelatihan">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <!-- Header Section -->
                    <div
                        class="mb-16 flex flex-col items-center justify-between gap-6 lg:flex-row lg:items-end"
                    >
                        <div class="text-center lg:text-left">
                            <!-- Accent Label -->
                            <div
                                class="mb-4 flex items-center justify-center gap-2 lg:justify-start"
                            >
                                <span class="h-px w-8 bg-[#0097B2]"></span>
                                <span
                                    class="text-sm font-bold tracking-[0.2em] text-[#0097B2] uppercase"
                                    >Dokumentasi</span
                                >
                            </div>
                            <h2
                                class="text-3xl font-extrabold text-slate-900 lg:text-4xl"
                            >
                                Gallery
                                <span class="text-[#0097B2]">Workshop</span>
                            </h2>
                        </div>

                        <!-- Button with Theme Color
                        <Link
                            href="/gallery"
                          class="group flex items-center gap-2 rounded-full border-2 border-[#0097B2] px-8 py-3 text-sm font-bold text-[#0097B2] shadow-lg shadow-cyan-100 transition-all hover:bg-[#0097B2] hover:text-white"
                        >
                            Lihat Semua Foto
                            <ArrowRight
                                class="h-4 w-4 transition-transform group-hover:translate-x-1"
                            />
                        </Link>
                         -->
                    </div>

                    <!-- Gallery Grid -->
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <!-- Card 1 -->
                        <div
                            class="group relative aspect-[4/3] overflow-hidden rounded-[2rem] bg-slate-100 shadow-xl shadow-slate-200/50"
                        >
                            <img
                                class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                src="/images/workshop/workshop-1.png"
                                alt="Pelatihan Guru Inklusif"
                            />
                            <!-- Gradient Overlay using Theme Color -->
                            <div
                                class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent p-8 opacity-0 transition-all duration-500 group-hover:opacity-100"
                            >
                                <div
                                    class="translate-y-4 transition-transform duration-500 group-hover:translate-y-0"
                                >
                                    <span
                                        class="mb-3 inline-block rounded-md bg-[#0097B2] px-3 py-1 text-[10px] font-bold tracking-widest text-white uppercase"
                                        >Parenting</span
                                    >
                                    <p class="text-xl font-bold text-white">
                                        Kemandirian Anak
                                    </p>
                                    <div
                                        class="mt-2 flex items-center text-sm text-slate-300"
                                    >
                                        <span class="mr-2"
                                            >📍 Yogyakarta, 2024</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div
                            class="group relative aspect-[4/3] overflow-hidden rounded-[2rem] bg-slate-100 shadow-xl shadow-slate-200/50"
                        >
                            <img
                                class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                src="/images/workshop/workshop-2.png"
                                alt="Seminar Parenting ABK"
                            />
                            <!-- Gradient Overlay -->
                            <div
                                class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent p-8 opacity-0 transition-all duration-500 group-hover:opacity-100"
                            >
                                <div
                                    class="translate-y-4 transition-transform duration-500 group-hover:translate-y-0"
                                >
                                    <span
                                        class="mb-3 inline-block rounded-md bg-[#0097B2] px-3 py-1 text-[10px] font-bold tracking-widest text-white uppercase"
                                        >Pendidikan</span
                                    >
                                    <p class="text-xl font-bold text-white">
                                        Pembelajaran
                                    </p>
                                    <div
                                        class="mt-2 flex items-center text-sm text-slate-300"
                                    >
                                        <span class="mr-2">📍 Solo, 2024</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Articles -->
            <section class="py-24 lg:py-32" id="article">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 flex items-end justify-between">
                        <div>
                            <h2
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
                            class="hidden text-sm font-bold text-primary hover:underline md:block"
                        >
                            Lihat Blog
                        </Link>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="article in articles.slice(0, 3)"
                            :key="article.id"
                            class="group flex flex-col overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm transition-all hover:shadow-xl hover:shadow-primary/5"
                        >
                            <div class="relative aspect-video overflow-hidden">
                                <img
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    :src="
                                        article.thumbnail
                                            ? assetUrl(article.thumbnail)
                                            : '/images/placeholder-image.jpg'
                                    "
                                    :alt="article.title"
                                />
                            </div>
                            <div class="flex flex-1 flex-col p-8">
                                <span
                                    class="mb-3 text-xs font-bold tracking-widest text-primary uppercase"
                                    >Wawasan</span
                                >
                                <h3
                                    class="mb-4 text-xl leading-snug font-bold text-slate-900"
                                >
                                    <Link
                                        :href="`/articles/${article.slug}`"
                                        class="line-clamp-2 transition-colors hover:text-primary"
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
                                            >{{
                                                article.author.first_name
                                            }}</span
                                        >
                                    </div>
                                    <span
                                        class="text-[10px] font-bold tracking-tighter text-slate-400 uppercase"
                                    >
                                        {{ formatDate(article.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Companions -->
            <section class="bg-slate-50 py-24 lg:py-32">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <h2
                            class="text-3xl font-extrabold text-slate-900 lg:text-4xl"
                        >
                            Booking Tentor Pendamping
                        </h2>
                        <p class="mt-4 text-lg text-slate-600">
                            Pilih pendamping terbaik yang berpengalaman dan
                            bersertifikasi.
                        </p>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="companion in companions.slice(0, 3)"
                            :key="companion.id"
                            class="group relative overflow-hidden rounded-3xl bg-white p-4 shadow-sm transition-all hover:shadow-xl"
                        >
                            <div
                                class="relative aspect-square overflow-hidden rounded-2xl"
                            >
                                <img
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    :src="
                                        companion.photo
                                            ? assetUrl(companion.photo)
                                            : '/images/tentor/tentor-1.png'
                                    "
                                    :alt="companion.first_name"
                                />
                                <div
                                    class="absolute top-4 right-4 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-slate-900 backdrop-blur-sm"
                                >
                                    ⭐ 5.0
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-slate-900">
                                    <Link
                                        :href="`/companions/${companion.id}`"
                                        class="transition-colors hover:text-primary"
                                    >
                                        {{ companion.first_name }}
                                        {{ companion.last_name }}
                                    </Link>
                                </h3>
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
                                        class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white transition-all hover:bg-primary"
                                    >
                                        Booking
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Partners Marquee -->
            <section class="border-y border-slate-100 bg-white py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p
                        class="mb-12 text-center text-xs font-bold tracking-[0.2em] text-slate-400 uppercase"
                    >
                        Mitra Kami
                    </p>

                    <div class="relative flex overflow-hidden">
                        <!-- Fade overlays -->
                        <div
                            class="pointer-events-none absolute left-0 z-10 h-full w-40 bg-gradient-to-r from-white to-transparent lg:w-40"
                        ></div>
                        <div
                            class="pointer-events-none absolute right-0 z-10 h-full w-40 bg-gradient-to-l from-white to-transparent lg:w-40"
                        ></div>

                        <!-- Infinite Scroll Content -->
                        <div
                            class="animate-marquee flex items-center gap-16 py-4 whitespace-nowrap lg:gap-24"
                        >
                            <div
                                v-for="(partner, index) in [
                                    ...partners,
                                    ...partners,
                                    ...partners,
                                    ...partners,
                                ]"
                                :key="index"
                                class="flex h-20 w-42 items-center justify-center opacity-40 grayscale transition-all hover:opacity-100 hover:grayscale-0 lg:h-20 lg:w-48"
                            >
                                <img
                                    :src="partner.logo"
                                    :alt="partner.name"
                                    class="h-full w-full object-contain"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="bg-white py-16">
                <!-- Padding luar dikurangi -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="relative overflow-hidden rounded-[2.5rem] bg-[#0097B2] px-8 py-12 text-center shadow-2xl shadow-cyan-200 lg:px-16 lg:py-16"
                    >
                        <!-- Decorative Elements - Dibuat lebih lembut -->
                        <div
                            class="absolute top-0 right-0 -mt-16 -mr-16 h-48 w-48 rounded-full bg-white/10 blur-3xl"
                        ></div>
                        <div
                            class="absolute bottom-0 left-0 -mb-16 -ml-16 h-48 w-48 rounded-full bg-black/5 blur-3xl"
                        ></div>

                        <div class="relative z-10 mx-auto max-w-2xl">
                            <h2
                                class="text-2xl font-bold text-white lg:text-3xl"
                            >
                                Siap Memberikan yang Terbaik untuk Si Kecil?
                            </h2>
                            <p
                                class="mt-4 text-base leading-relaxed text-cyan-50/90 lg:text-md"
                            >
                                Bergabunglah dengan ratusan orang tua lainnya
                                dan mulai perjalanan transformatif bersama
                                Difafriends.
                            </p>

                            <div
                                class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
                            >
                                <!-- Tombol Utama: Putih, Teks Cyan -->
                                <Link
                                    :href="register()"
                                    class="w-full rounded-xl bg-white px-8 py-3.5 text-sm font-bold text-[#0097B2] shadow-lg transition-all hover:bg-cyan-50 hover:shadow-xl active:scale-95 sm:w-auto"
                                >
                                    Daftar Sekarang
                                </Link>

                                <!-- Tombol Sekunder: Outline Putih -->
                                <button
                                    class="w-full rounded-xl border-2 border-white/30 px-8 py-3.5 text-sm font-bold text-white transition-all hover:bg-white/10 active:scale-95 sm:w-auto"
                                >
                                    Konsultasi Gratis
                                </button>
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

/* Custom transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
