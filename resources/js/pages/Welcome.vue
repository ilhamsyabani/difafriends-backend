<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { onMounted } from 'vue';
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
    if (!html) {
        return '';
    }

    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;

    return tmp.textContent || tmp.innerText || '';
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars -- used in template
const stats = [
    { value: '100+', label: 'Anak Terbantu' },
    { value: '10+', label: 'Tentor Bersertifikat' },
    { value: '4', label: 'Kelas Tersedia' },
    { value: '4.9', label: 'Rating Rata-rata' },
] as const;

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

const isVideoPlaying = ref(false);
</script>

<template>
    <GuestLayout>
        <div class="min-h-screen bg-white font-sans text-slate-800">
            <Head>
                <title>
                    Difafriends — Platform Edukasi Anak Berkebutuhan Khusus
                    Terpercaya
                </title>
                <meta
                    name="description"
                    content="Platform edukasi inklusif terpercaya..."
                />
            </Head>

            <section class="relative py-20 lg:py-32">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2"
                    >
                        <div>
                            <h1
                                class="text-4xl leading-tight font-extrabold text-slate-900 lg:text-5xl"
                            >
                                Layanan Intervensi Anak Berkebutuhan Khusus
                            </h1>
                            <p class="mt-6 text-lg text-slate-600">
                                Difafriends adalah platform yang dirancang untuk
                                membantu orangtua dan guru dalam mengoptimalkan
                                intervensi bagi anak berkebutuhan khusus.
                            </p>
                            <Link
                                :href="register()"
                                class="mt-8 inline-flex items-center justify-center rounded-xl bg-[#0097B2] px-8 py-4 font-semibold text-white transition-all hover:bg-[#007b91] hover:shadow-lg"
                            >
                                Coba Layanan
                            </Link>
                        </div>
                        <div>
                            <img
                                class="w-full object-contain"
                                src="/images/hero-img.png"
                                alt="Layanan Intervensi"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-slate-50 py-24" id="service">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto mb-16 text-center lg:w-1/2">
                        <h2
                            class="text-3xl font-bold text-slate-900 lg:text-4xl"
                        >
                            Layanan Kami
                        </h2>
                    </div>

                    <div
                        class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2"
                    >
                        <div class="order-2 lg:order-2">
                            <div
                                v-show="activeServiceTab === 0"
                                class="animate-[fadeIn_0.5s_ease-in-out]"
                            >
                                <img
                                    class="w-full rounded-[40px] object-contain shadow-lg"
                                    src="/images/image-1.png"
                                    alt="Asesmen Anak"
                                />
                                <p class="mt-6 text-slate-600">
                                    Lakukan booking asesmen anak melalui website
                                    kami untuk mendapatkan pemahaman mendalam
                                    tentang kebutuhan dan potensi anak Anda.
                                </p>
                            </div>
                            <div
                                v-show="activeServiceTab === 1"
                                class="animate-[fadeIn_0.5s_ease-in-out]"
                            >
                                <img
                                    class="w-full rounded-[40px] object-contain shadow-lg"
                                    src="/images/image-1.png"
                                    alt="Pendampingan Belajar"
                                />
                                <p class="mt-6 text-slate-600">
                                    Pesan guru bimbel terbaik melalui website
                                    kami dan pilih dari profil guru yang
                                    tersedia untuk membantu anak Anda belajar
                                    lebih efektif.
                                </p>
                            </div>
                        </div>

                        <div class="order-1 lg:order-1">
                            <h2
                                class="text-3xl font-bold text-slate-900 lg:text-4xl"
                            >
                                Intervensi
                            </h2>
                            <p class="mt-4 text-slate-600">
                                Kami membantu Anda dalam mengembangkan potensi
                                anak Anda melalui intervensi belajar yang
                                berkualitas. Serta melakukan pendampingan dalam
                                belajar.
                            </p>
                            <ul class="mt-8 space-y-4">
                                <li
                                    class="flex cursor-pointer items-center rounded-xl p-4 transition-all"
                                    :class="
                                        activeServiceTab === 0
                                            ? 'bg-[#0097B2]/10 font-bold text-[#0097B2]'
                                            : 'text-slate-700 hover:bg-slate-100'
                                    "
                                    @click="activeServiceTab = 0"
                                >
                                    <img
                                        class="mr-4 h-6 w-6"
                                        src="/images/icons/drop.svg"
                                        alt="Icon"
                                    />
                                    Asesmen Anak
                                </li>
                                <li
                                    class="flex cursor-pointer items-center rounded-xl p-4 transition-all"
                                    :class="
                                        activeServiceTab === 1
                                            ? 'bg-[#0097B2]/10 font-bold text-[#0097B2]'
                                            : 'text-slate-700 hover:bg-slate-100'
                                    "
                                    @click="activeServiceTab = 1"
                                >
                                    <img
                                        class="mr-4 h-6 w-6"
                                        src="/images/icons/brain.svg"
                                        alt="Icon"
                                    />
                                    Pendampingan Belajar Anak
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div
                        class="mt-24 grid grid-cols-1 items-center gap-12 lg:grid-cols-2"
                    >
                        <div class="order-2 lg:order-1">
                            <img
                                class="w-full rounded-[40px] object-contain shadow-lg"
                                src="/images/service-1.jpg"
                                alt="Pelatihan"
                            />
                        </div>
                        <div class="order-1 lg:order-2">
                            <h2
                                class="text-3xl font-bold text-slate-900 lg:text-4xl"
                            >
                                Pelatihan
                            </h2>
                            <p class="mt-4 text-slate-600">
                                Difafriends membantu Anda dalam mengembangkan
                                keterampilan untuk menjadi fasilitator yang baik
                                bagi Warga difabel dengan melatih dan memberikan
                                keterampilan yang dibutuhkan.
                            </p>
                            <ul
                                class="mt-6 space-y-3 font-semibold text-slate-800"
                            >
                                <li class="flex items-center">
                                    <img
                                        class="mr-3 h-5 w-5"
                                        src="/images/icons/checkmark-circle.svg"
                                        alt="Check"
                                    />
                                    <Link
                                        href="/courses"
                                        class="hover:text-[#0097B2]"
                                        >Pelatihan Guru</Link
                                    >
                                </li>
                                <li class="flex items-center">
                                    <img
                                        class="mr-3 h-5 w-5"
                                        src="/images/icons/checkmark-circle.svg"
                                        alt="Check"
                                    />
                                    <Link
                                        href="/courses"
                                        class="hover:text-[#0097B2]"
                                        >Pelatihan Orang Tua</Link
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div
                        class="mt-24 grid grid-cols-1 items-center gap-12 lg:grid-cols-12"
                    >
                        <div class="order-1 lg:order-2 lg:col-span-7">
                            <div class="relative pr-8 pb-6 lg:pr-10">
                                <div
                                    class="absolute right-0 bottom-0 h-[90%] w-[90%] rounded-3xl bg-slate-200"
                                ></div>

                                <div
                                    class="relative z-10 aspect-video overflow-hidden rounded-2xl bg-slate-100 shadow-xl"
                                >
                                    <div
                                        v-if="!isVideoPlaying"
                                        class="group relative h-full w-full cursor-pointer"
                                        @click="isVideoPlaying = true"
                                    >
                                        <img
                                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                            src="/images/intro-thumbnail.png"
                                            alt="Video Intro Difafriends"
                                        />
                                        <div
                                            class="absolute inset-0 flex items-center justify-center bg-slate-900/20 transition-all duration-300 group-hover:bg-slate-900/40"
                                        >
                                            <button
                                                class="flex h-20 w-20 items-center justify-center rounded-full bg-white/95 text-[#0097B2] shadow-2xl backdrop-blur transition-transform duration-300 group-hover:scale-110 focus:ring-4 focus:ring-[#0097B2]/50 focus:outline-none"
                                            >
                                                <svg
                                                    class="ml-1.5 h-8 w-8"
                                                    fill="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div v-else class="h-full w-full">
                                        <iframe
                                            class="h-full w-full"
                                            allowfullscreen
                                            allow="autoplay"
                                            src="https://www.youtube.com/embed/g3-VxLQO7do?autoplay=1"
                                        ></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="order-2 lg:order-1 lg:col-span-5">
                            <div class="text-container">
                                <h2
                                    class="text-3xl leading-tight font-bold text-slate-900 lg:text-4xl"
                                >
                                    Langganan Akun Premium
                                </h2>
                                <p
                                    class="mt-6 text-lg leading-relaxed text-slate-600"
                                >
                                    Nikmati fasilitas eksklusif dengan akun
                                    premium, termasuk akses ke artikel premium,
                                    modul pembelajaran, dan layanan konsultasi
                                    ahli. Daftar sekarang untuk mendukung
                                    perjalanan belajar anak Anda, termasuk
                                    dukungan khusus untuk siswa disabilitas!
                                </p>
                                <a
                                    href="https://difapreneur.com/register"
                                    class="mt-8 inline-flex items-center justify-center rounded-xl bg-slate-900 px-8 py-4 text-base font-semibold text-white shadow-lg transition-all hover:-translate-y-1 hover:bg-slate-800 hover:shadow-xl focus:ring-4 focus:ring-slate-300 focus:outline-none"
                                >
                                    Mulai Langganan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-24" id="article">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 class="mb-10 text-3xl font-bold text-slate-900">
                        Artikel Terbaru
                    </h2>
                    <div
                        class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="article in articles.slice(0, 3)"
                            :key="article.id"
                            class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-transform hover:-translate-y-1 hover:shadow-lg"
                        >
                            <img
                                class="h-52 w-full object-cover"
                                :src="
                                    article.thumbnail
                                        ? assetUrl(article.thumbnail)
                                        : 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80'
                                "
                                :alt="article.title"
                            />
                            <div class="flex flex-1 flex-col p-6">
                                <span
                                    class="mb-3 text-xs font-bold tracking-wider text-[#0097B2] uppercase"
                                    >Pendidikan</span
                                >
                                <h3
                                    class="mb-3 text-xl leading-snug font-bold text-slate-900"
                                >
                                    <Link
                                        :href="`/articles/${article.slug}`"
                                        class="hover:underline"
                                        >{{ article.title }}</Link
                                    >
                                </h3>
                                <p
                                    class="mb-6 line-clamp-3 flex-1 text-sm text-slate-500"
                                >
                                    {{ stripHtml(article.content) }}
                                </p>
                                <div
                                    class="mt-auto border-t border-slate-100 pt-4 text-xs text-slate-400"
                                >
                                    {{
                                        new Date(
                                            article.created_at,
                                        ).toLocaleDateString('id-ID', {
                                            day: 'numeric',
                                            month: 'short',
                                            year: 'numeric',
                                        })
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-slate-100 py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2
                        class="mb-12 text-center text-3xl font-bold text-slate-900 lg:text-4xl"
                    >
                        Pemesanan Tentor
                    </h2>
                    <div
                        class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="companion in companions.slice(0, 3)"
                            :key="companion.id"
                            class="flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-md transition-transform hover:-translate-y-1"
                        >
                            <img
                                class="h-40 w-full object-cover"
                                :src="
                                    companion.photo
                                        ? assetUrl(companion.photo)
                                        : '/images/tentor/tentor-1.png'
                                "
                                :alt="companion.first_name"
                            />
                            <div class="flex flex-1 flex-col p-6">
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <div
                                        class="flex items-center text-yellow-400"
                                    >
                                        <svg
                                            v-for="n in 5"
                                            :key="n"
                                            class="h-5 w-5 fill-current"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                            ></path>
                                        </svg>
                                        <span
                                            class="ml-2 text-xs font-bold text-slate-800"
                                            >5.0</span
                                        >
                                    </div>
                                </div>
                                <h3
                                    class="mb-2 text-xl font-bold text-slate-900"
                                >
                                    <Link :href="`/companions/${companion.id}`"
                                        >{{ companion.first_name }}
                                        {{ companion.last_name }}</Link
                                    >
                                </h3>
                                <p
                                    class="mb-6 line-clamp-2 flex-1 text-sm text-slate-500"
                                >
                                    {{ companion.bio }}
                                </p>
                                <div
                                    class="mb-6 text-lg font-bold text-slate-900"
                                >
                                    {{ formatPrice(companion.starting_price)
                                    }}<span
                                        class="text-sm font-normal text-slate-500"
                                        >/Jam</span
                                    >
                                </div>
                                <Link
                                    :href="`/companions/${companion.id}`"
                                    class="mt-auto block w-full rounded-xl bg-slate-900 py-3 text-center font-semibold text-white transition-colors hover:bg-slate-800"
                                >
                                    Dapatkan 1 Sesi Gratis
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-24" id="pelatihan">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto mb-12 max-w-2xl text-center">
                        <h2
                            class="text-3xl font-bold text-slate-900 lg:text-4xl"
                        >
                            Gallery Workshop
                        </h2>
                    </div>

                    <div
                        class="grid grid-cols-1 items-start gap-8 md:grid-cols-2"
                    >
                        <div
                            class="relative overflow-hidden rounded-2xl shadow-lg transition-transform hover:-translate-y-1"
                        >
                            <img
                                class="w-full object-cover"
                                width="480"
                                height="328"
                                src="/images/workshop/workshop-1.png"
                                alt="Dokumentasi Workshop 1"
                            />
                        </div>
                        <div
                            class="relative overflow-hidden rounded-2xl shadow-lg transition-transform hover:-translate-y-1"
                        >
                            <img
                                class="w-full object-cover"
                                width="480"
                                height="540"
                                src="/images/workshop/workshop-2.png"
                                alt="Dokumentasi Workshop 2"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mx-auto mb-16 max-w-2xl text-center">
                        <h2
                            class="text-3xl font-bold text-slate-900 lg:text-4xl"
                        >
                            Kenali Kami Lebih Dekat
                        </h2>
                        <p class="mt-4 text-lg text-slate-600">
                            Kami adalah tim yang berdedikasi untuk memberikan
                            yang terbaik.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <div class="flex flex-col items-center text-center">
                            <img
                                class="mb-6 h-32 w-32 rounded-full object-cover shadow-lg"
                                src="/images/users/avatar-1.png"
                                alt="Danang Pradana"
                            />
                            <h5 class="text-lg font-bold text-slate-900">
                                Danang Pradana (CAND) M.B.A
                            </h5>
                            <p class="font-semibold text-[#0097B2]">
                                Co-founder & COO
                            </p>
                            <p class="mt-3 text-sm text-slate-500">
                                6 tahun berpengalaman di bidang manajemen
                                kewirausahaan dan keuangan
                            </p>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <img
                                class="mb-6 h-32 w-32 rounded-full object-cover shadow-lg"
                                src="/images/users/avatar-2.png"
                                alt="Annis Na'immatun"
                            />
                            <h5 class="text-lg font-bold text-slate-900">
                                Annis Na'immatun S.P.d
                            </h5>
                            <p class="font-semibold text-[#0097B2]">
                                Founder & CEO
                            </p>
                            <p class="mt-3 text-sm text-slate-500">
                                3 tahun berpengalaman di bidang pengelolaan
                                kelas dan pendidikan khusus
                            </p>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div
                                class="mb-6 flex h-32 w-32 items-center justify-center rounded-full bg-slate-200 shadow-lg"
                            >
                                <span class="text-3xl font-bold text-slate-400"
                                    >IS</span
                                >
                            </div>
                            <h5 class="text-lg font-bold text-slate-900">
                                Ilham Syabani
                            </h5>
                            <p class="font-semibold text-[#0097B2]">
                                Head of Technology
                            </p>
                            <p class="mt-3 text-sm text-slate-500">
                                Berpengalaman pengembangan teknologi
                                pembelajaran
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Anda bisa menaruh custom css spesifik seperti efek tab services di sini */
.tab-content-panel {
    display: none;
    animation: fadeIn 0.5s;
}
.tab-content-panel.active {
    display: block;
}
.tab-nav-item.active {
    font-weight: bold;
    color: #0097b2; /* Gunakan color primer dari tema */
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
