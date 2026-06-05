<!-- resources/js/Pages/Instruments/Kesiapan.vue -->

<template>
    <AppLayout title="Kesiapan Sekolah">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-6 flex items-center gap-3">
                <Link
                    href="/assessments"
                    class="text-sm text-gray-400 transition-colors hover:text-gray-600"
                >
                    ← Instrumen
                </Link>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-medium text-gray-700">Kesiapan Sekolah</span>
            </div>

            <!-- Hasil — jika sudah ada data tersimpan -->
            <template v-if="result && !isEditing">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800">
                            Hasil — {{ result.child_name }}
                        </h1>
                        <p class="text-sm text-gray-400">Usia {{ result.child_age }}</p>
                    </div>
                </div>

                <ResultCard
                    :total="(result as KesiapanScoresJson).total"
                    :max-total="(result as KesiapanScoresJson).max_total"
                    :kategori="(result as KesiapanScoresJson).kategori"
                    :aspects="(result as KesiapanScoresJson).aspects"
                    :filled-at="formattedDate"
                    @reset="handleReset"
                />
            </template>

            <!-- Form — jika belum ada data atau sedang edit -->
            <template v-else>
                <div class="mb-6">
                    <h1 class="text-xl font-semibold text-gray-800">Kesiapan Sekolah</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Isi semua item sesuai kondisi anak saat ini.
                    </p>
                </div>

                <!-- Progress bar -->
                <div class="mb-6">
                    <div class="mb-1.5 flex items-center justify-between">
                        <span class="text-xs text-gray-400">Progress pengisian</span>
                        <span class="text-xs font-medium text-gray-600">{{ progress }}%</span>
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                        <div
                            class="h-full rounded-full bg-teal-500 transition-all duration-500"
                            :style="{ width: progress + '%' }"
                        />
                    </div>
                </div>

                <!-- Data anak -->
                <div class="mb-6 grid gap-4 rounded-xl border border-gray-200 bg-white p-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Nama Anak
                        </label>
                        <input
                            v-model="form.child_name"
                            type="text"
                            placeholder="Masukkan nama anak"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-100"
                        />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Usia
                        </label>
                        <input
                            v-model="form.child_age"
                            type="text"
                            placeholder="Contoh: 6 tahun 2 bulan"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-teal-400 focus:ring-2 focus:ring-teal-100"
                        />
                    </div>
                </div>

                <!-- Sections -->
                <div class="space-y-4">
                    <SectionBlock
                        v-for="(section, index) in SECTIONS"
                        :key="section.key"
                        :section-key="section.key"
                        :label="section.label"
                        :max="section.max"
                        :items="section.items"
                        :answers="form.answers"
                        :subtotal="subtotals[section.key]"
                        :show-legend="index === 0"
                        @update:answers="(no, val) => (form.answers[no] = val)"
                    />
                </div>

                <!-- Submit -->
                <div class="mt-8 flex items-center justify-between">
                    <div class="text-sm text-gray-400">
                        Total sementara:
                        <span class="font-semibold text-gray-700">{{ total }}/{{ maxTotal }}</span>
                    </div>
                    <button
                        @click="handleSubmit"
                        :disabled="!isComplete || !form.child_name || !form.child_age"
                        class="rounded-lg bg-teal-500 px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-teal-600 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Simpan Hasil
                    </button>
                </div>

                <!-- Tooltip jika belum lengkap -->
                <p v-if="!isComplete" class="mt-2 text-right text-xs text-gray-400">
                    Isi semua {{ totalItems }} item untuk menyimpan hasil.
                </p>
            </template>

        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import SectionBlock from '@/components/Instruments/SectionBlock.vue'
import ResultCard from '@/components/Instruments/ResultCard.vue'
import { useKesiapan } from '@/composables/useKesiapan'
import type { KesiapanScoresJson } from '@/types/instrument'

const props = defineProps<{
    result: (KesiapanScoresJson & { child_name?: string; child_age?: string }) | null
    updatedAt?: string
}>()

const {
    form,
    SECTIONS,
    subtotals,
    total,
    maxTotal,
    isComplete,
    progress,
    submit,
    reset,
} = useKesiapan()

const isEditing = ref(false)

const totalItems = computed(() =>
    SECTIONS.flatMap(s => s.items).length
)

const formattedDate = computed(() => {
    if (!props.updatedAt) return ''
    return new Date(props.updatedAt).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
})

function handleSubmit(): void {
    submit()
}

function handleReset(): void {
    reset()
}
</script>