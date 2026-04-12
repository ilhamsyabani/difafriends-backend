<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

/**
 * SectionHeader — header panel dengan judul dan link "Lihat semua".
 */
defineProps<{
    title: string;
    href?: string;
    linkText?: string;
}>();
</script>

<template>
    <div class="flex items-center justify-between border-b border-border px-5 py-4">
        <h2 class="font-semibold text-foreground">{{ title }}</h2>
        <Link
            v-if="href"
            :href="href"
            class="text-sm text-brand hover:underline"
        >
            {{ linkText ?? 'Lihat semua' }}
        </Link>
    </div>
</template>
