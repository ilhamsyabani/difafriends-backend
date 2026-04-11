<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { Toaster, toast } from 'vue-sonner';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

const page = usePage();

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// Watch dengan { immediate: false } agar tidak muncul saat pertama mount.
// Flash hanya muncul setelah navigasi / aksi Inertia.
watch(
    () => page.props.flash as { success?: string; error?: string } | null,
    (flash) => {
        if (flash?.success) {
            toast.success('Berhasil!', {
                description: flash.success,
                duration: 4000,
            });
        }
        if (flash?.error) {
            toast.error('Gagal!', {
                description: flash.error,
                duration: 5000,
            });
        }
    },
    { deep: true },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />

        <!-- Satu-satunya tempat flash message tampil -->
        <Toaster
            position="top-right"
            richColors
            :expand="true"
            :toastOptions="{
                class: 'rounded-xl shadow-lg border-none',
                descriptionClass: 'text-xs opacity-80',
            }"
        />

        <!-- Dialog konfirmasi global — dipakai lewat useConfirm() -->
        <ConfirmDialog />
    </AppLayout>
</template>
