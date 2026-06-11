<!-- resources/js/Pages/Instruments/Index.vue -->

<template>
    <AppLayout title="Assessment">
        <div class="mx-auto max-w-6xl px-4 py-10">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-gray-800">Assessment</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Pilih assessment yang ingin Anda isi untuk anak Anda.
                </p>
            </div>

            <!-- Cards -->
            <div class="grid gap-4 sm:grid-cols-2">
                <!-- Kesiapan Sekolah -->
                <Link href="/assessments/kesiapan">
                    <div
                        class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 transition-shadow hover:shadow-md"
                    >
                        <!-- Status badge -->
                        <div class="mb-4 flex items-start justify-between">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50"
                            >
                                <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="hasKesiapan
                                    ? 'bg-teal-50 text-teal-700'
                                    : 'bg-gray-100 text-gray-500'"
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="hasKesiapan ? 'bg-teal-500' : 'bg-gray-400'"
                                />
                                {{ hasKesiapan ? 'Sudah diisi' : 'Belum diisi' }}
                            </span>
                        </div>

                        <h2 class="text-base font-semibold text-gray-800">
                            Kesiapan Sekolah
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Screening kesiapan anak memasuki lingkungan sekolah.
                        </p>

                        <!-- Meta -->
                        <div class="mt-4 flex gap-4 text-xs text-gray-400">
                            <span>33 item</span>
                            <span>6 aspek</span>
                            <span>Usia 5–7 tahun</span>
                        </div>

                        <!-- Arrow -->
                        <div
                            class="absolute bottom-6 right-6 text-gray-300 transition-transform group-hover:translate-x-1"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </Link>

                <!-- Pemilihan Sekolah -->
                <Link href="/assessments/pemilihan">
                    <div
                        class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 transition-shadow hover:shadow-md"
                    >
                        <div class="mb-4 flex items-start justify-between">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50"
                            >
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="hasPemilihan
                                    ? 'bg-indigo-50 text-indigo-700'
                                    : 'bg-gray-100 text-gray-500'"
                            >
                                <span
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="hasPemilihan ? 'bg-indigo-500' : 'bg-gray-400'"
                                />
                                {{ hasPemilihan ? 'Sudah diisi' : 'Belum diisi' }}
                            </span>
                        </div>

                        <h2 class="text-base font-semibold text-gray-800">
                            Pemilihan Sekolah
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Penilaian dan perbandingan hingga 3 sekolah pilihan.
                        </p>

                        <div class="mt-4 flex gap-4 text-xs text-gray-400">
                            <span>35 item</span>
                            <span>7 aspek</span>
                            <span>Maks. 3 sekolah</span>
                        </div>

                        <div
                            class="absolute bottom-6 right-6 text-gray-300 transition-transform group-hover:translate-x-1"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps<{
    hasKesiapan: boolean
    hasPemilihan: boolean
}>()
</script>