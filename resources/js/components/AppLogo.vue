<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const appLogo = computed(() => page.props.appLogo as string | null);
const appName = computed(() => (page.props.name as string) || 'DifaFriends');
</script>

<template>
    <div class="flex items-center gap-2">
        <div
            class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-purple-600"
        >
            <img
                v-if="appLogo"
                :src="appLogo"
                :alt="appName"
                class="h-full w-full object-contain"
            />
            <span v-else class="text-sm font-bold text-white">DF</span>
        </div>
        <div class="grid flex-1 text-left leading-tight">
            <span class="truncate font-bold text-purple-700 dark:text-purple-400">
                {{ appName }}
            </span>
            <span class="truncate text-xs text-gray-400">
                Platform Edukasi Inklusif
            </span>
        </div>
    </div>
</template>
