<script setup lang="ts">
/**
 * StatsCard — kartu statistik yang dipakai di semua dashboard.
 * Gunakan slot #icon untuk icon kustom, atau cukup label+value.
 */
defineProps<{
    label: string;
    value: string | number;
    /** Warna aksen icon: 'brand' | 'blue' | 'green' | 'amber' | 'red' */
    color?: 'brand' | 'blue' | 'green' | 'amber' | 'red';
}>();
</script>

<template>
    <div class="rounded-2xl border border-border bg-card p-5">
        <div class="mb-3 flex items-center justify-between">
            <span class="text-sm text-muted-foreground">{{ label }}</span>
            <div
                v-if="$slots.icon"
                :class="[
                    'flex h-9 w-9 items-center justify-center rounded-xl',
                    color === 'brand' || !color
                        ? 'bg-brand-subtle text-brand'
                        : color === 'blue'
                          ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'
                          : color === 'green'
                            ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400'
                            : color === 'amber'
                              ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'
                              : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                ]"
            >
                <slot name="icon" />
            </div>
        </div>
        <p class="text-2xl font-bold text-foreground">{{ value }}</p>
    </div>
</template>
