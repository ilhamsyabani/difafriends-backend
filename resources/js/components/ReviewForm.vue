<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Star } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    targetType: 'course' | 'booking' | 'companion';
    targetId: number;
}>();

const hoverRating = ref(0);
const ratingGroupRef = ref<HTMLElement | null>(null);

const ratingLabels: Record<number, string> = {
    1: '1 bintang — Sangat Buruk',
    2: '2 bintang — Buruk',
    3: '3 bintang — Cukup',
    4: '4 bintang — Baik',
    5: '5 bintang — Sangat Baik',
};

const form = useForm({
    type: props.targetType,
    id: props.targetId,
    rating: 0,
    comment: '',
});

function setRating(val: number) {
    form.rating = val;
}

// Navigasi keyboard panah kiri/kanan di dalam radiogroup
function onRatingKeydown(e: KeyboardEvent, current: number) {
    if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
        e.preventDefault();
        const next = Math.min(current + 1, 5);
        setRating(next);
        focusStar(next);
    } else if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
        e.preventDefault();
        const prev = Math.max(current - 1, 1);
        setRating(prev);
        focusStar(prev);
    }
}

function focusStar(index: number) {
    const btn = ratingGroupRef.value?.querySelector<HTMLButtonElement>(
        `[data-star="${index}"]`,
    );
    btn?.focus();
}

function submitReview() {
    form.post('/reviews', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            hoverRating.value = 0;
        },
    });
}

const commentId = `review-comment-${props.targetId}`;
const ratingErrorId = `review-rating-error-${props.targetId}`;
</script>

<template>
    <section
        aria-labelledby="review-form-heading"
        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
    >
        <h3
            id="review-form-heading"
            class="text-lg font-bold text-gray-900 dark:text-white"
        >
            Berikan Ulasan Anda
        </h3>

        <form @submit.prevent="submitReview" class="mt-4" novalidate>

            <!-- Rating — ARIA radiogroup untuk screen reader & navigasi keyboard -->
            <fieldset class="mb-4">
                <legend class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Rating <span aria-hidden="true">*</span>
                    <span class="sr-only">(wajib diisi)</span>
                </legend>

                <div
                    ref="ratingGroupRef"
                    role="radiogroup"
                    :aria-label="`Rating: ${form.rating ? ratingLabels[form.rating] : 'Belum dipilih'}`"
                    :aria-describedby="form.errors.rating ? ratingErrorId : undefined"
                    :aria-required="true"
                    class="flex items-center gap-1"
                >
                    <button
                        v-for="i in 5"
                        :key="i"
                        :data-star="i"
                        type="button"
                        role="radio"
                        :aria-checked="form.rating === i"
                        :aria-label="ratingLabels[i]"
                        :tabindex="form.rating === 0 ? (i === 1 ? 0 : -1) : (form.rating === i ? 0 : -1)"
                        @click="setRating(i)"
                        @keydown="onRatingKeydown($event, i)"
                        @mouseenter="hoverRating = i"
                        @mouseleave="hoverRating = 0"
                        class="rounded-sm transition-transform hover:scale-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2"
                    >
                        <Star
                            :class="[
                                'h-8 w-8 transition-colors',
                                i <= (hoverRating || form.rating)
                                    ? 'fill-yellow-400 text-yellow-400'
                                    : 'text-gray-200 dark:text-gray-700',
                            ]"
                            aria-hidden="true"
                        />
                    </button>

                    <!-- Teks rating yang terpilih untuk semua pengguna -->
                    <span
                        v-if="form.rating > 0"
                        class="ml-2 text-sm text-gray-500 dark:text-gray-400"
                        aria-live="polite"
                    >
                        {{ ratingLabels[form.rating] }}
                    </span>
                </div>

                <p
                    v-if="form.errors.rating"
                    :id="ratingErrorId"
                    role="alert"
                    class="mt-1 text-sm text-red-500"
                >
                    {{ form.errors.rating }}
                </p>
            </fieldset>

            <!-- Komentar -->
            <div class="mb-4">
                <label
                    :for="commentId"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Komentar
                    <span class="text-xs font-normal text-gray-400">(opsional)</span>
                </label>
                <textarea
                    :id="commentId"
                    v-model="form.comment"
                    rows="3"
                    maxlength="1000"
                    placeholder="Ceritakan pengalaman Anda di sini..."
                    :aria-describedby="form.errors.comment ? `${commentId}-error` : undefined"
                    class="block w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-purple-500 focus:outline-none focus:ring-1 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
                <div class="mt-1 flex items-center justify-between">
                    <p
                        v-if="form.errors.comment"
                        :id="`${commentId}-error`"
                        role="alert"
                        class="text-sm text-red-500"
                    >
                        {{ form.errors.comment }}
                    </p>
                    <p class="ml-auto text-xs text-gray-400" aria-live="polite">
                        {{ form.comment.length }}/1000
                    </p>
                </div>
            </div>

            <button
                type="submit"
                :disabled="form.processing || form.rating === 0"
                :aria-busy="form.processing"
                :aria-disabled="form.rating === 0"
                class="inline-flex w-full items-center justify-center rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-purple-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <span v-if="form.processing" aria-hidden="true" class="mr-2">⏳</span>
                {{ form.processing ? 'Mengirim...' : 'Kirim Ulasan' }}
            </button>

            <p v-if="form.rating === 0" class="mt-2 text-center text-xs text-gray-400">
                Pilih rating bintang terlebih dahulu
            </p>
        </form>
    </section>
</template>
