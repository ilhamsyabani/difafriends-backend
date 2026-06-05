<template>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <!-- Header -->
        <div
            class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4"
        >
            <div class="flex items-center gap-3">
                <span
                    class="flex h-7 w-7 items-center justify-center rounded-full bg-teal-500 text-xs font-semibold text-white"
                >
                    {{ sectionKey.toUpperCase() }}
                </span>
                <h3 class="text-sm font-semibold text-gray-800">{{ label }}</h3>
            </div>
            <div class="flex items-center gap-2">
                <!-- Progress mini -->
                <div
                    class="h-1.5 w-16 overflow-hidden rounded-full bg-gray-200"
                >
                    <div
                        class="h-full rounded-full bg-teal-500 transition-all duration-300"
                        :style="{ width: progressPct + '%' }"
                    />
                </div>
                <span
                    class="text-sm font-semibold"
                    :class="subtotal > 0 ? 'text-teal-600' : 'text-gray-400'"
                >
                    {{ subtotal }}/{{ max }}
                </span>
            </div>
        </div>

        <!-- Legenda skor — tampil sekali di section pertama saja -->
        <div
            v-if="showLegend"
            class="flex gap-4 border-b border-gray-100 bg-gray-50 px-5 py-2"
        >
            <span
                v-for="s in SCORE_LABELS"
                :key="s.value"
                class="flex items-center gap-1.5 text-xs text-gray-400"
            >
                <span
                    class="flex h-5 w-5 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-500"
                >
                    {{ s.value }}
                </span>
                {{ s.label }}
            </span>
        </div>

        <!-- Items -->
        <div class="px-5">
            <ScoreInput
                v-for="item in items"
                :key="item.no"
                :no="item.no"
                :text="item.text"
                :modelValue="answers[item.no] ?? null"
                @update:modelValue="
                    (val) => emit('update:answers', item.no, val)
                "
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { ScoreItem } from '@/types/instrument';
import ScoreInput from './ScoreInput.vue';


const SCORE_LABELS = [
    { value: 0, label: 'Belum mampu' },
    { value: 1, label: 'Kadang-kadang' },
    { value: 2, label: 'Perlu bantuan' },
    { value: 3, label: 'Konsisten' },
];

const props = defineProps<{
    sectionKey: string;
    label: string;
    max: number;
    items: ScoreItem[];
    answers: Record<number, number>;
    subtotal: number;
    showLegend?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:answers', no: number, value: number): void;
}>();

const progressPct = computed(() =>
    Math.round((props.subtotal / props.max) * 100),
);
</script>

