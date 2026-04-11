<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Star } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    targetType: 'course' | 'booking' | 'companion';
    targetId: number;
}>();

const hoverRating = ref(0);

const form = useForm({
    type: props.targetType,
    id: props.targetId,
    rating: 0,
    comment: '',
});

function setRating(val: number) {
    form.rating = val;
}

function submitReview() {
    form.post('/reviews', {
        preserveScroll: true,
        onSuccess: () => {
            // Reset form setelah berhasil
            form.reset();
            hoverRating.value = 0;
        },
    });
}
</script>

<template>
    <div
        class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
    >
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            Berikan Ulasan Anda
        </h3>

        <form @submit.prevent="submitReview" class="mt-4">
            <div class="mb-4 flex items-center gap-1">
                <button
                    v-for="i in 5"
                    :key="i"
                    type="button"
                    @click="setRating(i)"
                    @mouseenter="hoverRating = i"
                    @mouseleave="hoverRating = 0"
                    class="transition-transform hover:scale-110 focus:outline-none"
                >
                    <Star
                        :class="[
                            'h-8 w-8 transition-colors',
                            i <= (hoverRating || form.rating)
                                ? 'fill-yellow-400 text-yellow-400'
                                : 'text-gray-200 dark:text-gray-700',
                        ]"
                    />
                </button>
            </div>
            <p v-if="form.errors.rating" class="mb-2 text-sm text-red-500">
                {{ form.errors.rating }}
            </p>

            <div class="mb-4">
                <textarea
                    v-model="form.comment"
                    rows="3"
                    placeholder="Ceritakan pengalaman Anda di sini (Opsional)..."
                    class="block w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                ></textarea>
                <p v-if="form.errors.comment" class="mt-1 text-sm text-red-500">
                    {{ form.errors.comment }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing || form.rating === 0"
                class="inline-flex w-full items-center justify-center rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-purple-700 disabled:opacity-50"
            >
                {{ form.processing ? 'Mengirim...' : 'Kirim Ulasan' }}
            </button>
        </form>
    </div>
</template>
