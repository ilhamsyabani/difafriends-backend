<template>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4">
            <div class="flex items-center gap-3">
                <span
                    class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-500 text-xs font-semibold text-white"
                >
                    {{ sectionKey.toUpperCase() }}
                </span>
                <h3 class="text-sm font-semibold text-gray-800">{{ label }}</h3>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-1.5 w-16 overflow-hidden rounded-full bg-gray-200">
                    <div
                        class="h-full rounded-full bg-indigo-500 transition-all duration-300"
                        :style="{ width: progressPct + '%' }"
                    />
                </div>
                <span
                    class="text-sm font-semibold"
                    :class="subtotal > 0 ? 'text-indigo-600' : 'text-gray-400'"
                >
                    {{ subtotal }}/{{ max }}
                </span>
            </div>
        </div>

        <div class="px-5">
            <div
                v-for="(item, index) in items"
                :key="index"
                class="flex items-start gap-4 border-b border-gray-100 py-3 last:border-0"
            >
                <span class="w-6 shrink-0 pt-0.5 text-sm text-gray-400">{{ index + 1 }}</span>
                <span class="flex-1 text-sm text-gray-700">{{ item }}</span>
                <div class="flex shrink-0 gap-1.5">
                    <button
                        v-for="score in [1, 2, 3, 4]"
                        :key="score"
                        type="button"
                        @click="emit('update:answers', `${sectionKey}_${index}`, score)"
                        :class="[
                            'flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium transition-colors',
                            answers[`${sectionKey}_${index}`] === score
                                ? 'bg-indigo-500 text-white'
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200',
                        ]"
                    >
                        {{ score }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    sectionKey: string
    label: string
    max: number
    items: string[]
    answers: Record<string, number>
    subtotal: number
}>()

const emit = defineEmits<{
    (e: 'update:answers', key: string, value: number): void
}>()

const progressPct = computed(() => Math.round((props.subtotal / props.max) * 100))
</script>