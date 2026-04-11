<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';

defineProps<{
    certificate: {
        certificate_number: string;
        issued_at: string;
        user: { first_name: string; last_name: string };
        course: { title: string };
    } | null;
    valid: boolean;
}>();

function formatDate(dt: string): string {
    return new Date(dt).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Sertifikat — DifaFriends" />

        <div class="mx-auto max-w-2xl px-4 py-16 text-center">
            <!-- Valid -->
            <div v-if="valid && certificate">
                <div
                    class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30"
                >
                    <svg
                        class="h-10 w-10 text-green-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                        />
                    </svg>
                </div>

                <h1 class="mb-2 text-2xl font-bold text-green-600">
                    Sertifikat Valid ✓
                </h1>
                <p class="mb-8 text-gray-500">
                    Sertifikat ini telah terverifikasi oleh DifaFriends
                </p>

                <div
                    class="space-y-4 rounded-2xl border border-gray-100 bg-white p-8 text-left dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Nama Penerima</span>
                        <span class="font-semibold">
                            {{ certificate.user.first_name }}
                            {{ certificate.user.last_name }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Kelas</span>
                        <span class="font-semibold">{{
                            certificate.course.title
                        }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">No. Sertifikat</span>
                        <span class="font-mono text-xs text-gray-600">
                            {{ certificate.certificate_number }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Diterbitkan</span>
                        <span class="font-semibold">{{
                            formatDate(certificate.issued_at)
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Invalid -->
            <div v-else>
                <div
                    class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30"
                >
                    <svg
                        class="h-10 w-10 text-red-500"
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
                </div>
                <h1 class="mb-2 text-2xl font-bold text-red-500">
                    Sertifikat Tidak Valid
                </h1>
                <p class="text-gray-500">
                    Nomor sertifikat tidak ditemukan dalam sistem DifaFriends.
                </p>
            </div>
        </div>
    </GuestLayout>
</template>
