<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useFormatters } from '@/composables/useFormatters';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { register } from '@/routes';
const { assetUrl } = useFormatters();

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
];

const partners = [
    'Kemendikbud',
    'Yayasan Peduli',
    'INKLUSI',
    'Sekolah Luar Biasa',
    'Terapis Indonesia',
    'Komunitas ABK',
];

const testimonials = [
    {
        name: 'Ibu Ratna Dewi',
        role: 'Orang Tua Murid, Yogyakarta',
        avatar: 'RD',
        text: 'Anak saya yang autis kini jauh lebih percaya diri. Tentor di Difafriends benar-benar paham cara mendampingi anak berkebutuhan khusus. Terima kasih banyak!',
        rating: 5,
    },
    {
        name: 'Pak Budi Santoso',
        role: 'Guru SD Inklusif, Semarang',
        avatar: 'BS',
        text: 'Pelatihan yang saya ikuti di sini mengubah cara saya mengajar. Materi sangat praktis dan instrukturnya profesional. Wajib untuk semua guru pendidikan khusus.',
        rating: 5,
    },
    {
        name: 'Ibu Sari Wulandari',
        role: 'Orang Tua Murid, Solo',
        avatar: 'SW',
        text: 'Proses asesmen anak saya sangat profesional dan hasilnya membantu kami memahami kebutuhan spesifik si kecil. Rekomendasinya konkret dan mudah diterapkan.',
        rating: 5,
    },
];

const howItWorks = [
    {
        step: '01',
        title: 'Buat Akun Gratis',
        description:
            'Daftar dalam 1 menit. Tidak perlu kartu kredit, langsung bisa eksplorasi semua layanan kami.',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>`,
    },
    {
        step: '02',
        title: 'Pilih Layanan',
        description:
            'Pilih kelas pelatihan, booking tentor pendamping, atau jadwalkan sesi asesmen anak Anda.',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>`,
    },
    {
        step: '03',
        title: 'Pantau Perkembangan',
        description:
            'Ikuti progress belajar anak secara real-time dan dapatkan laporan perkembangan berkala.',
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>`,
    },
];

const jsonLd = JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: 'DifaFriends',
    description:
        'Platform edukasi inklusif untuk anak berkebutuhan khusus. Tersedia pelatihan guru, pendampingan belajar, dan asesmen profesional.',
    url: 'https://difafriends.com',
    logo: 'https://difafriends.com/img/logo.png',
    contactPoint: {
        '@type': 'ContactPoint',
        email: 'difafriends@gmail.com',
        contactType: 'customer service',
        availableLanguage: 'Indonesian',
    },
    address: {
        '@type': 'PostalAddress',
        addressLocality: 'Klaten',
        addressCountry: 'ID',
    },
    sameAs: ['https://instagram.com/difafriends'],
});
</script>

<template>
    <GuestLayout>
        <Head>
            <title>
                DifaFriends — Platform Edukasi Anak Berkebutuhan Khusus
                Terpercaya
            </title>
            <meta
                name="description"
                content="Difafriends adalah platform edukasi inklusif terpercaya untuk anak berkebutuhan khusus (ABK). Tersedia pelatihan guru, pendampingan tentor, dan asesmen profesional di seluruh Indonesia."
            />
            <meta
                name="keywords"
                content="anak berkebutuhan khusus, ABK, pendidikan inklusif, autisme, pelatihan guru, terapi anak, difabel, asesmen anak, tentor ABK"
            />
            <meta name="robots" content="index, follow" />
            <link rel="canonical" href="https://difafriends.com" />

            <!-- Open Graph -->
            <meta property="og:type" content="website" />
            <meta property="og:url" content="https://difafriends.com" />
            <meta
                property="og:title"
                content="DifaFriends — Platform Edukasi Anak Berkebutuhan Khusus"
            />
            <meta
                property="og:description"
                content="Bantu anak Anda berkembang optimal dengan layanan asesmen, pendampingan tentor, dan pelatihan dari para profesional berpengalaman."
            />
            <meta
                property="og:image"
                content="https://difafriends.com/img/og-image.jpg"
            />
            <meta property="og:locale" content="id_ID" />
            <meta property="og:site_name" content="Difafriends" />

            <!-- Twitter Card -->
            <meta name="twitter:card" content="summary_large_image" />
            <meta
                name="twitter:title"
                content="Difafriends — Platform Edukasi ABK Terpercaya"
            />
            <meta
                name="twitter:description"
                content="Bantu anak Anda berkembang optimal dengan layanan asesmen, pendampingan tentor, dan pelatihan profesional."
            />
            <meta
                name="twitter:image"
                content="https://difafriends.com/img/og-image.jpg"
            />

            <!-- JSON-LD -->
            <!-- eslint-disable-next-line vue/no-v-html -->
            <component
                :is="'script'"
                type="application/ld+json"
                v-html="jsonLd"
            />
        </Head>

        <div
            class="min-h-screen bg-white font-sans text-gray-900 dark:bg-gray-950 dark:text-gray-100"
        >
            <!-- ── HERO ─────────────────────────────────────────────────── -->
            <section
                class="relative overflow-hidden bg-gradient-to-br from-purple-50 via-white to-orange-50/30 py-20 lg:py-32 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900"
            >
                <!-- Background decorations -->
                <div
                    class="pointer-events-none absolute inset-0 overflow-hidden"
                >
                    <div
                        class="absolute -top-40 -right-40 h-[500px] w-[500px] rounded-full bg-primary/10 blur-3xl dark:bg-primary/5"
                    ></div>
                    <div
                        class="absolute -bottom-20 -left-20 h-[400px] w-[400px] rounded-full bg-primary/10 blur-3xl dark:bg-primary/5"
                    ></div>
                </div>

                <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid items-center gap-12 lg:grid-cols-2">
                        <!-- Copy -->
                        <div class="max-w-2xl">
                            <span
                                class="mb-6 inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary dark:bg-primary/20 dark:text-primary"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-primary"
                                ></span>
                                Platform #1 Edukasi Inklusif Indonesia
                            </span>
                            <h1
                                class="mb-6 text-4xl leading-tight font-extrabold text-gray-900 lg:text-5xl xl:text-6xl dark:text-white"
                            >
                                Setiap Anak Berhak
                                <span class="text-primary">
                                    Berkembang Optimal</span
                                >
                            </h1>
                            <p
                                class="mb-8 text-lg leading-relaxed text-gray-600 dark:text-gray-400"
                            >
                                Difafriends hadir untuk orang tua dan guru yang
                                ingin memberikan yang terbaik bagi anak
                                berkebutuhan khusus — melalui asesmen
                                profesional, pendampingan tentor bersertifikat,
                                dan pelatihan berbasis bukti.
                            </p>
                            <div class="flex flex-wrap gap-4">
                                <Link
                                    :href="register()"
                                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-4 text-base font-semibold text-white shadow-lg shadow-primary/25 transition-all hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-orange-500/30"
                                >
                                    Mulai Gratis Sekarang
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6"
                                        />
                                    </svg>
                                </Link>
                                <a
                                    href="#service"
                                    class="inline-flex items-center gap-2 rounded-xl border-2 border-gray-200 bg-white px-8 py-4 text-base font-semibold text-gray-700 transition-all hover:border-primary hover:text-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    Lihat Layanan
                                </a>
                            </div>

                            <!-- Trust signals -->
                            <div class="mt-10 flex items-center gap-6">
                                <div class="flex -space-x-2">
                                    <div
                                        v-for="n in 4"
                                        :key="n"
                                        :class="`flex h-9 w-9 items-center justify-center rounded-full border-2 border-white bg-gradient-to-br text-xs font-bold text-white shadow-sm dark:border-gray-900 ${n === 1 ? 'from-purple-400 to-purple-600' : n === 2 ? 'from-orange-400 to-orange-600' : n === 3 ? 'from-pink-400 to-pink-600' : 'from-blue-400 to-blue-600'}`"
                                    >
                                        {{ ['RD', 'BS', 'SW', '+'][n - 1] }}
                                    </div>
                                </div>
                                <div
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    <span
                                        class="font-semibold text-gray-900 dark:text-white"
                                        >500+ orang tua & guru</span
                                    >
                                    sudah bergabung
                                </div>
                            </div>
                        </div>

                        <!-- Hero image -->
                        <div class="relative">
                            <div
                                class="relative overflow-hidden rounded-[40px] shadow-2xl shadow-primary/20 dark:shadow-primary/10"
                            >
                                <img
                                    src="https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=2070&auto=format&fit=crop"
                                    alt="Tentor profesional mendampingi anak berkebutuhan khusus dalam sesi belajar"
                                    class="h-full w-full object-cover"
                                    width="600"
                                    height="500"
                                    loading="eager"
                                />
                            </div>
                            <!-- Floating badge -->
                            <div
                                class="absolute -bottom-4 -left-4 rounded-2xl bg-white p-4 shadow-xl dark:bg-gray-800"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30"
                                    >
                                        <svg
                                            class="h-5 w-5 text-green-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs font-semibold text-gray-900 dark:text-white"
                                        >
                                            Tentor Terverifikasi
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Bersertifikat profesional
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="absolute -top-4 -right-4 rounded-2xl bg-white p-4 shadow-xl dark:bg-gray-800"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-2xl font-extrabold text-primary"
                                        >4.9</span
                                    >
                                    <div>
                                        <div class="flex text-yellow-400">
                                            <svg
                                                v-for="i in 5"
                                                :key="i"
                                                class="h-3.5 w-3.5 fill-current"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                                />
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            50+ ulasan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── STATS ────────────────────────────────────────────────── -->
            <section
                class="border-y border-gray-100 bg-white py-12 dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="text-center"
                        >
                            <div
                                class="text-3xl font-extrabold text-primary lg:text-4xl"
                            >
                                {{ stat.value }}
                            </div>
                            <div
                                class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                {{ stat.label }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── PARTNERS ─────────────────────────────────────────────── -->
            <section
                class="overflow-hidden bg-gray-50 py-12 dark:bg-gray-900/50"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <p
                        class="mb-8 text-center text-sm font-semibold tracking-widest text-gray-400 uppercase"
                    >
                        Dipercaya & Bermitra Dengan
                    </p>
                    <div class="relative flex overflow-hidden">
                        <!-- Marquee efek fade kiri-kanan -->
                        <div
                            class="pointer-events-none absolute left-0 z-10 h-full w-24 bg-gradient-to-r from-gray-50 dark:from-gray-900/50"
                        ></div>
                        <div
                            class="pointer-events-none absolute right-0 z-10 h-full w-24 bg-gradient-to-l from-gray-50 dark:from-gray-900/50"
                        ></div>
                        <div
                            class="flex animate-[marquee_25s_linear_infinite] items-center gap-16 whitespace-nowrap"
                        >
                            <span
                                v-for="(partner, i) in [
                                    ...partners,
                                    ...partners,
                                ]"
                                :key="`p-${i}`"
                                class="text-lg font-bold tracking-tight text-gray-300 transition-colors hover:text-gray-500 dark:text-gray-600 dark:hover:text-gray-400"
                            >
                                {{ partner }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── LAYANAN ──────────────────────────────────────────────── -->
            <section id="service" class="py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <span
                            class="mb-3 inline-block rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary dark:bg-primary/20 dark:text-primary"
                            >Layanan Kami</span
                        >
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white"
                        >
                            Solusi Lengkap untuk Tumbuh Kembang ABK
                        </h2>
                        <p
                            class="mt-4 text-lg text-gray-600 dark:text-gray-400"
                        >
                            Satu platform, semua yang dibutuhkan anak dan
                            keluarga Anda.
                        </p>
                    </div>

                    <!-- Intervensi -->
                    <div class="grid items-center gap-12 lg:grid-cols-2">
                        <div
                            class="relative order-2 overflow-hidden rounded-[40px] shadow-2xl lg:order-1"
                        >
                            <div v-show="activeServiceTab === 0">
                                <img
                                    src="https://images.unsplash.com/photo-1588072432836-e10032774350?q=80&w=2072&auto=format&fit=crop"
                                    alt="Sesi asesmen anak berkebutuhan khusus bersama psikolog profesional"
                                    class="aspect-video w-full bg-gray-100 object-cover"
                                    width="700"
                                    height="400"
                                    loading="lazy"
                                />
                                <div class="bg-white p-6 dark:bg-gray-800">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Pahami kebutuhan unik anak Anda melalui
                                        asesmen komprehensif oleh psikolog dan
                                        terapis berpengalaman. Hasil asesmen
                                        menjadi peta jalan untuk intervensi yang
                                        tepat sasaran.
                                    </p>
                                </div>
                            </div>
                            <div v-show="activeServiceTab === 1">
                                <img
                                    src="https://plus.unsplash.com/premium_photo-1663126319781-f4de55c7ebd4?q=80&w=1470&auto=format&fit=crop"
                                    alt="Tentor profesional mendampingi anak belajar secara personal"
                                    class="aspect-video w-full bg-gray-100 object-cover"
                                    width="700"
                                    height="400"
                                    loading="lazy"
                                />
                                <div class="bg-white p-6 dark:bg-gray-800">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        Pilih tentor pendamping yang memahami
                                        kebutuhan anak Anda dari ratusan profil
                                        bersertifikat. Sesi fleksibel — online
                                        maupun tatap muka, sesuai jadwal
                                        keluarga Anda.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="order-1 lg:order-2">
                            <h2
                                class="mb-3 text-3xl font-bold text-gray-900 dark:text-white"
                            >
                                Intervensi Profesional
                            </h2>
                            <p class="mb-8 text-gray-600 dark:text-gray-400">
                                Pendekatan berbasis bukti yang dirancang bersama
                                psikolog, terapis, dan pakar pendidikan khusus
                                untuk hasil terbaik bagi anak Anda.
                            </p>
                            <div class="flex flex-col space-y-4">
                                <button
                                    @click="activeServiceTab = 0"
                                    :class="[
                                        'flex items-center gap-4 rounded-2xl border-2 p-4 text-left transition-all',
                                        activeServiceTab === 0
                                            ? 'border-primary bg-primary/5 dark:bg-primary/10'
                                            : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-900',
                                    ]"
                                >
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white shadow-sm dark:bg-gray-800"
                                    >
                                        <svg
                                            class="h-6 w-6 text-primary"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <span
                                            :class="[
                                                'block text-lg font-semibold',
                                                activeServiceTab === 0
                                                    ? 'text-primary'
                                                    : 'text-gray-700 dark:text-gray-300',
                                            ]"
                                            >Asesmen Anak</span
                                        >
                                        <span class="text-sm text-gray-500"
                                            >Identifikasi kebutuhan secara
                                            mendalam</span
                                        >
                                    </div>
                                </button>
                                <button
                                    @click="activeServiceTab = 1"
                                    :class="[
                                        'flex items-center gap-4 rounded-2xl border-2 p-4 text-left transition-all',
                                        activeServiceTab === 1
                                            ? 'border-primary bg-primary/5 dark:bg-primary/10'
                                            : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-900',
                                    ]"
                                >
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white shadow-sm dark:bg-gray-800"
                                    >
                                        <svg
                                            class="h-6 w-6 text-primary"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <span
                                            :class="[
                                                'block text-lg font-semibold',
                                                activeServiceTab === 1
                                                    ? 'text-primary'
                                                    : 'text-gray-700 dark:text-gray-300',
                                            ]"
                                            >Pendampingan Belajar</span
                                        >
                                        <span class="text-sm text-gray-500"
                                            >Tentor personal bersertifikat</span
                                        >
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pelatihan -->
                    <div class="mt-24 grid items-center gap-12 lg:grid-cols-2">
                        <div>
                            <h2
                                class="mb-3 text-3xl font-bold text-gray-900 dark:text-white"
                            >
                                Pelatihan & Kelas Online
                            </h2>
                            <p class="mb-6 text-gray-600 dark:text-gray-400">
                                Tingkatkan kompetensi Anda sebagai orang tua
                                atau pendidik dengan kelas-kelas yang dibuat
                                oleh para ahli pendidikan khusus. Belajar kapan
                                saja, dari mana saja.
                            </p>
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3">
                                    <div
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5 text-primary"
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
                                    <div>
                                        <Link
                                            href="/courses"
                                            class="font-semibold text-gray-800 transition-colors hover:text-primary dark:text-gray-200"
                                            >Pelatihan untuk Guru &
                                            Pendidik</Link
                                        >
                                        <p class="mt-0.5 text-sm text-gray-500">
                                            Strategi mengajar anak berkebutuhan
                                            khusus di kelas inklusif
                                        </p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5 text-primary"
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
                                    <div>
                                        <Link
                                            href="/courses"
                                            class="font-semibold text-gray-800 transition-colors hover:text-primary dark:text-gray-200"
                                            >Panduan Praktis untuk Orang
                                            Tua</Link
                                        >
                                        <p class="mt-0.5 text-sm text-gray-500">
                                            Cara mendampingi anak ABK di rumah
                                            dengan efektif
                                        </p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/10 dark:bg-primary/20"
                                    >
                                        <svg
                                            class="h-3.5 w-3.5 text-primary"
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
                                    <div>
                                        <Link
                                            href="/courses"
                                            class="font-semibold text-gray-800 transition-colors hover:text-primary dark:text-gray-200"
                                            >Sertifikat Resmi</Link
                                        >
                                        <p class="mt-0.5 text-sm text-gray-500">
                                            Tingkatkan kredibilitas dengan
                                            sertifikat yang diakui
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div
                            class="relative overflow-hidden rounded-[40px] shadow-xl"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070&auto=format&fit=crop"
                                alt="Pelatihan online untuk guru dan orang tua anak berkebutuhan khusus"
                                class="aspect-[4/3] w-full object-cover"
                                width="700"
                                height="525"
                                loading="lazy"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── HOW IT WORKS ─────────────────────────────────────────── -->
            <section class="bg-primary/5 py-24 dark:bg-gray-900">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <span
                            class="mb-3 inline-block rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary dark:bg-primary/20 dark:text-primary"
                            >Cara Kerja</span
                        >
                        <h2
                            class="text-3xl font-bold text-gray-900 sm:text-4xl dark:text-white"
                        >
                            Mulai dalam 3 Langkah Mudah
                        </h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Tidak ada yang rumit. Langsung bisa digunakan hari
                            ini.
                        </p>
                    </div>
                    <div class="grid gap-8 md:grid-cols-3">
                        <div
                            v-for="step in howItWorks"
                            :key="step.step"
                            class="relative rounded-3xl bg-white p-8 shadow-sm dark:bg-gray-800"
                        >
                            <div
                                class="absolute -top-4 -right-4 text-7xl font-black text-primary/10 select-none dark:text-gray-700"
                            >
                                {{ step.step }}
                            </div>
                            <div
                                class="relative mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 dark:bg-primary/20"
                            >
                                <svg
                                    class="h-7 w-7 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <!-- eslint-disable-next-line vue/no-v-html -->
                                    <g v-html="step.icon" />
                                </svg>
                            </div>
                            <h3
                                class="mb-2 text-xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ step.title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ step.description }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-12 text-center">
                        <Link
                            :href="register()"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-4 font-semibold text-white shadow-lg shadow-primary/25 transition-all hover:-translate-y-0.5 hover:bg-orange-500"
                        >
                            Mulai Perjalanan Anda
                        </Link>
                    </div>
                </div>
            </section>

            <!-- ── KELAS UNGGULAN ───────────────────────────────────────── -->
            <section
                v-if="featuredCourses.length > 0"
                class="bg-white py-24 dark:bg-gray-800"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-12 flex items-end justify-between">
                        <div>
                            <span
                                class="mb-3 inline-block rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary dark:bg-primary/20 dark:text-primary"
                                >Kelas Unggulan</span
                            >
                            <h2
                                class="text-3xl font-bold text-gray-900 dark:text-white"
                            >
                                Belajar dari Para Ahli
                            </h2>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">
                                Dipilih oleh ratusan orang tua dan guru seluruh
                                Indonesia.
                            </p>
                        </div>
                        <Link
                            href="/courses"
                            class="hidden items-center gap-1 font-semibold text-primary hover:text-orange-500 md:flex"
                        >
                            Lihat semua kelas &rarr;
                        </Link>
                    </div>
                    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <Link
                            v-for="course in featuredCourses"
                            :key="course.id"
                            :href="`/courses/${course.slug}`"
                            class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all hover:-translate-y-1 hover:shadow-xl dark:border-gray-700 dark:bg-gray-900"
                        >
                            <div
                                class="relative aspect-video overflow-hidden bg-gray-100 dark:bg-gray-700"
                            >
                                <img
                                    v-if="course.thumbnail"
                                    :src="assetUrl(course.thumbnail)"
                                    :alt="`Kelas ${course.title}`"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    width="400"
                                    height="225"
                                    loading="lazy"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center bg-primary/5 dark:bg-primary/10"
                                >
                                    <svg
                                        class="h-12 w-12 text-purple-200"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                                <span
                                    class="absolute top-4 left-4 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-orange-500 shadow-sm backdrop-blur"
                                >
                                    {{ course.category.name }}
                                </span>
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <h3
                                    class="mb-2 line-clamp-2 text-lg font-bold text-gray-900 transition-colors group-hover:text-primary dark:text-white"
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
                                    class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-700"
                                >
                                    <div>
                                        <span
                                            v-if="course.discount_price"
                                            class="text-lg font-extrabold text-primary"
                                            >{{
                                                formatPrice(
                                                    course.discount_price,
                                                )
                                            }}</span
                                        >
                                        <span
                                            v-else
                                            class="text-lg font-extrabold text-primary"
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
                                        class="flex items-center gap-1 text-sm text-gray-500"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        {{
                                            formatDuration(
                                                course.duration_minutes,
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                    <div class="mt-10 text-center md:hidden">
                        <Link
                            href="/courses"
                            class="font-semibold text-primary hover:text-orange-500"
                            >Lihat semua kelas &rarr;</Link
                        >
                    </div>
                </div>
            </section>

            <!-- ── TESTIMONIALS ─────────────────────────────────────────── -->
            <section
                class="bg-gradient-to-br from-purple-600 to-purple-800 py-24"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <span
                            class="mb-3 inline-block rounded-full bg-white/20 px-4 py-1.5 text-sm font-semibold text-white"
                            >Testimoni</span
                        >
                        <h2 class="text-3xl font-bold text-white sm:text-4xl">
                            Mereka Sudah Merasakan Manfaatnya
                        </h2>
                        <p class="mt-4 text-purple-200">
                            Ribuan keluarga dan pendidik telah mempercayai
                            Difafriends.
                        </p>
                    </div>
                    <div class="grid gap-8 md:grid-cols-3">
                        <article
                            v-for="testimonial in testimonials"
                            :key="testimonial.name"
                            class="flex flex-col rounded-3xl bg-white/10 p-8 backdrop-blur-sm"
                        >
                            <div class="mb-4 flex text-yellow-400">
                                <svg
                                    v-for="i in testimonial.rating"
                                    :key="i"
                                    class="h-5 w-5 fill-current"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                    />
                                </svg>
                            </div>
                            <blockquote
                                class="mb-6 flex-1 text-sm leading-relaxed text-purple-100"
                            >
                                "{{ testimonial.text }}"
                            </blockquote>
                            <footer class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 text-sm font-bold text-white"
                                >
                                    {{ testimonial.avatar }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-white">
                                        {{ testimonial.name }}
                                    </p>
                                    <p class="text-xs text-purple-300">
                                        {{ testimonial.role }}
                                    </p>
                                </div>
                            </footer>
                        </article>
                    </div>
                </div>
            </section>

            <!-- ── TENTOR / COMPANION ───────────────────────────────────── -->
            <section v-if="false" class="bg-gray-50 py-24 dark:bg-gray-950">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-12 flex items-end justify-between">
                        <div>
                            <span
                                class="mb-3 inline-block rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary dark:bg-primary/20 dark:text-primary"
                                >Tentor Pilihan</span
                            >
                            <h2
                                class="text-3xl font-bold text-gray-900 dark:text-white"
                            >
                                Pendamping Bersertifikat untuk Anak Anda
                            </h2>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">
                                Semua tentor telah melalui seleksi ketat dan
                                pelatihan profesional.
                            </p>
                        </div>
                        <Link
                            href="/companions"
                            class="hidden items-center gap-1 font-semibold text-primary hover:text-orange-500 md:flex"
                        >
                            Lihat semua tentor &rarr;
                        </Link>
                    </div>

                    <div
                        v-if="companions.length === 0"
                        class="py-10 text-center text-gray-500"
                    >
                        Belum ada tentor yang tersedia.
                    </div>

                    <div v-else class="grid gap-8 md:grid-cols-3">
                        <Link
                            v-for="companion in companions"
                            :key="companion.id"
                            :href="`/companions/${companion.id}`"
                            class="group flex flex-col overflow-hidden rounded-[24px] border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-purple-200 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900 dark:hover:border-purple-800"
                        >
                            <div
                                class="relative mb-5 h-44 w-full overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800"
                            >
                                <img
                                    v-if="companion.photo"
                                    :src="companion.photo"
                                    :alt="`Tentor ${companion.first_name} ${companion.last_name}`"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    width="400"
                                    height="176"
                                    loading="lazy"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-5xl font-bold text-purple-200 dark:text-gray-600"
                                >
                                    {{
                                        getInitials(
                                            companion.first_name,
                                            companion.last_name,
                                        )
                                    }}
                                </div>
                                <div
                                    class="absolute top-3 left-3 flex items-center gap-1 rounded-full bg-white/90 px-2 py-1 text-xs font-bold text-yellow-500 shadow-sm backdrop-blur dark:bg-gray-900/90"
                                >
                                    <svg
                                        class="h-3.5 w-3.5 fill-current"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                        />
                                    </svg>
                                    5.0
                                </div>
                                <div
                                    class="absolute top-3 right-3 rounded-full bg-green-500 px-2 py-0.5 text-[10px] font-bold text-white"
                                >
                                    Tersedia
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col">
                                <h3
                                    class="mb-1 text-lg font-bold text-gray-900 transition-colors group-hover:text-primary dark:text-white"
                                >
                                    {{ companion.first_name }}
                                    {{ companion.last_name }}
                                </h3>
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
                                <p
                                    class="mb-6 line-clamp-2 flex-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                                >
                                    {{ companion.bio }}
                                </p>
                                <div
                                    class="mt-auto border-t border-gray-100 pt-4 dark:border-gray-800"
                                >
                                    <p class="mb-0.5 text-xs text-gray-500">
                                        Mulai dari
                                    </p>
                                    <p
                                        class="text-lg font-extrabold text-primary"
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

            <!-- ── ARTIKEL ──────────────────────────────────────────────── -->
            <section id="article" class="bg-white py-24 dark:bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-12 flex items-end justify-between">
                        <div>
                            <span
                                class="mb-3 inline-block rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary dark:bg-primary/20 dark:text-primary"
                                >Artikel</span
                            >
                            <h2
                                class="text-3xl font-bold text-gray-900 dark:text-white"
                            >
                                Wawasan & Tips Terbaru
                            </h2>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">
                                Ditulis oleh para profesional di bidang
                                pendidikan khusus.
                            </p>
                        </div>
                        <Link
                            href="/articles"
                            class="hidden items-center gap-1 font-semibold text-primary hover:text-orange-500 md:flex"
                        >
                            Lihat semua artikel &rarr;
                        </Link>
                    </div>

                    <div
                        v-if="articles?.length === 0"
                        class="py-10 text-center text-gray-500"
                    >
                        Belum ada artikel yang dipublikasikan.
                    </div>

                    <div v-else class="grid gap-8 md:grid-cols-3">
                        <Link
                            v-for="article in articles"
                            :key="article.id"
                            :href="`/articles/${article.slug}`"
                            class="group flex flex-col overflow-hidden rounded-[24px] border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-purple-200 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900 dark:hover:border-purple-800"
                        >
                            <div
                                class="relative aspect-[16/10] w-full overflow-hidden bg-gray-100 dark:bg-gray-800"
                            >
                                <img
                                    v-if="article.thumbnail"
                                    :src="assetUrl(article.thumbnail)"
                                    :alt="article.title"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    width="400"
                                    height="250"
                                    loading="lazy"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center bg-primary/5 dark:bg-primary/10"
                                >
                                    <span
                                        class="text-2xl font-bold text-purple-200"
                                        >DifaFriends</span
                                    >
                                </div>
                                <span
                                    class="absolute top-4 left-4 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-orange-500 shadow-sm backdrop-blur dark:bg-gray-900/90 dark:text-purple-300"
                                >
                                    Pendidikan
                                </span>
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <h3
                                    class="mb-3 line-clamp-2 text-xl leading-snug font-bold text-gray-900 transition-colors group-hover:text-primary dark:text-white"
                                >
                                    {{ article.title }}
                                </h3>
                                <p
                                    class="mb-6 line-clamp-3 flex-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                                >
                                    {{ stripHtml(article.content) }}
                                </p>
                                <footer
                                    class="mt-auto flex items-center justify-between border-t border-gray-100 pt-5 dark:border-gray-800"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary dark:bg-primary/20"
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
                                    <time
                                        :datetime="article.created_at"
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
                                    </time>
                                </footer>
                            </div>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- ── CTA ─────────────────────────────────────────────────── -->
            <section class="px-4 py-20">
                <div
                    class="mx-auto max-w-7xl overflow-hidden rounded-[40px] bg-gradient-to-r from-primary to-orange-500 shadow-2xl shadow-primary/20"
                >
                    <div
                        class="grid items-center gap-8 px-8 py-16 md:px-16 lg:grid-cols-2 lg:text-left"
                    >
                        <div>
                            <h2
                                class="mb-4 text-3xl leading-tight font-extrabold text-white lg:text-4xl"
                            >
                                Mulai Hari Ini.<br />Gratis, Tanpa Risiko.
                            </h2>
                            <p class="mb-8 text-lg text-orange-100">
                                Bergabunglah dengan 500+ keluarga yang sudah
                                merasakan perbedaannya. Daftar gratis dan
                                eksplorasi semua layanan kami.
                            </p>
                            <div class="flex flex-wrap gap-4">
                                <Link
                                    :href="register()"
                                    class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-4 font-bold text-primary transition-all hover:bg-gray-50 hover:shadow-lg"
                                >
                                    Daftar Gratis Sekarang
                                </Link>
                                <a
                                    href="https://wa.me/6285159540559?text=Halo%20Difafriends%2C%20saya%20ingin%20tahu%20lebih%20lanjut%20tentang%20layanan%20ini."
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 rounded-xl border-2 border-white/40 px-8 py-4 font-semibold text-white transition-all hover:border-white hover:bg-white/10"
                                >
                                    <svg
                                        class="h-5 w-5 fill-current"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                                        />
                                    </svg>
                                    Chat via WhatsApp
                                </a>
                            </div>
                        </div>
                        <div class="hidden lg:block">
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    v-for="item in [
                                        {
                                            icon: '🎓',
                                            label: '50+ Kelas Online',
                                        },
                                        {
                                            icon: '👨‍🏫',
                                            label: '120+ Tentor Aktif',
                                        },
                                        {
                                            icon: '📋',
                                            label: 'Asesmen Profesional',
                                        },
                                        {
                                            icon: '📜',
                                            label: 'Sertifikat Resmi',
                                        },
                                    ]"
                                    :key="item.label"
                                    class="flex items-center gap-3 rounded-2xl bg-white/15 p-4"
                                >
                                    <span class="text-2xl">{{
                                        item.icon
                                    }}</span>
                                    <span
                                        class="text-sm font-semibold text-white"
                                        >{{ item.label }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── TIM ─────────────────────────────────────────────────── -->
            <section class="bg-gray-50 py-24 dark:bg-gray-900">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto mb-16 max-w-2xl text-center">
                        <span
                            class="mb-3 inline-block rounded-full bg-primary/10 px-4 py-1.5 text-sm font-semibold text-primary dark:bg-primary/20 dark:text-primary"
                            >Tim Kami</span
                        >
                        <h2
                            class="text-3xl font-bold text-gray-900 dark:text-white"
                        >
                            Dibangun oleh Para Ahli yang Peduli
                        </h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Tim kami terdiri dari psikolog, pendidik khusus, dan
                            profesional teknologi yang berdedikasi untuk
                            inklusi.
                        </p>
                    </div>
                    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="member in [
                                {
                                    name: 'Danang Pradana (CAND) M.B.A',
                                    role: 'Co-founder & COO',
                                    img: '/img/avatar-1.png',
                                    bio: '6 tahun berpengalaman di bidang manajemen kewirausahaan dan keuangan',
                                },
                                {
                                    name: 'Annis Na\'immatun S.P.d',
                                    role: 'Founder & CEO',
                                    img: '/img/avatar-2.png',
                                    bio: '3 tahun berpengalaman di bidang pengelolaan kelas dan pendidikan khusus',
                                },
                                {
                                    name: 'Ilham Syabani',
                                    role: 'Head of Technology',
                                    img: '/images/users/user-3.png',
                                    bio: 'Berpengalaman mengembangkan teknologi pembelajaran inklusif',
                                },
                            ]"
                            :key="member.name"
                            class="flex flex-col items-center text-center"
                        >
                            <div
                                class="mb-5 h-28 w-28 overflow-hidden rounded-full shadow-lg ring-4 ring-white dark:ring-gray-800"
                            >
                                <img
                                    :src="member.img"
                                    :alt="member.name"
                                    class="h-full w-full object-cover"
                                    width="112"
                                    height="112"
                                    loading="lazy"
                                />
                            </div>
                            <div
                                class="w-full rounded-2xl bg-white p-6 shadow-sm dark:bg-gray-800"
                            >
                                <h3
                                    class="text-base font-bold text-gray-900 dark:text-white"
                                >
                                    {{ member.name }}
                                </h3>
                                <p
                                    class="mt-1 text-sm font-semibold text-primary"
                                >
                                    {{ member.role }}
                                </p>
                                <p
                                    class="mt-3 text-xs leading-relaxed text-gray-500"
                                >
                                    {{ member.bio }}
                                </p>
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
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}
</style>
