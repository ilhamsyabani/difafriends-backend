<script setup lang="ts">
import { VueFinalModal } from 'vue-final-modal';

defineProps<{
    title: string;
    message: string;
}>();

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();
</script>

<template>
    <VueFinalModal
        class="flex items-center justify-center"
        overlay-class="fixed inset-0 bg-black/50 backdrop-blur-sm"
        :click-to-close="false"
    >
        <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 border dark:border-gray-800">
            <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">
                {{ title }}
            </h3>
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                {{ message }}
            </p>
            
            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    @click="emit('cancel')"
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow-md shadow-primary/20 transition-all hover:bg-orange-500 active:scale-95"
                    @click="emit('confirm')"
                >
                    Ya, lanjutkan
                </button>
            </div>
        </div>
    </VueFinalModal>
</template>
