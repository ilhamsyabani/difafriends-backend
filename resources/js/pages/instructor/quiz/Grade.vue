<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    course: { id: number; title: string };
    quiz: {
        id: number;
        title: string;
        questions: Array<{ id: number; type: string; points: number }>;
    };
    attempts: {
        data: Array<{
            id: number;
            score: number | null;
            status: string;
            submitted_at: string;
            user: { first_name: string; last_name: string };
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
                };
                selected_option: {
                    option_text: string;
                    is_correct: boolean;
                } | null;
            }>;
        }>;
        links: any[];
        meta: any;
    };
}>();

const activeAttemptId = ref<number | null>(null);

function toggleAttempt(id: number) {
    activeAttemptId.value = activeAttemptId.value === id ? null : id;
}

// Form penilaian per jawaban esai
const gradeForm = useForm<
    Record<number, { points_earned: number; instructor_note: string }>
>({});

function initGradeForm(attemptId: number) {
    const attempt = props.attempts.data.find((a) => a.id === attemptId);
    if (!attempt) return;

    attempt.answers
        .filter((a) => a.question.type === 'essay')
        .forEach((a) => {
            gradeForm[a.id] = {
                points_earned: a.points_earned ?? 0,
                instructor_note: a.instructor_note ?? '',
            };
        });
}

function submitGrade(answerId: number) {
    useForm({
        points_earned: gradeForm[answerId].points_earned,
        instructor_note: gradeForm[answerId].instructor_note,
    }).post(`/instructor/quiz-answers/${answerId}/grade`, {
        preserveState: true,
        onSuccess: () => {
            // Refresh halaman untuk update status
        },
    });
}

function statusColor(status: string): string {
    return status === 'graded'
        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
}

function formatDate(dt: string): string {
    return new Date(dt).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <AppLayout>
        <Head :title="`Penilaian: ${quiz.title}`" />

        <div class="mx-auto max-w-5xl p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center gap-3">
                <Link
                    :href="`/instructor/courses/${course.id}/manage`"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-xl font-bold">Penilaian Esai</h1>
                    <p class="text-sm text-gray-400">{{ quiz.title }}</p>
                </div>
            </div>

            <!-- Empty -->
            <div
                v-if="attempts.data.length === 0"
                class="py-16 text-center text-gray-400"
            >
                <p class="font-medium">
                    Belum ada siswa yang mengerjakan kuis ini.
                </p>
            </div>

            <!-- Attempt list -->
            <div v-else class="space-y-4">
                <div
                    v-for="attempt in attempts.data"
                    :key="attempt.id"
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
                >
                    <!-- Attempt header -->
                    <button
                        @click="
                            () => {
                                toggleAttempt(attempt.id);
                                initGradeForm(attempt.id);
                            }
                        "
                        class="flex w-full items-center justify-between px-5 py-4 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/30"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 text-sm font-bold text-purple-600 dark:bg-purple-900/30"
                            >
                                {{ attempt.user.first_name.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium">
                                    {{ attempt.user.first_name }}
                                    {{ attempt.user.last_name }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    Submit:
                                    {{ formatDate(attempt.submitted_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                v-if="attempt.score !== null"
                                class="text-lg font-bold text-purple-600"
                            >
                                {{ attempt.score }}%
                            </span>
                            <span
                                :class="[
                                    'rounded-full px-2.5 py-1 text-xs font-medium',
                                    statusColor(attempt.status),
                                ]"
                            >
                                {{
                                    attempt.status === 'graded'
                                        ? 'Sudah Dinilai'
                                        : 'Perlu Dinilai'
                                }}
                            </span>
                            <svg
                                :class="[
                                    'h-4 w-4 text-gray-400 transition-transform',
                                    activeAttemptId === attempt.id
                                        ? 'rotate-180'
                                        : '',
                                ]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </div>
                    </button>

                    <!-- Jawaban detail -->
                    <div
                        v-if="activeAttemptId === attempt.id"
                        class="divide-y divide-gray-100 border-t border-gray-100 dark:divide-gray-700 dark:border-gray-700"
                    >
                        <div
                            v-for="(answer, idx) in attempt.answers"
                            :key="answer.id"
                            class="px-5 py-4"
                        >
                            <!-- Nomor & soal -->
                            <div class="mb-3 flex items-start gap-2">
                                <span
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-400"
                                >
                                    {{ idx + 1 }}
                                </span>
                                <div class="flex-1">
                                    <div class="mb-1 flex items-center gap-2">
                                        <span
                                            :class="[
                                                'rounded-full px-2 py-0.5 text-xs font-medium',
                                                answer.question.type ===
                                                'multiple_choice'
                                                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            ]"
                                        >
                                            {{
                                                answer.question.type ===
                                                'multiple_choice'
                                                    ? 'Pilihan Ganda'
                                                    : 'Esai'
                                            }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ answer.question.points }} poin
                                        </span>
                                    </div>
                                    <p class="text-sm font-medium">
                                        {{ answer.question.question }}
                                    </p>
                                </div>
                            </div>

                            <!-- Jawaban PG -->
                            <div
                                v-if="
                                    answer.question.type === 'multiple_choice'
                                "
                                class="ml-8"
                            >
                                <p class="mb-1 text-xs text-gray-500">
                                    Jawaban siswa:
                                </p>
                                <div
                                    :class="[
                                        'flex items-center gap-2 rounded-lg px-3 py-2 text-sm',
                                        answer.selected_option?.is_correct
                                            ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400'
                                            : 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400',
                                    ]"
                                >
                                    <svg
                                        class="h-4 w-4 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            v-if="
                                                answer.selected_option
                                                    ?.is_correct
                                            "
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                        <path
                                            v-else
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                    {{
                                        answer.selected_option?.option_text ??
                                        'Tidak dijawab'
                                    }}
                                    <span class="ml-auto font-semibold">
                                        {{ answer.points_earned ?? 0 }} /
                                        {{ answer.question.points }} poin
                                    </span>
                                </div>
                            </div>

                            <!-- Jawaban Esai + Form Penilaian -->
                            <div v-else class="ml-8 space-y-3">
                                <div>
                                    <p class="mb-1 text-xs text-gray-500">
                                        Jawaban siswa:
                                    </p>
                                    <div
                                        class="rounded-xl bg-gray-50 p-3 text-sm leading-relaxed text-gray-700 dark:bg-gray-700/50 dark:text-gray-300"
                                    >
                                        {{
                                            answer.essay_answer ||
                                            '(Tidak dijawab)'
                                        }}
                                    </div>
                                </div>

                                <!-- Form nilai (jika belum dinilai atau sedang edit) -->
                                <div
                                    v-if="gradeForm[answer.id]"
                                    class="space-y-3 rounded-xl bg-purple-50 p-4 dark:bg-purple-900/10"
                                >
                                    <p
                                        class="text-xs font-medium text-purple-700 dark:text-purple-400"
                                    >
                                        Penilaian Instruktur
                                    </p>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label
                                                class="mb-1 block text-xs font-medium"
                                            >
                                                Nilai (0–{{
                                                    answer.question.points
                                                }})
                                            </label>
                                            <input
                                                v-model="
                                                    gradeForm[answer.id]
                                                        .points_earned
                                                "
                                                type="number"
                                                :min="0"
                                                :max="answer.question.points"
                                                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                                            />
                                        </div>
                                        <div class="flex items-end">
                                            <button
                                                @click="submitGrade(answer.id)"
                                                class="w-full rounded-lg bg-purple-600 py-2 text-sm font-medium text-white transition-colors hover:bg-purple-700"
                                            >
                                                Simpan Nilai
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1 block text-xs font-medium"
                                        >
                                            Catatan / Feedback
                                            <span
                                                class="font-normal text-gray-400"
                                                >(opsional)</span
                                            >
                                        </label>
                                        <textarea
                                            v-model="
                                                gradeForm[answer.id]
                                                    .instructor_note
                                            "
                                            rows="2"
                                            placeholder="Berikan feedback untuk jawaban ini..."
                                            class="w-full resize-none rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                                        />
                                    </div>

                                    <!-- Nilai yang sudah tersimpan -->
                                    <div
                                        v-if="answer.points_earned !== null"
                                        class="flex items-center gap-2 text-xs text-green-600 dark:text-green-400"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                        Sudah dinilai:
                                        {{ answer.points_earned }} /
                                        {{ answer.question.points }} poin
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
