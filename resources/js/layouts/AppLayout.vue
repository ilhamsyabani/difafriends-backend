<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ModalsContainer } from 'vue-final-modal';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

const page = usePage();

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />

        <!-- Container untuk modal dinamik (confirm dialog, dsb) -->
        <ModalsContainer />

        <!--
            Region tersembunyi untuk screen reader — mengumumkan flash message
            saat terjadi navigasi Inertia (polite = tidak memotong pembacaan).
        -->
        <div
            role="status"
            aria-live="polite"
            aria-atomic="true"
            class="sr-only"
        >
            <span v-if="(page.props.flash as any)?.success">
                {{ (page.props.flash as any).success }}
            </span>
            <span v-if="(page.props.flash as any)?.error">
                Terjadi kesalahan: {{ (page.props.flash as any).error }}
            </span>
        </div>
    </AppLayout>
</template>
