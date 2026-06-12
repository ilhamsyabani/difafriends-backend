<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import { useFormatters } from '@/composables/useFormatters';

const props = defineProps<{
    galleries: Array<{ id: number; filename: string; alt: string; path: string }>
    currentIndex: number
}>()

const emit = defineEmits<{
    close: []
    navigate: [index: number]
}>()

const { assetUrl } = useFormatters()  // import sesuai path kamu

function prev() {
    if (props.currentIndex > 0) emit('navigate', props.currentIndex - 1)
}
function next() {
    if (props.currentIndex < props.galleries.length - 1) emit('navigate', props.currentIndex + 1)
}

function onKey(e: KeyboardEvent) {
    if (e.key === 'ArrowLeft') prev()
    else if (e.key === 'ArrowRight') next()
    else if (e.key === 'Escape') emit('close')
}

onMounted(() => window.addEventListener('keydown', onKey))
onUnmounted(() => window.removeEventListener('keydown', onKey))
</script>

<template>
    <Teleport to="body">
        <Transition name="fade">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4"
                @click.self="emit('close')"
                role="dialog"
                aria-modal="true"
            >
                <div class="relative w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <button
                        class="absolute top-3 right-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-black/50 text-white hover:bg-black/80"
                        @click="emit('close')"
                        aria-label="Tutup"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="bg-black">
                        <img
                            :src="assetUrl(galleries[currentIndex].path)"
                            :alt="galleries[currentIndex].alt"
                            class="max-h-[75vh] w-full object-contain"
                        />
                    </div>

                    <div class="flex items-center justify-between px-4 py-3">
                        <p class="text-sm font-medium text-slate-700">{{ galleries[currentIndex].alt }}</p>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-slate-400">{{ currentIndex + 1 }} / {{ galleries.length }}</span>
                            <div class="flex gap-1">
                                <button
                                    class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-30"
                                    :disabled="currentIndex === 0"
                                    @click="prev"
                                    aria-label="Foto sebelumnya"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <button
                                    class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-30"
                                    :disabled="currentIndex === galleries.length - 1"
                                    @click="next"
                                    aria-label="Foto selanjutnya"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>