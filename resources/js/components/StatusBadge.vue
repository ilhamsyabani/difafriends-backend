<script setup lang="ts">
import { computed } from 'vue';

/**
 * StatusBadge — badge status yang konsisten di seluruh app.
 * Warna dikontrol oleh CSS status tokens di app.css.
 */
const props = withDefaults(
    defineProps<{
        status: string;
    }>(),
    {},
);

const labelMap: Record<string, string> = {
    paid: 'Lunas',
    pending: 'Menunggu',
    expired: 'Kedaluwarsa',
    cancelled: 'Dibatalkan',
    confirmed: 'Dikonfirmasi',
    completed: 'Selesai',
    published: 'Dipublikasi',
    draft: 'Draft',
    review: 'Dalam Review',
    archived: 'Diarsipkan',
    active: 'Aktif',
    inactive: 'Nonaktif',
};

const colorMap: Record<string, string> = {
    paid: 'success',
    confirmed: 'success',
    published: 'success',
    active: 'success',
    completed: 'info',
    pending: 'warning',
    review: 'warning',
    expired: 'neutral',
    draft: 'neutral',
    archived: 'neutral',
    inactive: 'neutral',
    cancelled: 'error',
};

const colorClass = computed(() => {
    const key = colorMap[props.status] ?? 'neutral';
    const map: Record<string, string> = {
        success: 'bg-status-success-bg text-status-success',
        warning: 'bg-status-warning-bg text-status-warning',
        error: 'bg-status-error-bg text-status-error',
        info: 'bg-status-info-bg text-status-info',
        neutral: 'bg-status-neutral-bg text-status-neutral',
    };

    return map[key];
});

const label = computed(() => labelMap[props.status] ?? props.status);
</script>

<template>
    <span
        :class="[
            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
            colorClass,
        ]"
    >
        {{ label }}
    </span>
</template>
