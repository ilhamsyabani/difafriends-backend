<template>
    <div class="flex items-start gap-4 py-3 border-b border-gray-100 last:border-0">
        <span class="w-6 shrink-0 pt-0.5 text-sm text-gray-400">{{ no }}</span>
        <span class="flex-1 text-sm text-gray-700">{{ text }}</span>
        <div class="flex shrink-0 gap-1.5">
            <button
                v-for="score in SCORES"
                :key="score.value"
                type="button"
                :title="score.label"
                @click="emit('update:modelValue', score.value)"
                :class="[
                    'flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium transition-colors',
                    modelValue === score.value
                        ? 'bg-teal-500 text-white'
                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200',
                ]"
            >
                {{ score.value }}
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
const SCORES = [
    { value: 0, label: 'Belum mampu' },
    { value: 1, label: 'Kadang-kadang mampu' },
    { value: 2, label: 'Sudah mampu tetapi masih perlu bantuan' },
    { value: 3, label: 'Sudah mampu secara konsisten' },
]

defineProps<{
    no: number
    text: string
    modelValue: number | null
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: number): void
}>()
</script>