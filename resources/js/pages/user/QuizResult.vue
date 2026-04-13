<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    course: { id: number; title: string; slug: string };
    quiz: {
        id: number;
        title: string;
        passing_score: number;
    };
    attempt: {
        id: number;
        score: number | null;
        status: string;
        submitted_at: string;
        answers: Array<{
            id: number;
            essay_answer: string | null;
            points_earned: number | null;
            instructor_note: string | null;
            question: {
                id: number;
                type: string;
                question: string;
                points: number;
                options: Array<{
                    id: number;
                    option_text: string;
                    is_correct: boolean;
                }>;
            };
            selected_option: {
                id: number;
                option_text: string;
                is_correct: boolean;
            } | null;
        }>;
    };
    passed: boolean;
}>();

function totalEarned(): number {
    return props.attempt.answers.reduce(
        (sum, a) => sum + (a.points_earned ?? 0),
        0,
    );
}

function totalPoints(): number {
    return props.attempt.answers.reduce((sum, a) => sum + a.question.points, 0);
}
</script>

<template>
    <Head :title="`Hasil Kuis: ${quiz.title}`" />

    <div class="min-h-screen bg-gray-50 py-8 dark:bg-gray-950">
        <div class="mx-auto max-w-3xl px-4">
            <!-- Hasil score -->
            <div
                :class="[
                    'mb-6 rounded-2xl border p-8 text-center',
                    passed
                        ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20'
                        : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20',
                ]"
            >
                <div
                    :class="[
                        'mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full',
                        passed
                            ? 'bg-green-100 dark:bg-green-900/30'
                            : 'bg-red-100 dark:bg-red-900/30',
                    ]"
                >
                    <span
                        :class="[
                            'text-3xl font-bold',
                            passed ? 'text-green-600' : 'text-red-600',
                        ]"
                    >
                        {{ attempt.score ?? '?' }}%
                    </span>
                </div>

                <h1 class="mb-1 text-2xl font-bold">
                    {{ passed ? '🎉 Selamat, Kamu Lulus!' : 'Belum Lulus' }}
                </h1>
                <p
                    :class="[
                        'text-sm',
                        passed
                            ? 'text-green-600 dark:text-green-400'
                            : 'text-red-500 dark:text-red-400',
                    ]"
                >
                    {{ totalEarned() }} / {{ totalPoints() }} poin · Nilai
                    minimum lulus: {{ quiz.passing_score }}%
                </p>
            </div>

            <!-- Pembahasan per soal -->
            <div class="space-y-4">
                <h2 class="text-lg font-bold">Pembahasan</h2>

                <div
                    v-for="(answer, idx) in attempt.answers"
                    :key="answer.id"
                    :class="[
                        'rounded-2xl border bg-white p-5 dark:bg-gray-800',
                        answer.question.type === 'multiple_choice'
                            ? answer.selected_option?.is_correct
                                ? 'border-green-200 dark:border-green-800'
                                : 'border-red-200 dark:border-red-800'
                            : 'border-gray-100 dark:border-gray-700',
                    ]"
                >
                    <!-- Header soal -->
                    <div class="mb-3 flex items-start justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-400"
                            >
                                {{ idx + 1 }}
                            </span>
                            <span
                                :class="[
                                    'rounded-full px-2 py-0.5 text-xs font-medium',
                                    answer.question.type === 'multiple_choice'
                                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                ]"
                            >
                                {{
                                    answer.question.type === 'multiple_choice'
                                        ? 'Pilihan Ganda'
                                        : 'Esai'
                                }}
                            </span>
                        </div>
                        <span
                            class="text-sm font-semibold"
                            :class="
                                answer.points_earned === answer.question.points
                                    ? 'text-green-600'
                                    : answer.points_earned === 0
                                      ? 'text-red-500'
                                      : 'text-amber-600'
                            "
                        >
                            {{ answer.points_earned ?? '?' }} /
                            {{ answer.question.points }} poin
                        </span>
                    </div>

                    <p class="mb-4 text-sm font-medium">
                        {{ answer.question.question }}
                    </p>

                    <!-- PG: tampilkan semua opsi -->
                    <div
                        v-if="answer.question.type === 'multiple_choice'"
                        class="space-y-2"
                    >
                        <div
                            v-for="opt in answer.question.options"
                            :key="opt.id"
                            :class="[
                                'flex items-center gap-2 rounded-lg px-3 py-2 text-sm',
                                opt.is_correct
                                    ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400'
                                    : answer.selected_option?.id === opt.id &&
                                        !opt.is_correct
                                      ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400'
                                      : 'text-gray-500',
                            ]"
                        >
                            <svg
                                class="h-4 w-4 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    v-if="opt.is_correct"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                                <path
                                    v-else-if="
                                        answer.selected_option?.id === opt.id
                                    "
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                                <circle
                                    v-else
                                    cx="12"
                                    cy="12"
                                    r="4"
                                    fill="currentColor"
                                    opacity="0.3"
                                />
                            </svg>
                            {{ opt.option_text }}
                            <span
                                v-if="
                                    answer.selected_option?.id === opt.id &&
                                    !opt.is_correct
                                "
                                class="ml-auto text-xs"
                                >(Jawaban kamu)</span
                            >
                        </div>
                    </div>

                    <!-- Esai: tampilkan jawaban + feedback instruktur -->
                    <div v-else class="space-y-3">
                        <div
                            class="rounded-xl bg-gray-50 p-3 text-sm leading-relaxed dark:bg-gray-700/50"
                        >
                            {{ answer.essay_answer || '(Tidak dijawab)' }}
                        </div>

                        <div
                            v-if="answer.instructor_note"
                            class="rounded-xl bg-purple-50 p-3 dark:bg-purple-900/20"
                        >
                            <p
                                class="mb-1 text-xs font-medium text-primary-hover dark:text-purple-400"
                            >
                                Feedback Instruktur:
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                {{ answer.instructor_note }}
                            </p>
                        </div>

                        <div
                            v-if="attempt.status === 'pending'"
                            class="flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400"
                        >
                            <svg
                                class="h-3.5 w-3.5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Menunggu penilaian instruktur
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol aksi -->
            <div class="mt-8 flex items-center justify-center gap-4">
                <Link
                    :href="`/learn/${course.slug}`"
                    class="rounded-xl bg-primary px-6 py-2.5 font-medium text-white transition-colors hover:bg-purple-700"
                >
                    Kembali ke Kelas
                </Link>
                <Link
                    v-if="!passed && quiz.passing_score > 0"
                    :href="`/learn/${course.slug}/quiz/${quiz.id}`"
                    class="rounded-xl border border-purple-200 px-6 py-2.5 font-medium text-primary transition-colors hover:bg-purple-50 dark:border-purple-800 dark:hover:bg-purple-900/20"
                >
                    Kerjakan Ulang
                </Link>
            </div>
        </div>
    </div>
</template>
