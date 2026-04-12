<script setup lang="ts">
/**
 * EmptyState — tampilan ketika list kosong.
 * Gunakan slot #action untuk tombol / link tindakan.
 */
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div class="py-12 text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted">
            <slot name="icon">
                <svg class="h-6 w-6 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </slot>
        </div>
        <p class="text-sm text-muted-foreground">{{ message ?? 'Belum ada data.' }}</p>
        <div v-if="$slots.action" class="mt-3">
            <slot name="action" />
        </div>
    </div>
</template>
