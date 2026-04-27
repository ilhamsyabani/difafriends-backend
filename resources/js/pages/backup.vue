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
    if (!html) {
        return '';
    }

    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;

    return tmp.textContent || tmp.innerText || '';
}

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
                Difafriends — Layanan Intervensi Anak Berkebutuhan Khusus
            </title>
            <component
                :is="'script'"
                type="application/ld+json"
                v-html="jsonLd"
            />
        </Head>

        <section class="section banner relative">
            <div class="container">
                <div class="row items-center">
                    <div class="lg:col-6">
                        <h1 class="banner-title">
                            Layanan Intervensi Anak Berkebutuhan Khusus
                        </h1>
                        <p class="mt-6">
                            Difafriends adalah platform yang dirancang untuk
                            membantu orangtua dan guru dalam mengoptimalkan
                            intervensi bagi anak berkebutuhan khusus.
                        </p>
                        <a
                            class="btn btn-white mt-8 text-white"
                            href="https://difapreneur.com/register"
                            target="_blank"
                            >Coba Layanan</a
                        >
                    </div>
                    <div class="lg:col-6">
                        <img
                            class="w-full object-contain"
                            src="/images/hero-img.png"
                            alt="Hero Image"
                        />
                    </div>
                </div>
            </div>
        </section>
        <section class="section services" id="service">
            <div class="container">
                <div class="mx-auto mb-8 text-center lg:col-6">
                    <h2>Layanan Kami</h2>
                </div>
                <div class="tab row gx-5 items-center">
                    <div class="lg:order-2 lg:col-6">
                        <div class="tab-content">
                            <div
                                class="tab-content-panel"
                                :class="{
                                    'active block': activeServiceTab === 0,
                                    hidden: activeServiceTab !== 0,
                                }"
                            >
                                <img
                                    class="w-full rounded-[40px] object-contain"
                                    src="/images/image-1.png"
                                    alt="Asesmen Anak"
                                />
                                <p class="mt-4">
                                    Lakukan booking asesmen anak melalui website
                                    kami untuk mendapatkan pemahaman mendalam
                                    tentang kebutuhan dan potensi anak Anda.
                                </p>
                            </div>
                            <div
                                class="tab-content-panel"
                                :class="{
                                    'active block': activeServiceTab === 1,
                                    hidden: activeServiceTab !== 1,
                                }"
                            >
                                <img
                                    class="w-full rounded-[40px] object-contain"
                                    src="/images/image-1.png"
                                    alt="Pendampingan Belajar"
                                />
                                <p class="mt-4">
                                    Pesan guru bimbel terbaik melalui website
                                    kami dan pilih dari profil guru yang
                                    tersedia untuk membantu anak Anda belajar
                                    lebih efektif.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 lg:order-1 lg:col-6 lg:mt-0">
                        <div class="text-container">
                            <h2 class="lg:text-4xl">Intervensi</h2>
                            <p class="mt-4">
                                Kami membantu Anda dalam mengembangkan potensi
                                anak Anda melalui intervensi belajar yang
                                berkualitas. Serta melakukan pendapingan dalam
                                belajar.
                            </p>
                            <ul class="tab-nav mt-8 -ml-4 border-b-0">
                                <li
                                    class="tab-nav-item cursor-pointer"
                                    :class="{ active: activeServiceTab === 0 }"
                                    @click="activeServiceTab = 0"
                                >
                                    <img
                                        class="mr-3"
                                        src="/images/icons/drop.svg"
                                        alt="Asesmen Icon"
                                    />
                                    Asesmen Anak
                                </li>
                                <li
                                    class="tab-nav-item cursor-pointer"
                                    :class="{ active: activeServiceTab === 1 }"
                                    @click="activeServiceTab = 1"
                                >
                                    <img
                                        class="mr-3"
                                        src="/images/icons/brain.svg"
                                        alt="Brain Icon"
                                    />
                                    Pendampingan Belajar Anak
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row gx-5 mt-12 items-center lg:mt-0">
                    <div class="my-12 lg:col-6">
                        <div class="relative">
                            <img
                                class="w-full rounded-[40px] object-contain"
                                src="/images/service-1.jpg"
                                alt="Service 1"
                            />
                        </div>
                    </div>
                    <div class="mt-6 lg:col-6 lg:mt-0">
                        <div class="text-container">
                            <h2 class="lg:text-4xl">Pelatihan</h2>
                            <p class="mt-4">
                                Difafriends membantu Anda dalam mengembangkan
                                keterampilan untuk menjadi fasilitator yang baik
                                bagi Warga difabel dengan melatih dan memberikan
                                kertampilan yang dibutuhkan.
                            </p>
                            <ul class="text-dark mt-6 lg:-ml-4">
                                <li class="mb-2 flex items-center rounded px-4">
                                    <img
                                        class="mr-3"
                                        src="/images/icons/checkmark-circle.svg"
                                        alt="Check Icon"
                                    />
                                    <Link href="/courses">Pelatihan Guru</Link>
                                </li>
                                <li class="mb-2 flex items-center rounded px-4">
                                    <img
                                        class="mr-3"
                                        src="/images/icons/checkmark-circle.svg"
                                        alt="Check Icon"
                                    />
                                    <Link href="/courses"
                                        >Pelatihan Orang Tua</Link
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row gx-5 mt-12 items-center lg:mt-0">
                    <div class="lg:order-2 lg:col-7">
                        <div class="video pr-9 pb-5">
                            <div
                                class="video-thumbnail relative overflow-hidden rounded-2xl"
                            >
                                <img
                                    class="w-full object-contain"
                                    src="/images/intro-thumbnail.png"
                                    alt="Intro Thumbnail"
                                />
                                <button
                                    class="video-play-btn absolute inset-0 m-auto"
                                ></button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 lg:order-1 lg:col-5 lg:mt-0">
                        <div class="text-container">
                            <h2 class="lg:text-4xl">Langganan Akun Premium</h2>
                            <p class="mt-4">
                                Nikmati fasilitas eksklusif dengan akun premium,
                                termasuk akses ke artikel premium, modul
                                pembelajaran, dan layanan konsultasi ahli.
                                Daftar sekarang untuk mendukung perjalanan
                                belajar anak Anda!
                            </p>
                            <a
                                href="https://difapreneur.com/register"
                                class="btn btn-white mt-6 text-white"
                                >Mulai Langganan</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section pt-0" id="article">
            <div class="container">
                <h2 class="mb-4">Artikel</h2>

                <div v-if="articles && articles.length > 0" class="row">
                    <div
                        v-for="article in articles.slice(0, 3)"
                        :key="article.id"
                        class="mb-8 md:col-6 lg:col-4"
                    >
                        <div class="card h-full">
                            <img
                                class="card-img h-[210px] rounded-[12px] object-cover"
                                width="335"
                                height="210"
                                :src="
                                    article.thumbnail
                                        ? assetUrl(article.thumbnail)
                                        : 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1532&auto=format&fit=crop'
                                "
                                :alt="article.title"
                            />
                            <div class="card-content flex h-full flex-col">
                                <div class="card-tags">
                                    <a class="tag" href="#">Pendidikan</a>
                                </div>
                                <h3 class="h4 card-title">
                                    <Link :href="`/articles/${article.slug}`">
                                        {{ article.title }}
                                    </Link>
                                </h3>
                                <p class="mb-4 line-clamp-3">
                                    {{ stripHtml(article.content) }}
                                </p>
                                <div class="card-footer mt-auto flex space-x-4">
                                    <span
                                        class="inline-flex items-center text-xs text-[#666]"
                                    >
                                        <svg
                                            class="mr-1.5"
                                            width="14"
                                            height="16"
                                            viewBox="0 0 14 16"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M12.5 2H11V0.375C11 0.16875 10.8313 0 10.625 0H9.375C9.16875 0 9 0.16875 9 0.375V2H5V0.375C5 0.16875 4.83125 0 4.625 0H3.375C3.16875 0 3 0.16875 3 0.375V2H1.5C0.671875 2 0 2.67188 0 3.5V14.5C0 15.3281 0.671875 16 1.5 16H12.5C13.3281 16 14 15.3281 14 14.5V3.5C14 2.67188 13.3281 2 12.5 2ZM12.3125 14.5H1.6875C1.58438 14.5 1.5 14.4156 1.5 14.3125V5H12.5V14.3125C12.5 14.4156 12.4156 14.5 12.3125 14.5Z"
                                                fill="#939393"
                                            />
                                        </svg>
                                        {{
                                            new Date(
                                                article.created_at,
                                            ).toLocaleDateString('id-ID', {
                                                day: 'numeric',
                                                month: 'short',
                                                year: 'numeric',
                                            })
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section bg-slate-200 pt-8">
            <div class="container">
                <h2 class="h2 mx-auto my-8 text-center">Pemesanan Tentor</h2>
                <div class="row">
                    <div
                        v-for="companion in companions.slice(0, 3)"
                        :key="companion.id"
                        class="mb-8 md:col-6 lg:col-4"
                    >
                        <div class="card flex h-full flex-col">
                            <img
                                class="card-img h-[210px] object-cover"
                                width="335"
                                height="210"
                                :src="
                                    companion.photo
                                        ? assetUrl(companion.photo)
                                        : '/images/tentor/tentor-1.png'
                                "
                                :alt="companion.first_name"
                            />
                            <div class="card-content flex flex-1 flex-col">
                                <div
                                    class="mt-2 mb-5 flex items-center justify-between"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            v-for="n in 5"
                                            :key="n"
                                            aria-hidden="true"
                                            class="h-5 w-5 text-yellow-300"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                            ></path>
                                        </svg>
                                        <span
                                            class="mr-2 ml-3 rounded bg-yellow-200 px-2.5 py-0.5 text-xs font-semibold"
                                            >5.0</span
                                        >
                                    </div>
                                    <p>
                                        <span class="font-bold text-slate-900"
                                            >32 Ulasan</span
                                        >
                                    </p>
                                </div>
                                <h3 class="h4 card-title">
                                    <Link :href="`/companions/${companion.id}`">
                                        {{ companion.first_name }}
                                        {{ companion.last_name }}
                                    </Link>
                                </h3>
                                <p class="line-clamp-2 flex-1">
                                    {{ companion.bio }}
                                </p>
                                <div
                                    class="mt-2 mb-5 flex items-center justify-between"
                                >
                                    <p>
                                        <span class="font-bold text-slate-900"
                                            >{{
                                                formatPrice(
                                                    companion.starting_price,
                                                )
                                            }}/Jam</span
                                        >
                                    </p>
                                </div>
                                <a
                                    :href="`https://wa.me/6285159540559?text=Saya%20tertarik%20buat%20diskusi%20sama%20kak%20${companion.first_name}%20dong%20..!`"
                                    target="_blank"
                                    class="mt-auto flex items-center justify-center rounded-md bg-slate-900 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-gray-700 focus:ring-4 focus:ring-blue-300 focus:outline-none"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="mr-2 h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                        />
                                    </svg>
                                    Dapatkan 1 Sesi Gratis
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="section" id="pelatihan">
            <div class="container">
                <div class="row justify-center text-center">
                    <div class="col-8">
                        <h2>Daftar Pelatihan</h2>
                    </div>
                </div>
                <div class="row mt-2.5">
                    <div class="md:col-6">
                        <div class="relative mt-8">
                            <img
                                class="w-full object-cover"
                                width="480"
                                height="328"
                                src="/images/workshop/workshop-1.png"
                                alt="Workshop 1"
                            />
                        </div>
                    </div>
                    <div class="md:col-6">
                        <div class="relative mt-8">
                            <img
                                class="w-full object-cover"
                                width="480"
                                height="540"
                                src="/images/workshop/workshop-2.png"
                                alt="Workshop 2"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div
                    class="bg-gradient row justify-center rounded-[40px] px-[30px] pt-16 pb-[75px]"
                >
                    <div class="lg:col-11">
                        <div class="row">
                            <div class="lg:col-7">
                                <h2 class="h1 text-white">Daftar Langganan</h2>
                                <a
                                    class="btn btn-white mt-8 text-white"
                                    href="https://wa.me/6285159540559?text=Saya%20tertarik%20buat%20coba%20layanan%20Difafriends%20dong%20kak..!"
                                    >Mulai Berlanganan</a
                                >
                            </div>
                            <div class="mt-8 lg:col-5 lg:mt-0">
                                <p class="text-white">
                                    <span
                                        >Ingin memberikan yang terbaik untuk
                                        perkembangan anak Anda? Berlangganan
                                        layanan premium
                                    </span>
                                    kami dan nikmati berbagai fasilitas
                                    eksklusif yang dirancang untuk mendukung
                                    guru dan siswa, termasuk siswa disabilitas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="mx-auto text-center lg:col-6">
                        <h2>Kenali Kami Lebih Dekat</h2>
                        <p class="mt-4">
                            Kami adalah tim yang berdedikasi untuk memberikan
                            yang terbaik. Mari berkenalan dengan para ahli di
                            balik kesuksesan kami.
                        </p>
                    </div>
                </div>
                <div class="row mt-12 justify-center">
                    <div class="row">
                        <div
                            class="mb-6 flex flex-col px-6 text-center sm:col-6 sm:items-center lg:col-4"
                        >
                            <div
                                class="member-avatar inline-flex justify-center"
                            >
                                <img
                                    class="h-28 w-28 rounded-full object-cover"
                                    src="/images/users/avatar-1.png"
                                    alt="Foto Danang Pradana"
                                />
                            </div>
                            <div
                                class="mt-6 w-full flex-1 rounded-xl bg-white px-4 py-8 shadow-lg"
                            >
                                <h5 class="font-primary">
                                    Danang Pradana (CAND) M.B.A
                                </h5>
                                <p class="mt-1.5 text-[#0097B2]">
                                    Co-founder & COO
                                </p>
                                <p class="text-[12px] text-slate-700">
                                    6 tahun berpengalaman di bidang manajemen
                                    kewirausahaan dan keuangan
                                </p>
                            </div>
                        </div>
                        <div
                            class="mb-6 flex flex-col px-6 text-center sm:col-6 sm:items-center lg:col-4"
                        >
                            <div
                                class="member-avatar inline-flex justify-center"
                            >
                                <img
                                    class="h-28 w-28 rounded-full object-cover"
                                    src="/images/users/avatar-2.png"
                                    alt="Foto Annis Na'immatun"
                                />
                            </div>
                            <div
                                class="mt-6 w-full flex-1 rounded-xl bg-white px-4 py-8 shadow-lg"
                            >
                                <h5 class="font-primary">
                                    Annis Na'immatun S.P.d
                                </h5>
                                <p class="mt-1.5 text-[#0097B2]">
                                    Founder & CEO
                                </p>
                                <p class="text-[12px] text-slate-700">
                                    3 tahun berpengalaman di bidang pengelolaan
                                    kelas dan pendidikan khusus
                                </p>
                            </div>
                        </div>
                        <div
                            class="mb-6 flex flex-col px-6 text-center sm:col-6 sm:items-center lg:col-4"
                        >
                            <div
                                class="member-avatar inline-flex justify-center"
                            >
                                <img
                                    class="h-28 w-28 rounded-full object-cover"
                                    src="/images/users/user-3.png"
                                    alt="Foto Ilham Syabani"
                                />
                            </div>
                            <div
                                class="mt-6 w-full flex-1 rounded-xl bg-white px-4 py-8 shadow-lg"
                            >
                                <h5 class="font-primary">Ilham Syabani</h5>
                                <p class="mt-1.5 text-[#0097B2]">
                                    Head of Technology
                                </p>
                                <p class="text-[12px] text-slate-700">
                                    Berpengalaman pengembangan teknologi
                                    pembelajaran
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
