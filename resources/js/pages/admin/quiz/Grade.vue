<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { grade as gradeAnswer } from '@/actions/App/Http/Controllers/Admin/QuizGradeController';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    course: { id: number; title: string; slug: string };
    quiz: {
        id: number;
        title: string;
        passing_score: number;
        questions: Array<{ id: number; type: string; points: number }>;
    };
    attempts: {
        data: Array<{
            id: number;
            score: number | null;
            status: string;
            submitted_at: string | null;
            user: { first_name: string; last_name: string; email: string };
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
        meta: { current_page: number; last_page: number; total: number };
    };
}>();

const activeAttemptId = ref<number | null>(null);

// gradeForm: keyed by answerId
const gradeForm = ref<
    Record<number, { points_earned: number; instructor_note: string }>
>({});

function toggleAttempt(attemptId: number) {
    if (activeAttemptId.value === attemptId) {
        activeAttemptId.value = null;

        return;
    }

    activeAttemptId.value = attemptId;

    const attempt = props.attempts.data.find((a) => a.id === attemptId);

    if (!attempt) {
return;
}

    attempt.answers
        .filter((a) => a.question.type === 'essay')
        .forEach((a) => {
            if (gradeForm.value[a.id] === undefined) {
                gradeForm.value[a.id] = {
                    points_earned: a.points_earned ?? 0,
                    instructor_note: a.instructor_note ?? '',
                };
            }
        });
}

function submitGrade(answerId: number) {
    useForm({
        points_earned: gradeForm.value[answerId].points_earned,
        instructor_note: gradeForm.value[answerId].instructor_note,
    }).post(gradeAnswer.url(answerId), {
        preserveScroll: true,
    });
}

const pendingCount = (attempt: (typeof props.attempts.data)[number]) =>
    attempt.answers.filter(
        (a) => a.question.type === 'essay' && a.points_earned === null,
    ).length;

function statusBadge(status: string): string {
    return status === 'graded'
        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
}

function formatDate(dt: string | null): string {
    if (!dt) {
return '—';
}

    return new Date(dt).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function initials(first: string, last: string): string {
    return (first.charAt(0) + last.charAt(0)).toUpperCase();
}
</script>

<template>
    <AppLayout>
        <Head :title="`Penilaian Kuis: ${quiz.title}`" />

        <div class="mx-auto max-w-5xl p-6">
            <!-- Header -->
            <div class="mb-6 flex items-center gap-3">
                <Link
                    href="/admin/courses"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800"
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
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold">Penilaian Kuis</h1>
                        <span
                            class="rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400"
                        >
                            Admin
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ course.title }} &mdash; {{ quiz.title }}
                    </p>
                </div>
            </div>

            <!-- Summary bar -->
            <div class="mb-6 grid grid-cols-3 gap-4">
                <div
                    class="rounded-2xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                >
                    <p
                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                    >
                        Total Pengerjaan
                    </p>
                    <p class="mt-1 text-2xl font-bold">
                        {{ attempts.meta.total }}
                    </p>
                </div>
                <div
                    class="rounded-2xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                >
                    <p
                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                    >
                        Sudah Dinilai
                    </p>
                    <p class="mt-1 text-2xl font-bold text-green-600">
                        {{
                            attempts.data.filter((a) => a.status === 'graded')
                                .length
                        }}
                    </p>
                </div>
                <div
                    class="rounded-2xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                >
                    <p
                        class="text-xs font-medium text-gray-500 dark:text-gray-400"
                    >
                        Perlu Dinilai
                    </p>
                    <p class="mt-1 text-2xl font-bold text-amber-600">
                        {{
                            attempts.data.filter((a) => a.status !== 'graded')
                                .length
                        }}
                    </p>
                </div>
            </div>

            <!-- Empty state -->
            <div
                v-if="attempts.data.length === 0"
                class="rounded-2xl border border-dashed border-gray-200 py-20 text-center dark:border-gray-700"
            >
                <svg
                    class="mx-auto mb-3 h-10 w-10 text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />
                </svg>
                <p class="font-medium text-gray-400">
                    Belum ada siswa yang mengerjakan kuis ini.
                </p>
            </div>

            <!-- Attempt list -->
            <div v-else class="space-y-3">
                <div
                    v-for="attempt in attempts.data"
                    :key="attempt.id"
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
                >
                    <!-- Attempt header (toggle) -->
                    <button
                        @click="toggleAttempt(attempt.id)"
                        class="flex w-full items-center justify-between px-5 py-4 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/30"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Avatar inisial -->
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100 text-sm font-bold text-purple-600 dark:bg-purple-900/30 dark:text-purple-400"
                            >
                                {{
                                    initials(
                                        attempt.user.first_name,
                                        attempt.user.last_name,
                                    )
                                }}
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold">
                                    {{ attempt.user.first_name }}
                                    {{ attempt.user.last_name }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ attempt.user.email }} &bull; Submit:
                                    {{ formatDate(attempt.submitted_at) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            <!-- Esai pending -->
                            <span
                                v-if="pendingCount(attempt) > 0"
                                class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400"
                            >
                                {{ pendingCount(attempt) }} esai belum dinilai
                            </span>

                            <!-- Skor -->
                            <span
                                v-if="attempt.score !== null"
                                class="text-lg font-bold"
                                :class="
                                    attempt.score >= quiz.passing_score
                                        ? 'text-green-600'
                                        : 'text-red-500'
                                "
                            >
                                {{ attempt.score }}%
                            </span>

                            <!-- Status badge -->
                            <span
                                :class="[
                                    'rounded-full px-2.5 py-1 text-xs font-medium',
                                    statusBadge(attempt.status),
                                ]"
                            >
                                {{
                                    attempt.status === 'graded'
                                        ? 'Sudah Dinilai'
                                        : 'Perlu Dinilai'
                                }}
                            </span>

                            <!-- Chevron -->
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

                    <!-- Detail jawaban -->
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
                                        <span class="text-xs text-gray-400"
                                            >{{
                                                answer.question.points
                                            }}
                                            poin</span
                                        >
                                    </div>
                                    <p class="text-sm font-medium">
                                        {{ answer.question.question }}
                                    </p>
                                </div>
                            </div>

                            <!-- Pilihan Ganda — auto-graded -->
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

                            <!-- Esai — manual grading -->
                            <div v-else class="ml-8 space-y-3">
                                <!-- Jawaban siswa -->
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

                                <!-- Form penilaian -->
                                <div
                                    v-if="gradeForm[answer.id]"
                                    class="space-y-3 rounded-xl bg-purple-50 p-4 dark:bg-purple-900/10"
                                >
                                    <p
                                        class="text-xs font-medium text-purple-700 dark:text-purple-400"
                                    >
                                        Penilaian Admin
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
                                                v-model.number="
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

                                    <!-- Nilai tersimpan -->
                                    <div
                                        v-if="answer.points_earned !== null"
                                        class="flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400"
                                    >
                                        <svg
                                            class="h-4 w-4 shrink-0"
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

            <!-- Pagination -->
            <div
                v-if="attempts.meta.last_page > 1"
                class="mt-6 flex justify-center gap-2"
            >
                <template v-for="link in attempts.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        preserve-scroll
                        :class="[
                            'rounded-lg px-3 py-1.5 text-sm transition-colors',
                            link.active
                                ? 'bg-purple-600 text-white'
                                : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700',
                        ]"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="rounded-lg px-3 py-1.5 text-sm text-gray-300 dark:text-gray-600"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
