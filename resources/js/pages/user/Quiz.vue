<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, computed } from 'vue';

const props = defineProps<{
    course: { id: number; title: string; slug: string };
    quiz: {
        id: number;
        title: string;
        description: string | null;
        passing_score: number;
        questions: Array<{
            id: number;
            type: string;
            question: string;
            points: number;
            sort_order: number;
            options: Array<{ id: number; option_text: string }>;
        }>;
    };
    pendingAttempt: { id: number } | null;
    lastAttempt: any | null;
    canRetake: boolean;
}>();

// State
const attemptId = ref<number | null>(props.pendingAttempt?.id ?? null);
const isStarted = ref(!!props.pendingAttempt);
const isSubmitted = ref(false);
const isLoading = ref(false);
const currentIdx = ref(0);

// Jawaban siswa: { question_id → answer }
const answers = ref<
    Record<
        number,
        {
            question_id: number;
            selected_option_id: number | null;
            essay_answer: string;
        }
    >
>({});

// Init answers
function initAnswers() {
    props.quiz.questions.forEach((q) => {
        answers.value[q.id] = {
            question_id: q.id,
            selected_option_id: null,
            essay_answer: '',
        };
    });
}

const currentQuestion = computed(() => props.quiz.questions[currentIdx.value]);

const totalPoints = computed(() =>
    props.quiz.questions.reduce((sum, q) => sum + q.points, 0),
);

const answeredCount = computed(
    () =>
        Object.values(answers.value).filter(
            (a) =>
                a.selected_option_id !== null || a.essay_answer.trim() !== '',
        ).length,
);

const progressPercent = computed(() =>
    props.quiz.questions.length > 0
        ? Math.round((answeredCount.value / props.quiz.questions.length) * 100)
        : 0,
);

// Mulai kuis
async function startQuiz() {
    isLoading.value = true;

    try {
        const res = await axios.post(
            `/learn/${props.course.slug}/quiz/${props.quiz.id}/start`,
        );
        attemptId.value = res.data.attempt_id;
        isStarted.value = true;
        initAnswers();
    } catch (_e) {
        alert('Gagal memulai kuis. Coba lagi.');
    } finally {
        isLoading.value = false;
    }
}

// Submit kuis
async function submitQuiz() {
    if (
        !confirm(
            'Yakin ingin submit kuis? Jawaban tidak bisa diubah setelah submit.',
        )
    ) {
        return;
    }

    isLoading.value = true;

    try {
        const res = await axios.post(
            `/learn/${props.course.slug}/quiz/${props.quiz.id}/submit`,
            {
                attempt_id: attemptId.value,
                answers: Object.values(answers.value),
            },
        );

        if (res.data.has_essay) {
            // Ada esai — tampilkan pesan tunggu penilaian
            isSubmitted.value = true;
        } else {
            // Semua PG — langsung ke halaman hasil
            router.visit(
                `/learn/${props.course.slug}/quiz/${props.quiz.id}/result`,
            );
        }
    } catch (_e) {
        alert('Gagal submit kuis. Coba lagi.');
    } finally {
        isLoading.value = false;
    }
}

function goToQuestion(idx: number) {
    if (idx >= 0 && idx < props.quiz.questions.length) {
        currentIdx.value = idx;
    }
}

function isAnswered(questionId: number): boolean {
    const a = answers.value[questionId];

    if (!a) {
        return false;
    }

    return a.selected_option_id !== null || a.essay_answer.trim() !== '';
}
</script>

<template>
    <Head :title="`${quiz.title} — ${course.title}`" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <!-- Navbar quiz -->
        <header
            class="sticky top-0 z-40 border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900"
        >
            <div
                class="mx-auto flex h-14 max-w-4xl items-center justify-between px-4"
            >
                <div class="flex items-center gap-3">
                    <Link
                        :href="`/learn/${course.slug}`"
                        class="text-gray-400 transition-colors hover:text-gray-600"
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
                        <p class="text-sm font-semibold">{{ quiz.title }}</p>
                        <p class="text-xs text-gray-400">{{ course.title }}</p>
                    </div>
                </div>

                <!-- Progress bar -->
                <div
                    v-if="isStarted && !isSubmitted"
                    class="flex items-center gap-3"
                >
                    <span class="text-xs text-gray-500">
                        {{ answeredCount }}/{{ quiz.questions.length }} dijawab
                    </span>
                    <div
                        class="h-2 w-24 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                    >
                        <div
                            class="h-2 rounded-full bg-purple-500 transition-all"
                            :style="{ width: progressPercent + '%' }"
                        />
                    </div>
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-4xl px-4 py-8">
            <!-- ── BELUM MULAI ────────────────────────────────── -->
            <div v-if="!isStarted && !isSubmitted">
                <!-- Info kuis -->
                <div
                    class="rounded-2xl border border-gray-100 bg-white p-8 text-center dark:border-gray-700 dark:bg-gray-800"
                >
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30"
                    >
                        <svg
                            class="h-8 w-8 text-primary"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                            />
                        </svg>
                    </div>

                    <h1 class="mb-2 text-2xl font-bold">{{ quiz.title }}</h1>

                    <p
                        v-if="quiz.description"
                        class="mb-6 text-gray-500 dark:text-gray-400"
                    >
                        {{ quiz.description }}
                    </p>

                    <!-- Stats kuis -->
                    <div class="mx-auto mb-8 grid max-w-xs grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-primary">
                                {{ quiz.questions.length }}
                            </p>
                            <p class="text-xs text-gray-400">Soal</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-primary">
                                {{ totalPoints }}
                            </p>
                            <p class="text-xs text-gray-400">Total Poin</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-primary">
                                {{
                                    quiz.passing_score > 0
                                        ? quiz.passing_score + '%'
                                        : '—'
                                }}
                            </p>
                            <p class="text-xs text-gray-400">Nilai Lulus</p>
                        </div>
                    </div>

                    <!-- Hasil attempt sebelumnya -->
                    <div
                        v-if="lastAttempt"
                        class="mb-6 rounded-xl bg-gray-50 p-4 text-sm dark:bg-gray-700/50"
                    >
                        <p class="mb-1 font-medium">Hasil sebelumnya:</p>
                        <p v-if="lastAttempt.status === 'graded'">
                            Nilai:
                            <span class="font-bold text-primary"
                                >{{ lastAttempt.score }}%</span
                            >
                        </p>
                        <p v-else class="text-amber-600 dark:text-amber-400">
                            Menunggu penilaian instruktur...
                        </p>
                    </div>

                    <button
                        @click="startQuiz"
                        :disabled="isLoading"
                        class="rounded-xl bg-primary px-8 py-3 font-semibold text-white transition-colors hover:bg-purple-700 disabled:opacity-60"
                    >
                        {{
                            isLoading
                                ? 'Memulai...'
                                : lastAttempt
                                  ? 'Kerjakan Ulang'
                                  : 'Mulai Kuis'
                        }}
                    </button>
                </div>
            </div>

            <!-- ── SEDANG MENGERJAKAN ──────────────────────────── -->
            <div v-else-if="isStarted && !isSubmitted">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                    <!-- Navigator soal (kiri) -->
                    <div class="lg:col-span-1">
                        <div
                            class="sticky top-20 rounded-2xl border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <p
                                class="mb-3 text-xs font-medium tracking-wide text-gray-500 uppercase"
                            >
                                Navigasi Soal
                            </p>
                            <div class="grid grid-cols-5 gap-2 lg:grid-cols-3">
                                <button
                                    v-for="(q, idx) in quiz.questions"
                                    :key="q.id"
                                    @click="goToQuestion(idx)"
                                    :class="[
                                        'aspect-square w-full rounded-lg text-sm font-medium transition-colors',
                                        currentIdx === idx
                                            ? 'bg-primary text-white'
                                            : isAnswered(q.id)
                                              ? 'bg-purple-100 text-primary-hover dark:bg-purple-900/30 dark:text-purple-300'
                                              : 'bg-gray-100 text-gray-500 dark:bg-gray-700',
                                    ]"
                                >
                                    {{ idx + 1 }}
                                </button>
                            </div>
                            <div
                                class="mt-4 space-y-1 border-t border-gray-100 pt-4 text-xs dark:border-gray-700"
                            >
                                <div class="flex items-center gap-2">
                                    <div class="h-3 w-3 rounded bg-primary" />
                                    <span class="text-gray-400"
                                        >Sedang dikerjakan</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-3 w-3 rounded bg-purple-100 dark:bg-purple-900/30"
                                    />
                                    <span class="text-gray-400"
                                        >Sudah dijawab</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-3 w-3 rounded bg-gray-100 dark:bg-gray-700"
                                    />
                                    <span class="text-gray-400"
                                        >Belum dijawab</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Soal aktif (kanan) -->
                    <div class="space-y-4 lg:col-span-3">
                        <div
                            v-if="currentQuestion"
                            class="rounded-2xl border border-gray-100 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <!-- Header soal -->
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Soal {{ currentIdx + 1 }} dari
                                        {{ quiz.questions.length }}
                                    </span>
                                    <span
                                        :class="[
                                            'rounded-full px-2 py-0.5 text-xs font-medium',
                                            currentQuestion.type ===
                                            'multiple_choice'
                                                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                                : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        ]"
                                    >
                                        {{
                                            currentQuestion.type ===
                                            'multiple_choice'
                                                ? 'Pilihan Ganda'
                                                : 'Esai'
                                        }}
                                    </span>
                                </div>
                                <span
                                    class="text-sm font-semibold text-primary"
                                >
                                    {{ currentQuestion.points }} poin
                                </span>
                            </div>

                            <!-- Teks soal -->
                            <p
                                class="mb-6 text-base leading-relaxed font-medium"
                            >
                                {{ currentQuestion.question }}
                            </p>

                            <!-- Pilihan Ganda -->
                            <div
                                v-if="
                                    currentQuestion.type === 'multiple_choice'
                                "
                                class="space-y-3"
                            >
                                <label
                                    v-for="opt in currentQuestion.options"
                                    :key="opt.id"
                                    :class="[
                                        'flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition-colors',
                                        answers[currentQuestion.id]
                                            ?.selected_option_id === opt.id
                                            ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                                            : 'border-gray-200 hover:border-purple-300 dark:border-gray-700 dark:hover:border-purple-700',
                                    ]"
                                >
                                    <input
                                        type="radio"
                                        :name="`q_${currentQuestion.id}`"
                                        :value="opt.id"
                                        v-model="
                                            answers[currentQuestion.id]
                                                .selected_option_id
                                        "
                                        class="hidden"
                                    />
                                    <div
                                        :class="[
                                            'flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2',
                                            answers[currentQuestion.id]
                                                ?.selected_option_id === opt.id
                                                ? 'border-purple-500 bg-purple-500'
                                                : 'border-gray-300 dark:border-gray-600',
                                        ]"
                                    >
                                        <div
                                            v-if="
                                                answers[currentQuestion.id]
                                                    ?.selected_option_id ===
                                                opt.id
                                            "
                                            class="h-2 w-2 rounded-full bg-white"
                                        />
                                    </div>
                                    <span class="text-sm">{{
                                        opt.option_text
                                    }}</span>
                                </label>
                            </div>

                            <!-- Esai -->
                            <div v-else>
                                <textarea
                                    v-model="
                                        answers[currentQuestion.id].essay_answer
                                    "
                                    rows="6"
                                    placeholder="Tuliskan jawaban kamu di sini..."
                                    class="w-full resize-none rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900"
                                />
                                <p class="mt-2 text-xs text-gray-400">
                                    Jawaban esai akan dinilai oleh instruktur
                                    setelah kamu submit.
                                </p>
                            </div>
                        </div>

                        <!-- Navigasi prev/next -->
                        <div class="flex items-center justify-between">
                            <button
                                v-if="currentIdx > 0"
                                @click="goToQuestion(currentIdx - 1)"
                                class="flex items-center gap-2 text-sm text-gray-500 transition-colors hover:text-gray-700"
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
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                                Sebelumnya
                            </button>
                            <div v-else />

                            <!-- Submit jika soal terakhir -->
                            <button
                                v-if="currentIdx === quiz.questions.length - 1"
                                @click="submitQuiz"
                                :disabled="isLoading"
                                class="rounded-xl bg-primary px-6 py-2.5 font-semibold text-white transition-colors hover:bg-purple-700 disabled:opacity-60"
                            >
                                {{
                                    isLoading ? 'Submitting...' : 'Submit Kuis'
                                }}
                            </button>
                            <button
                                v-else
                                @click="goToQuestion(currentIdx + 1)"
                                class="flex items-center gap-2 text-sm font-medium text-primary transition-colors hover:text-primary-hover"
                            >
                                Berikutnya
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
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SETELAH SUBMIT (ada esai) ───────────────────── -->
            <div v-else-if="isSubmitted">
                <div
                    class="mx-auto max-w-md rounded-2xl border border-gray-100 bg-white p-8 text-center dark:border-gray-700 dark:bg-gray-800"
                >
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30"
                    >
                        <svg
                            class="h-8 w-8 text-amber-600"
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
                    </div>
                    <h2 class="mb-2 text-xl font-bold">
                        Kuis Berhasil Disubmit!
                    </h2>
                    <p class="mb-6 text-gray-500 dark:text-gray-400">
                        Kuis kamu berisi soal esai yang perlu dinilai oleh
                        instruktur. Hasil akan tersedia setelah instruktur
                        selesai menilai.
                    </p>
                    <Link
                        :href="`/learn/${course.slug}`"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 font-medium text-white transition-colors hover:bg-purple-700"
                    >
                        Kembali ke Kelas
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
