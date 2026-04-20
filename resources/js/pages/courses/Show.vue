<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
import { useFormatters } from '@/composables/useFormatters';
import GuestLayout from '@/layouts/GuestLayout.vue';

const { assetUrl } = useFormatters();

const props = defineProps<{
    course: {
        id: number;
        title: string;
        slug: string;
        description: string;
        thumbnail: string | null;
        preview_video: string | null;
        price: number;
        discount_price: number | null;
        duration_minutes: number;
        has_certificate: boolean;
        prerequisites: string | null;
        is_featured: boolean;
        instructor: {
            first_name: string;
            last_name: string;
            photo: string | null;
        };
        category: { name: string; slug: string };
        goals: Array<{ id: number; goal_name: string }>;
        sections: Array<{
            id: number;
            title: string;
            sort_order: number;
            lectures: Array<{
                id: number;
                title: string;
                type: string;
                video_duration: number;
                is_free_preview: boolean;
            }>;
        }>;
        reviews: Array<{
            id: number;
            rating: number;
            comment: string;
            user: {
                first_name: string;
                last_name: string;
                photo: string | null;
            };
            created_at: string;
        }>;
    };
    isEnrolled: boolean;
}>();

const activeTab = ref<'description' | 'curriculum' | 'reviews'>('description');
const expandedSections = ref<number[]>([props.course.sections[0]?.id]);
const isLoading = ref(false);

function toggleSection(id: number) {
    if (expandedSections.value.includes(id)) {
        expandedSections.value = expandedSections.value.filter((s) => s !== id);
    } else {
        expandedSections.value.push(id);
    }
}

function formatPrice(p: number): string {
    if (p === 0) {
        return 'Gratis';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(p);
}

function formatDuration(seconds: number): string {
    const m = Math.floor(seconds / 60);
    const h = Math.floor(m / 60);
    const rem = m % 60;

    return h > 0 ? `${h}j ${rem}m` : `${m}m`;
}

function totalLectures(): number {
    return props.course.sections.reduce((acc, s) => acc + s.lectures.length, 0);
}

function totalDuration(): number {
    return props.course.sections.reduce(
        (acc, s) => acc + s.lectures.reduce((a, l) => a + l.video_duration, 0),
        0,
    );
}

function averageRating(): number {
    if (!props.course.reviews.length) {
        return 0;
    }

    const sum = props.course.reviews.reduce((acc, r) => acc + r.rating, 0);

    return Math.round((sum / props.course.reviews.length) * 10) / 10;
}

async function handleBuy() {
    if (props.isEnrolled) {
        return;
    }

    // Cek sudah login
    if (!(usePage().props.auth as any).user) {
        router.visit('/login');

        return;
    }

    isLoading.value = true;

    try {
        const res = await axios.post('/orders', {
            course_id: props.course.id,
        });

        // Kalau gratis
        if (res.data.free) {
            router.visit(res.data.redirect);

            return;
        }

        // Tampilkan Midtrans Snap popup
        const { snap_token, client_key } = res.data;

        // Load Midtrans Snap script
        const script = document.createElement('script');
        script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', client_key);
        document.head.appendChild(script);

        script.onload = () => {
            (window as any).snap.pay(snap_token, {
                onSuccess: () => {
                    router.visit('/orders');
                },
                onPending: () => {
                    router.visit('/orders');
                },
                onError: () => {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: () => {
                    console.log('Popup ditutup');
                },
            });
        };
    } catch (err: any) {
        alert(err.response?.data?.message ?? 'Terjadi kesalahan.');
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <GuestLayout>
        <Head :title="`${course.title} — DifaFriends`" />

        <!-- ── HERO ──────────────────────────────────────────── -->
        <div class="bg-gray-900 text-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Info kiri -->
                    <div class="lg:col-span-2">
                        <!-- Breadcrumb -->
                        <div
                            class="mb-4 flex items-center gap-2 text-sm text-gray-400"
                        >
                            <Link
                                href="/courses"
                                class="transition-colors hover:text-white"
                                >Katalog</Link
                            >
                            <span>/</span>
                            <span class="text-purple-400">{{
                                course.category.name
                            }}</span>
                        </div>

                        <h1 class="mb-4 text-3xl leading-tight font-bold">
                            {{ course.title }}
                        </h1>

                        <p
                            class="mb-6 line-clamp-3 leading-relaxed text-gray-300"
                        >
                            {{ course.description }}
                        </p>

                        <!-- Rating & stats -->
                        <div
                            class="mb-6 flex flex-wrap items-center gap-4 text-sm"
                        >
                            <div class="flex items-center gap-1">
                                <div class="flex">
                                    <svg
                                        v-for="i in 5"
                                        :key="i"
                                        class="h-4 w-4"
                                        :class="
                                            i <= Math.round(averageRating())
                                                ? 'text-amber-400'
                                                : 'text-gray-600'
                                        "
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                        />
                                    </svg>
                                </div>
                                <span class="font-semibold text-amber-400">{{
                                    averageRating()
                                }}</span>
                                <span class="text-gray-400"
                                    >({{ course.reviews.length }} ulasan)</span
                                >
                            </div>
                            <span class="text-gray-400">•</span>
                            <span>{{ totalLectures() }} lecture</span>
                            <span class="text-gray-400">•</span>
                            <span>{{ formatDuration(totalDuration()) }}</span>
                            <span
                                v-if="course.has_certificate"
                                class="text-gray-400"
                                >•</span
                            >
                            <span
                                v-if="course.has_certificate"
                                class="flex items-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4 text-purple-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                    />
                                </svg>
                                Sertifikat
                            </span>
                        </div>

                        <!-- Instruktur -->
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold"
                            >
                                {{ course.instructor.first_name.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Instruktur</p>
                                <p class="font-medium">
                                    {{ course.instructor.first_name }}
                                    {{ course.instructor.last_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar kanan — hanya tampil di desktop di hero -->
                    <div class="hidden lg:block">
                        <!-- kosong di sini karena sidebar sticky di bawah -->
                    </div>
                </div>
            </div>
        </div>

        <!-- ── MAIN CONTENT ─────────────────────────────────── -->
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <!-- ── KONTEN KIRI ──────────────────────────── -->
                <div class="space-y-6 md:col-span-2">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="flex gap-6">
                            <button
                                v-for="tab in [
                                    { key: 'description', label: 'Deskripsi' },
                                    { key: 'curriculum', label: 'Kurikulum' },
                                    {
                                        key: 'reviews',
                                        label: `Ulasan (${course.reviews.length})`,
                                    },
                                ]"
                                :key="tab.key"
                                @click="activeTab = tab.key as any"
                                :class="[
                                    'border-b-2 pb-3 text-sm font-medium transition-colors',
                                    activeTab === tab.key
                                        ? 'border-purple-600 text-primary'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300',
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Tab: Deskripsi -->
                    <div v-show="activeTab === 'description'" class="space-y-6">
                        <!-- Yang akan dipelajari -->
                        <div v-if="course.goals.length > 0">
                            <h2 class="mb-4 text-lg font-semibold">
                                Yang Akan Dipelajari
                            </h2>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div
                                    v-for="goal in course.goals"
                                    :key="goal.id"
                                    class="flex items-start gap-2"
                                >
                                    <svg
                                        class="mt-0.5 h-5 w-5 shrink-0 text-purple-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                    <span class="text-sm">{{
                                        goal.goal_name
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <h2 class="mb-3 text-lg font-semibold">
                                Tentang Kelas Ini
                            </h2>
                            <p
                                class="leading-relaxed whitespace-pre-line text-gray-600 dark:text-gray-400"
                            >
                                {{ course.description }}
                            </p>
                        </div>

                        <!-- Prerequisites -->
                        <div v-if="course.prerequisites">
                            <h2 class="mb-3 text-lg font-semibold">
                                Prasyarat
                            </h2>
                            <p
                                class="text-sm leading-relaxed text-gray-600 dark:text-gray-400"
                            >
                                {{ course.prerequisites }}
                            </p>
                        </div>
                    </div>

                    <!-- Tab: Kurikulum -->
                    <div v-show="activeTab === 'curriculum'" class="space-y-3">
                        <div class="mb-2 flex items-center justify-between">
                            <p class="text-sm text-gray-500">
                                {{ course.sections.length }} bagian •
                                {{ totalLectures() }} lecture •
                                {{ formatDuration(totalDuration()) }}
                            </p>
                        </div>

                        <div
                            v-for="section in course.sections"
                            :key="section.id"
                            class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700"
                        >
                            <!-- Section header -->
                            <button
                                @click="toggleSection(section.id)"
                                class="flex w-full items-center justify-between bg-gray-50 px-4 py-3 text-left transition-colors hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700"
                            >
                                <span class="text-sm font-medium">{{
                                    section.title
                                }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400"
                                        >{{
                                            section.lectures.length
                                        }}
                                        lecture</span
                                    >
                                    <svg
                                        class="h-4 w-4 text-gray-400 transition-transform"
                                        :class="{
                                            'rotate-180':
                                                expandedSections.includes(
                                                    section.id,
                                                ),
                                        }"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </div>
                            </button>

                            <!-- Lectures -->
                            <div v-show="expandedSections.includes(section.id)">
                                <div
                                    v-for="lecture in section.lectures"
                                    :key="lecture.id"
                                    class="flex items-center justify-between border-t border-gray-100 px-4 py-3 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50"
                                >
                                    <div class="flex items-center gap-3">
                                        <!-- Icon type -->
                                        <svg
                                            class="h-4 w-4 shrink-0 text-gray-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <span class="text-sm">{{
                                            lecture.title
                                        }}</span>
                                        <!-- Free preview badge -->
                                        <span
                                            v-if="lecture.is_free_preview"
                                            class="rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700 dark:bg-green-900/30 dark:text-green-400"
                                            >Preview</span
                                        >
                                    </div>
                                    <span
                                        class="shrink-0 text-xs text-gray-400"
                                    >
                                        {{
                                            formatDuration(
                                                lecture.video_duration,
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Reviews -->
                    <div v-show="activeTab === 'reviews'" class="space-y-6">
                        <div
                            v-if="course.reviews.length === 0"
                            class="py-12 text-center text-gray-400"
                        >
                            <p>Belum ada ulasan untuk kelas ini.</p>
                        </div>

                        <div
                            v-for="review in course.reviews"
                            :key="review.id"
                            class="border-b border-gray-100 pb-6 last:border-0 dark:border-gray-800"
                        >
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100 text-sm font-bold text-primary dark:bg-purple-900/30"
                                >
                                    {{ review.user.first_name.charAt(0) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div
                                        class="mb-1 flex items-center justify-between"
                                    >
                                        <p class="text-sm font-medium">
                                            {{ review.user.first_name }}
                                            {{ review.user.last_name }}
                                        </p>
                                        <div class="flex">
                                            <svg
                                                v-for="i in 5"
                                                :key="i"
                                                class="h-3.5 w-3.5"
                                                :class="
                                                    i <= review.rating
                                                        ? 'text-amber-400'
                                                        : 'text-gray-300'
                                                "
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <p
                                        class="text-sm leading-relaxed text-gray-600 dark:text-gray-400"
                                    >
                                        {{ review.comment }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── SIDEBAR KANAN — STICKY ───────────────── -->
                <div class="md:col-span-1">
                    <div
                        class="sticky top-20 overflow-hidden rounded-2xl border border-gray-200 shadow-lg dark:border-gray-700"
                    >
                        <!-- Thumbnail -->
                        <div
                            class="relative aspect-video bg-purple-50 dark:bg-purple-900/20"
                        >
                            <img
                                v-if="course.thumbnail"
                                :src="assetUrl(course.thumbnail)"
                                :alt="course.title"
                                class="h-full w-full object-cover"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center"
                            >
                                <svg
                                    class="h-12 w-12 text-purple-200"
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
                        </div>

                        <div class="space-y-4 p-5">
                            <!-- Harga -->
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl font-bold text-primary">
                                    {{
                                        formatPrice(
                                            course.discount_price ??
                                                course.price,
                                        )
                                    }}
                                </span>
                                <span
                                    v-if="course.discount_price"
                                    class="text-lg text-gray-400 line-through"
                                >
                                    {{ formatPrice(course.price) }}
                                </span>
                            </div>

                            <!-- Diskon badge -->
                            <div
                                v-if="course.discount_price"
                                class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-600 dark:bg-red-900/30 dark:text-red-400"
                            >
                                Hemat
                                {{
                                    Math.round(
                                        (1 -
                                            course.discount_price /
                                                course.price) *
                                            100,
                                    )
                                }}%
                            </div>

                            <!-- Tombol aksi -->
                            <div class="space-y-2">
                                <!-- Sudah enroll -->
                                <div v-if="isEnrolled">
                                    <div
                                        class="w-full rounded-xl bg-green-100 py-3 text-center text-sm font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400"
                                    >
                                        ✓ Sudah Terdaftar
                                    </div>
                                    <Link
                                        :href="`/learn/${course.slug}`"
                                        class="mt-2 block w-full rounded-xl bg-primary py-3 text-center text-sm font-semibold text-white transition-colors hover:bg-purple-700"
                                    >
                                        Lanjut Belajar →
                                    </Link>
                                </div>

                                <!-- Belum enroll -->
                                <template v-else>
                                    <button
                                        @click="handleBuy"
                                        class="w-full rounded-xl bg-primary py-3 font-semibold text-white transition-colors hover:bg-purple-700"
                                    >
                                        {{
                                            course.price === 0
                                                ? 'Daftar Gratis'
                                                : 'Beli Sekarang'
                                        }}
                                    </button>
                                    <p
                                        class="text-center text-xs text-gray-400"
                                    >
                                        Garansi uang kembali 7 hari
                                    </p>
                                </template>
                            </div>

                            <!-- Info kelas -->
                            <div
                                class="space-y-2 border-t border-gray-100 pt-4 dark:border-gray-700"
                            >
                                <h3 class="mb-3 text-sm font-semibold">
                                    Termasuk dalam kelas:
                                </h3>
                                <div
                                    v-for="item in [
                                        {
                                            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                            text: `${formatDuration(totalDuration())} video`,
                                        },
                                        {
                                            icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                            text: `${totalLectures()} lecture`,
                                        },
                                        {
                                            icon: 'M12 18h.01M8 21l4-4 4 4M3 9l9-2 9 2-9 18L3 9z',
                                            text: 'Akses seumur hidup',
                                        },
                                    ]"
                                    :key="item.text"
                                    class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
                                >
                                    <svg
                                        class="h-4 w-4 shrink-0 text-purple-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            :d="item.icon"
                                        />
                                    </svg>
                                    {{ item.text }}
                                </div>
                                <div
                                    v-if="course.has_certificate"
                                    class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
                                >
                                    <svg
                                        class="h-4 w-4 shrink-0 text-purple-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                        />
                                    </svg>
                                    Sertifikat penyelesaian
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
