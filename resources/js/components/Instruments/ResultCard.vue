<template>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <!-- Total score -->
        <div class="border-b border-gray-100 px-6 py-8 text-center">
            <div class="text-6xl font-bold text-teal-600">{{ total }}</div>
            <div class="mt-1 text-sm text-gray-400">dari {{ maxTotal }}</div>
            <div
                class="mt-4 inline-block rounded-full px-5 py-1.5 text-sm font-medium"
                :class="kategoriStyle.bg"
            >
                <span :class="kategoriStyle.text">{{ kategori }}</span>
            </div>
        </div>

        <!-- Per aspek -->
        <div class="divide-y divide-gray-100">
            <div
                v-for="(aspect, key) in aspects"
                :key="key"
                class="flex items-center justify-between px-5 py-3"
            >
                <div class="flex items-center gap-2">
                    <span class="w-6 text-xs font-bold uppercase text-gray-400">{{ key }}</span>
                    <span class="text-sm text-gray-600">{{ aspect.label }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-1.5 w-24 overflow-hidden rounded-full bg-gray-100">
                        <div
                            class="h-full rounded-full bg-teal-500 transition-all"
                            :style="{ width: (aspect.subtotal / aspect.max * 100) + '%' }"
                        />
                    </div>
                    <span class="w-12 text-right text-sm font-medium text-gray-700">
                        {{ aspect.subtotal }}/{{ aspect.max }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between border-t border-gray-100 px-5 py-4">
            <span class="text-xs text-gray-400">Diisi pada {{ filledAt }}</span>
            <button
                @click="emit('reset')"
                class="text-sm text-gray-400 transition-colors hover:text-red-500"
            >
                Isi ulang
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { AspectResult } from '@/types/instrument'

const props = defineProps<{
    total: number
    maxTotal: number
    kategori: string
    aspects: Record<string, AspectResult>
    filledAt?: string
}>()

const emit = defineEmits<{
    (e: 'reset'): void
}>()

const kategoriStyle = computed(() => {
    const pct = props.total / props.maxTotal
    if (pct >= 0.8)  return { bg: 'bg-teal-50',   text: 'text-teal-700' }
    if (pct >= 0.6)  return { bg: 'bg-blue-50',   text: 'text-blue-700' }
    if (pct >= 0.4)  return { bg: 'bg-amber-50',  text: 'text-amber-700' }
    return               { bg: 'bg-red-50',    text: 'text-red-700' }
})
</script>