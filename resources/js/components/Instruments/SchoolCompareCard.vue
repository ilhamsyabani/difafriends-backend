<!-- resources/js/Components/Instruments/SchoolCompareCard.vue -->

<template>
    <div
        class="overflow-hidden rounded-xl border bg-white transition-shadow"
        :class="isBest ? 'border-teal-400 shadow-md' : 'border-gray-200'"
    >
        <!-- Header -->
        <div
            class="flex items-center justify-between px-5 py-4"
            :class="isBest ? 'bg-teal-50' : 'bg-gray-50'"
        >
            <div>
                <div v-if="isBest" class="mb-1 text-xs font-semibold text-teal-600">
                    ★ Paling direkomendasikan
                </div>
                <h3 class="text-sm font-semibold text-gray-800">
                    {{ name || `Sekolah ${index + 1}` }}
                </h3>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold" :class="isBest ? 'text-teal-600' : 'text-gray-700'">
                    {{ total }}
                </div>
                <div class="text-xs text-gray-400">dari 140</div>
            </div>
        </div>

        <!-- Kategori badge -->
        <div class="border-b border-gray-100 px-5 py-3">
            <span
                class="inline-block rounded-full px-3 py-1 text-xs font-medium"
                :class="kategoriStyle.bg"
            >
                <span :class="kategoriStyle.text">{{ kategori }}</span>
            </span>
        </div>

        <!-- Per aspek -->
        <div class="divide-y divide-gray-100">
            <div
                v-for="(aspect, key) in aspects"
                :key="key"
                class="flex items-center justify-between px-5 py-2.5"
            >
                <span class="text-xs text-gray-500">{{ aspect.label }}</span>
                <div class="flex items-center gap-2">
                    <div class="h-1 w-20 overflow-hidden rounded-full bg-gray-100">
                        <div
                            class="h-full rounded-full transition-all"
                            :class="isBest ? 'bg-teal-500' : 'bg-gray-400'"
                            :style="{ width: (aspect.subtotal / aspect.max * 100) + '%' }"
                        />
                    </div>
                    <span class="w-10 text-right text-xs font-medium text-gray-600">
                        {{ aspect.subtotal }}/{{ aspect.max }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { AspectResult } from '@/types/instrument'

const props = defineProps<{
    index: number
    name: string
    total: number
    kategori: string
    aspects: Record<string, AspectResult>
    isBest: boolean
}>()

const kategoriStyle = computed(() => {
    const pct = props.total / 140
    if (pct >= 0.86) return { bg: 'bg-teal-50',  text: 'text-teal-700' }
    if (pct >= 0.71) return { bg: 'bg-blue-50',  text: 'text-blue-700' }
    if (pct >= 0.57) return { bg: 'bg-amber-50', text: 'text-amber-700' }
    if (pct >= 0.43) return { bg: 'bg-orange-50',text: 'text-orange-700' }
    return               { bg: 'bg-red-50',  text: 'text-red-700' }
})
</script>