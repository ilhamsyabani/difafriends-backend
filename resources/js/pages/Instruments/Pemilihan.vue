<!-- resources/js/Pages/Instruments/Pemilihan.vue -->

<template>
    <AppLayout title="Pemilihan Sekolah">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6 flex items-center gap-3">
                <Link
                    href="/assessments"
                    class="text-sm text-gray-400 transition-colors hover:text-gray-600"
                >
                    ← Instrumen
                </Link>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-medium text-gray-700">Pemilihan Sekolah</span>
            </div>

            <!-- Hasil -->
            <template v-if="result && !isEditing">
                <div class="mb-6">
                    <h1 class="text-xl font-semibold text-gray-800">
                        Hasil — {{ result.child_name ?? '' }}
                    </h1>
                    <p class="text-sm text-gray-400">Usia {{ result.child_age ?? '' }}</p>
                </div>

                <!-- Perbandingan 3 sekolah -->
                <div class="grid gap-4 sm:grid-cols-3">
                    <SchoolCompareCard
                        v-for="(school, key, index) in (result as PemilihanScoresJson).schools"
                        :key="key"
                        :index="index"
                        :name="school.name"
                        :total="school.total"
                        :kategori="school.kategori"
                        :aspects="school.aspects"
                        :is-best="index === bestSchoolIndexFromResult"
                    />
                </div>

                <div class="mt-6 text-right">
                    <button
                        @click="handleReset"
                        class="text-sm text-gray-400 transition-colors hover:text-red-500"
                    >
                        Isi ulang
                    </button>
                </div>
            </template>

            <!-- Form -->
            <template v-else>
                <div class="mb-6">
                    <h1 class="text-xl font-semibold text-gray-800">Pemilihan Sekolah</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Nilai dan bandingkan hingga 3 sekolah pilihan Anda.
                    </p>
                </div>

                <!-- Progress -->
                <div class="mb-6">
                    <div class="mb-1.5 flex items-center justify-between">
                        <span class="text-xs text-gray-400">Progress pengisian</span>
                        <span class="text-xs font-medium text-gray-600">{{ progress }}%</span>
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                        <div
                            class="h-full rounded-full bg-indigo-500 transition-all duration-500"
                            :style="{ width: progress + '%' }"
                        />
                    </div>
                </div>

                <!-- Data anak -->
                <div class="mb-6 grid gap-4 rounded-xl border border-gray-200 bg-white p-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Nama Anak</label>
                        <input
                            v-model="form.child_name"
                            type="text"
                            placeholder="Masukkan nama anak"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                        />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Usia</label>
                        <input
                            v-model="form.child_age"
                            type="text"
                            placeholder="Contoh: 6 tahun 2 bulan"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                        />
                    </div>
                </div>

                <!-- Profil anak -->
                <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Profil Anak</h2>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <!-- Minat -->
                        <div>
                            <p class="mb-2 text-xs font-medium text-gray-500">Potensi & Minat</p>
                            <div class="space-y-2">
                                <label
                                    v-for="opt in MINAT_OPTIONS"
                                    :key="opt.key"
                                    class="flex cursor-pointer items-center gap-2.5"
                                >
                                    <input
                                        type="checkbox"
                                        :value="opt.key"
                                        v-model="form.child_profile.minat"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-500"
                                    />
                                    <span class="text-sm text-gray-600">{{ opt.label }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Kebutuhan -->
                        <div>
                            <p class="mb-2 text-xs font-medium text-gray-500">Kebutuhan Anak</p>
                            <div class="space-y-2">
                                <label
                                    v-for="opt in KEBUTUHAN_OPTIONS"
                                    :key="opt.key"
                                    class="flex cursor-pointer items-center gap-2.5"
                                >
                                    <input
                                        type="checkbox"
                                        :value="opt.key"
                                        v-model="form.child_profile.kebutuhan"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-500"
                                    />
                                    <span class="text-sm text-gray-600">{{ opt.label }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab sekolah -->
                <div class="mb-4 flex gap-2">
                    <button
                        v-for="(school, index) in form.schools"
                        :key="index"
                        @click="activeSchool = index"
                        :class="[
                            'rounded-lg px-4 py-2 text-sm font-medium transition-colors',
                            activeSchool === index
                                ? 'bg-indigo-500 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                        ]"
                    >
                        {{ school.name || `Sekolah ${index + 1}` }}
                    </button>
                </div>

                <!-- Nama sekolah aktif -->
                <div class="mb-4">
                    <input
                        v-model="form.schools[activeSchool].name"
                        type="text"
                        :placeholder="`Nama Sekolah ${activeSchool + 1}`"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                    />
                </div>

                <!-- Sections untuk sekolah aktif -->
                <div class="space-y-4">
                    <SectionBlockPemilihan
                        v-for="aspect in ASPECTS"
                        :key="aspect.key"
                        :section-key="aspect.key"
                        :label="aspect.label"
                        :max="aspect.max"
                        :items="aspect.items"
                        :answers="form.schools[activeSchool].answers"
                        :subtotal="getSubtotal(activeSchool, aspect.key)"
                        @update:answers="(key, val) => (form.schools[activeSchool].answers[key] = val)"
                    />
                </div>

                <!-- Total sekolah aktif -->
                <div class="mt-6 rounded-xl border border-gray-100 bg-gray-50 px-5 py-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">
                            Total {{ form.schools[activeSchool].name || `Sekolah ${activeSchool + 1}` }}
                        </span>
                        <span class="text-lg font-semibold text-gray-800">
                            {{ getTotal(activeSchool) }}/140
                        </span>
                    </div>
                    <div class="mt-2 text-sm text-indigo-600 font-medium">
                        {{ getKategori(activeSchool) }}
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-8 flex items-center justify-between">
                    <div class="text-sm text-gray-400">
                        Isi ketiga sekolah untuk menyimpan hasil.
                    </div>
                    <button
                        @click="handleSubmit"
                        :disabled="!isComplete || !form.child_name || !form.child_age"
                        class="rounded-lg bg-indigo-500 px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Simpan & Lihat Perbandingan
                    </button>
                </div>
            </template>

        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import SectionBlockPemilihan from '@/components/Instruments/SectionBlockPemilihan.vue'
import SchoolCompareCard from '@/components/Instruments/SchoolCompareCard.vue'
import { usePemilihan } from '@/composables/usePemilihan'
import type { PemilihanScoresJson } from '@/types/instrument'

const props = defineProps<{
    result: (PemilihanScoresJson & { child_name?: string; child_age?: string }) | null
}>()

const {
    form,
    ASPECTS,
    MINAT_OPTIONS,
    KEBUTUHAN_OPTIONS,
    isComplete,
    progress,
    getSubtotal,
    getTotal,
    getKategori,
    submit,
    reset,
} = usePemilihan()

const activeSchool = ref<number>(0)
const isEditing    = ref<boolean>(false)

const bestSchoolIndexFromResult = computed<number>(() => {
    if (!props.result?.schools) return 0
    const totals = Object.values(props.result.schools).map(s => s.total)
    return totals.indexOf(Math.max(...totals))
})

function handleSubmit(): void {
    submit()
}

function handleReset(): void {
    reset()
}
</script>