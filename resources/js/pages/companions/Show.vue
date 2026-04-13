<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import {
    MapPin,
    Phone,
    Star,
    CalendarClock,
    Clock,
    Wallet,
    MessageSquare,
    Loader2,
    X,
    Quote,
} from 'lucide-vue-next';
import { ref } from 'vue';
// import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import GuestLayout from '@/layouts/GuestLayout.vue';

defineProps<{
    companion: {
        id: number;
        first_name: string;
        last_name: string;
        photo: string | null;
        city: string | null;
        bio: string | null;
        phone: string | null;
        schedules: Array<{
            id: number;
            day_of_week: number;
            start_time: string;
            end_time: string;
            session_duration: number;
            price: number;
            is_active: boolean;
        }>;
        reviews: Array<{
            id: number;
            rating: number;
            comment: string;
            user: { first_name: string; last_name: string };
            created_at: string;
        }>;
    };
    avgRating: number;
}>();

const page = usePage();
const isLoading = ref(false);
const selectedSchedule = ref<any>(null);
const bookingDate = ref('');
const bookingNotes = ref('');
const showBookingModal = ref(false);
const bookingError = ref('');

const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

function getDayName(dayOfWeek: number): string {
    return days[dayOfWeek] ?? '-';
}

function formatTime(time: string): string {
    return time.slice(0, 5);
}

function formatPrice(price: number): string {
    if (price === 0) {
return 'Gratis';
}

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
}

// Memformat tanggal YYYY-MM-DD menjadi format lokal yang cantik (15 Agustus 2026)
function formatDateLocal(dateString: string): string {
    const options: Intl.DateTimeFormatOptions = {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    };

    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function getInitials(first: string, last: string): string {
    return ((first?.charAt(0) || '') + (last?.charAt(0) || '')).toUpperCase();
}

function openBooking(schedule: any) {
    if (!(page.props.auth as any).user) {
        router.visit('/login');

        return;
    }

    selectedSchedule.value = schedule;
    bookingDate.value = '';
    bookingNotes.value = '';
    bookingError.value = '';
    showBookingModal.value = true;
}

// Generate tanggal dengan format raw (value) dan label (tampilan)
function getAvailableDates(
    dayOfWeek: number,
): Array<{ raw: string; label: string }> {
    const dates: Array<{ raw: string; label: string }> = [];
    const today = new Date();

    for (let i = 1; i <= 30; i++) {
        const d = new Date(today);
        d.setDate(today.getDate() + i);

        if (d.getDay() === dayOfWeek) {
            const raw = d.toISOString().split('T')[0];
            dates.push({ raw, label: formatDateLocal(raw) });
        }
    }

    return dates;
}

async function submitBooking() {
    if (!selectedSchedule.value || !bookingDate.value) {
        bookingError.value = 'Silakan pilih tanggal booking terlebih dahulu.';

        return;
    }

    isLoading.value = true;
    bookingError.value = '';

    const startAt = `${bookingDate.value} ${formatTime(selectedSchedule.value.start_time)}:00`;

    try {
        const res = await axios.post('/bookings', {
            schedule_id: selectedSchedule.value.id,
            start_at: startAt,
            notes: bookingNotes.value,
        });

        showBookingModal.value = false;

        if (res.data.free) {
            router.visit(res.data.redirect);

            return;
        }

        // Load Midtrans Snap
        const script = document.createElement('script');
        script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', res.data.client_key);
        document.head.appendChild(script);

        script.onload = () => {
            (window as any).snap.pay(res.data.snap_token, {
                onSuccess: () => router.visit('/user/orders'),
                onPending: () => router.visit('/user/orders'),
                onError: () => alert('Pembayaran gagal. Coba lagi.'),
                onClose: () =>
                    alert(
                        'Anda menutup popup pembayaran sebelum menyelesaikannya.',
                    ),
            });
        };
    } catch (err: any) {
        bookingError.value =
            err.response?.data?.message ?? 'Terjadi kesalahan pada server.';
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <GuestLayout>
        <Head
            :title="`${companion.first_name} ${companion.last_name} — Profil Tutor`"
        />

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- ── PROFIL KIRI (STICKY) ───────────────────────────── -->
                <div class="lg:col-span-4">
                    <div class="sticky top-8 space-y-6">
                        <!-- Card Profil Utama -->
                        <div
                            class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-[0_2px_20px_-3px_rgba(6,81,237,0.05)] dark:border-gray-800 dark:bg-gray-900"
                        >
                            <!-- Header Background -->
                            <div
                                class="h-24 bg-gradient-to-r from-purple-500 to-indigo-500"
                            ></div>

                            <div class="px-6 pb-6">
                                <!-- Avatar -->
                                <div
                                    class="relative mx-auto -mt-12 flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-purple-100 dark:border-gray-900 dark:bg-purple-900/50"
                                >
                                    <img
                                        v-if="companion.photo"
                                        :src="`/storage/${companion.photo}`"
                                        class="h-full w-full object-cover"
                                        alt="Profil"
                                    />
                                    <span
                                        v-else
                                        class="text-3xl font-bold tracking-wider text-purple-600 dark:text-purple-400"
                                    >
                                        {{
                                            getInitials(
                                                companion.first_name,
                                                companion.last_name,
                                            )
                                        }}
                                    </span>
                                </div>

                                <div class="mt-4 text-center">
                                    <h1
                                        class="text-2xl font-bold text-gray-900 dark:text-white"
                                    >
                                        {{ companion.first_name }}
                                        {{ companion.last_name }}
                                    </h1>
                                    <p
                                        class="mt-1 text-sm font-medium text-purple-600 dark:text-purple-400"
                                    >
                                        Guru Pendamping
                                    </p>
                                </div>

                                <!-- Info List -->
                                <div
                                    class="mt-6 space-y-3 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/50"
                                >
                                    <div
                                        v-if="companion.city"
                                        class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300"
                                    >
                                        <MapPin
                                            class="h-4 w-4 shrink-0 text-gray-400"
                                        />
                                        <span>{{ companion.city }}</span>
                                    </div>
                                    <div
                                        v-if="companion.phone"
                                        class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300"
                                    >
                                        <Phone
                                            class="h-4 w-4 shrink-0 text-gray-400"
                                        />
                                        <span>{{ companion.phone }}</span>
                                    </div>
                                    <div
                                        class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300"
                                    >
                                        <Star
                                            class="h-4 w-4 shrink-0 text-amber-400"
                                        />
                                        <span
                                            class="font-semibold text-gray-900 dark:text-white"
                                            >{{ avgRating }}</span
                                        >
                                        <span class="text-gray-400"
                                            >({{
                                                companion.reviews.length
                                            }}
                                            ulasan)</span
                                        >
                                    </div>
                                </div>

                                <div v-if="companion.bio" class="mt-6">
                                    <h3
                                        class="text-xs font-bold tracking-wider text-gray-400 uppercase"
                                    >
                                        Tentang Saya
                                    </h3>
                                    <p
                                        class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300"
                                    >
                                        {{ companion.bio }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── JADWAL & REVIEW KANAN ─────────────────── -->
                <div class="space-y-8 lg:col-span-8">
                    <!-- Section: Jadwal -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 sm:p-8 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="mb-6 flex items-center justify-between">
                            <h2
                                class="text-xl font-bold text-gray-900 dark:text-white"
                            >
                                Jadwal Ketersediaan
                            </h2>
                            <CalendarClock class="h-6 w-6 text-gray-300" />
                        </div>

                        <div
                            v-if="companion.schedules.length === 0"
                            class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-100 py-12 dark:border-gray-800"
                        >
                            <Clock class="mb-3 h-8 w-8 text-gray-300" />
                            <p class="text-sm font-medium text-gray-500">
                                Tutor ini belum mengatur jadwal.
                            </p>
                        </div>

                        <div v-else class="grid gap-4 sm:grid-cols-2">
                            <div
                                v-for="schedule in companion.schedules"
                                :key="schedule.id"
                                class="flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-100 transition-all hover:border-purple-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-800/30"
                            >
                                <div class="p-5">
                                    <div
                                        class="mb-4 flex items-center justify-between"
                                    >
                                        <span
                                            class="rounded-lg bg-purple-100 px-3 py-1 text-xs font-bold tracking-wider text-purple-700 uppercase dark:bg-purple-900/50 dark:text-purple-300"
                                        >
                                            {{
                                                getDayName(schedule.day_of_week)
                                            }}
                                        </span>
                                        <span
                                            class="flex items-center gap-1.5 text-sm font-bold text-green-600 dark:text-green-400"
                                        >
                                            <Wallet class="h-4 w-4" />
                                            {{ formatPrice(schedule.price) }}
                                        </span>
                                    </div>
                                    <div class="space-y-2">
                                        <div
                                            class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300"
                                        >
                                            <Clock
                                                class="h-4 w-4 text-gray-400"
                                            />
                                            <span class="font-medium"
                                                >{{
                                                    formatTime(
                                                        schedule.start_time,
                                                    )
                                                }}
                                                –
                                                {{
                                                    formatTime(
                                                        schedule.end_time,
                                                    )
                                                }}</span
                                            >
                                        </div>
                                        <div class="pl-7 text-xs text-gray-500">
                                            Durasi:
                                            {{ schedule.session_duration }}
                                            menit / sesi
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="border-t border-gray-50 bg-gray-50/50 p-3 dark:border-gray-800/50 dark:bg-transparent"
                                >
                                    <button
                                        @click="openBooking(schedule)"
                                        class="w-full rounded-xl bg-gray-900 py-2.5 text-sm font-medium text-white transition-all hover:bg-purple-600 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-purple-500 dark:hover:text-white"
                                    >
                                        Pesan Jadwal Ini
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Ulasan -->
                    <div
                        class="rounded-3xl border border-gray-100 bg-white p-6 sm:p-8 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="mb-8 flex items-center justify-between">
                            <h2
                                class="text-xl font-bold text-gray-900 dark:text-white"
                            >
                                Ulasan Siswa
                            </h2>
                            <MessageSquare class="h-6 w-6 text-gray-300" />
                        </div>

                        <div
                            v-if="companion.reviews.length === 0"
                            class="py-6 text-center text-sm text-gray-400"
                        >
                            Belum ada ulasan untuk tutor ini.
                        </div>

                        <div v-else class="space-y-6">
                            <div
                                v-for="review in companion.reviews"
                                :key="review.id"
                                class="flex gap-4 border-b border-gray-50 pb-6 last:border-0 last:pb-0 dark:border-gray-800"
                            >
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-gray-100 to-gray-200 text-sm font-bold text-gray-600 dark:from-gray-700 dark:to-gray-800 dark:text-gray-300"
                                >
                                    {{
                                        getInitials(
                                            review.user.first_name,
                                            review.user.last_name,
                                        )
                                    }}
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="mb-1 flex flex-col sm:flex-row sm:items-center sm:justify-between"
                                    >
                                        <h4
                                            class="font-bold text-gray-900 dark:text-white"
                                        >
                                            {{ review.user.first_name }}
                                            {{ review.user.last_name }}
                                        </h4>
                                        <span class="text-xs text-gray-400">{{
                                            formatDateLocal(review.created_at)
                                        }}</span>
                                    </div>
                                    <div class="mb-3 flex">
                                        <Star
                                            v-for="i in 5"
                                            :key="i"
                                            class="h-3.5 w-3.5"
                                            :class="
                                                i <= review.rating
                                                    ? 'fill-amber-400 text-amber-400'
                                                    : 'fill-gray-100 text-gray-200 dark:fill-gray-800 dark:text-gray-700'
                                            "
                                        />
                                    </div>
                                    <div
                                        class="relative rounded-2xl bg-gray-50 p-4 text-sm text-gray-600 dark:bg-gray-800/50 dark:text-gray-300"
                                    >
                                        <Quote
                                            class="absolute top-2 left-2 h-8 w-8 text-gray-200 opacity-50 dark:text-gray-700"
                                        />
                                        <p class="relative z-10 pl-4">
                                            {{ review.comment }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── MODAL BOOKING (REDESIGN) ────────────────────────────────── -->
        <div
            v-if="showBookingModal"
            class="relative z-50"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                @click="showBookingModal = false"
            ></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                    <div
                        class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white text-left align-middle shadow-2xl transition-all dark:bg-gray-900"
                    >
                        <!-- Modal Header -->
                        <div
                            class="flex items-center justify-between border-b border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-transparent"
                        >
                            <h2
                                class="text-lg font-bold text-gray-900 dark:text-white"
                                id="modal-title"
                            >
                                Konfirmasi Sesi
                            </h2>
                            <button
                                @click="showBookingModal = false"
                                class="rounded-full p-2 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                            >
                                <X class="h-5 w-5" />
                            </button>
                        </div>

                        <div class="px-6 py-6">
                            <!-- Receipt / Summary Box -->
                            <div
                                class="mb-6 overflow-hidden rounded-2xl border border-purple-100 bg-purple-50/50 dark:border-purple-900/30 dark:bg-purple-900/10"
                            >
                                <div
                                    class="border-b border-dashed border-purple-200 p-4 dark:border-purple-800"
                                >
                                    <p
                                        class="text-xs font-bold tracking-wider text-purple-600 uppercase dark:text-purple-400"
                                    >
                                        Tutor Pendamping
                                    </p>
                                    <p
                                        class="mt-1 font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ companion.first_name }}
                                        {{ companion.last_name }}
                                    </p>
                                </div>
                                <div class="space-y-3 p-4 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Hari</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                            >{{
                                                getDayName(
                                                    selectedSchedule?.day_of_week,
                                                )
                                            }}</span
                                        >
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Waktu</span>
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{
                                                formatTime(
                                                    selectedSchedule?.start_time ??
                                                        '',
                                                )
                                            }}
                                            –
                                            {{
                                                formatTime(
                                                    selectedSchedule?.end_time ??
                                                        '',
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500"
                                            >Durasi</span
                                        >
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                            >{{
                                                selectedSchedule?.session_duration
                                            }}
                                            menit</span
                                        >
                                    </div>
                                </div>
                                <div
                                    class="bg-purple-100 p-4 dark:bg-purple-900/40"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span
                                            class="font-bold text-gray-900 dark:text-white"
                                            >Total Tagihan</span
                                        >
                                        <span
                                            class="text-lg font-bold text-purple-700 dark:text-purple-400"
                                        >
                                            {{
                                                formatPrice(
                                                    selectedSchedule?.price ??
                                                        0,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Form -->
                            <div class="space-y-5">
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Pilih Tanggal Sesi
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <Select v-model="bookingDate">
                                        <SelectTrigger
                                            :class="[
                                                'w-full rounded-xl bg-white px-4 py-3 text-sm transition-all focus:ring-2 focus:ring-purple-500 dark:bg-gray-950 dark:text-white',
                                                bookingError && !bookingDate
                                                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20'
                                                    : 'border-gray-200 dark:border-gray-800',
                                            ]"
                                        >
                                            <SelectValue
                                                placeholder="Pilih tanggal yang tersedia..."
                                            />
                                        </SelectTrigger>
                                        <SelectContent
                                            class="rounded-xl border-gray-100 shadow-xl dark:border-gray-800"
                                        >
                                            <SelectGroup>
                                                <SelectItem
                                                    v-for="date in getAvailableDates(
                                                        selectedSchedule?.day_of_week ??
                                                            0,
                                                    )"
                                                    :key="date.raw"
                                                    :value="date.raw"
                                                >
                                                    {{ date.label }}
                                                </SelectItem>
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Catatan untuk Tutor
                                        <span class="font-normal text-gray-400"
                                            >(Opsional)</span
                                        >
                                    </label>
                                    <textarea
                                        v-model="bookingNotes"
                                        rows="3"
                                        placeholder="Sebutkan materi yang ingin difokuskan atau kondisi spesifik siswa..."
                                        class="block w-full resize-y rounded-xl border-gray-200 bg-white px-4 py-3 text-sm transition-all focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:border-gray-800 dark:bg-gray-950 dark:text-white"
                                    ></textarea>
                                </div>

                                <div
                                    v-if="bookingError"
                                    class="rounded-lg bg-red-50 p-3 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400"
                                >
                                    {{ bookingError }}
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-transparent"
                        >
                            <button
                                @click="submitBooking"
                                :disabled="isLoading"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-purple-600 py-3.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-70 dark:focus:ring-offset-gray-900"
                            >
                                <Loader2
                                    v-if="isLoading"
                                    class="h-4 w-4 animate-spin"
                                />
                                {{
                                    isLoading
                                        ? 'Memproses Pesanan...'
                                        : 'Lanjutkan ke Pembayaran'
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
