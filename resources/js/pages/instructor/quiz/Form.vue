<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    course: { id: number; title: string };
    section: { id: number; title: string };
    quiz: {
        id: number;
        type: string;
        title: string;
        description: string | null;
        is_required: boolean;
        passing_score: number;
        questions: Array<{
            id: number;
            type: string;
            question: string;
            points: number;
            sort_order: number;
            options: Array<{
                id: number;
                option_text: string;
                is_correct: boolean;
            }>;
        }>;
    } | null;
}>();

const isEdit = !!props.quiz;

const quizTypeOptions = [
    { value: 'pretest', label: 'Pre-Test', desc: 'Dikerjakan sebelum materi' },
    { value: 'quiz', label: 'Kuis', desc: 'Latihan di tengah materi' },
    {
        value: 'posttest',
        label: 'Post-Test',
        desc: 'Dikerjakan setelah materi',
    },
];

// ── Form kuis ──────────────────────────────────────────
const quizForm = useForm({
    type: props.quiz?.type ?? 'quiz',
    title: props.quiz?.title ?? '',
    description: props.quiz?.description ?? '',
    is_required: props.quiz?.is_required ?? false,
    passing_score: props.quiz?.passing_score ?? 70,
});

function submitQuiz() {
    const base = `/instructor/courses/${props.course.id}/sections/${props.section.id}/quiz`;

    if (isEdit) {
        quizForm.put(`${base}/${props.quiz!.id}`);
    } else {
        quizForm.post(base);
    }
}

// ── Form soal ──────────────────────────────────────────
const showQuestionForm = ref(false);
const editingQuestion = ref<any>(null);

const questionForm = useForm({
    type: 'multiple_choice',
    question: '',
    points: 10,
    sort_order: 0,
    options: [
        { option_text: '', is_correct: false },
        { option_text: '', is_correct: false },
    ] as Array<{ option_text: string; is_correct: boolean }>,
});

function openQuestionForm(q: any = null) {
    editingQuestion.value = q;
    questionForm.type = q?.type ?? 'multiple_choice';
    questionForm.question = q?.question ?? '';
    questionForm.points = q?.points ?? 10;
    questionForm.sort_order = q?.sort_order ?? 0;
    questionForm.options = q?.options?.length
        ? q.options.map((o: any) => ({
              option_text: o.option_text,
              is_correct: o.is_correct,
          }))
        : [
              { option_text: '', is_correct: false },
              { option_text: '', is_correct: false },
          ];
    showQuestionForm.value = true;
}

function addOption() {
    questionForm.options.push({ option_text: '', is_correct: false });
}

function removeOption(idx: number) {
    if (questionForm.options.length <= 2) {
        return;
    }

    questionForm.options.splice(idx, 1);
}

function setCorrect(idx: number) {
    questionForm.options.forEach((o, i) => {
        o.is_correct = i === idx;
    });
}

function submitQuestion() {
    const base = `/instructor/courses/${props.course.id}/sections/${props.section.id}/quiz/${props.quiz!.id}/questions`;

    if (editingQuestion.value) {
        questionForm.put(`${base}/${editingQuestion.value.id}`, {
            onSuccess: () => {
                showQuestionForm.value = false;
            },
        });
    } else {
        questionForm.post(base, {
            onSuccess: () => {
                showQuestionForm.value = false;
                questionForm.reset();
            },
        });
    }
}

function deleteQuestion(qId: number) {
    if (!confirm('Hapus soal ini?')) {
        return;
    }

    const base = `/instructor/courses/${props.course.id}/sections/${props.section.id}/quiz/${props.quiz!.id}/questions`;
    router.delete(`${base}/${qId}`);
}

function totalPoints(): number {
    return props.quiz?.questions.reduce((sum, q) => sum + q.points, 0) ?? 0;
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Kuis' : 'Buat Kuis'" />

        <div class="mx-auto max-w-4xl space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-3">
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
                    <h1 class="text-xl font-bold">
                        {{ isEdit ? 'Edit Kuis' : 'Buat Kuis' }}
                    </h1>
                    <p class="text-sm text-gray-400">{{ section.title }}</p>
                </div>
            </div>

            <!-- Form Pengaturan Kuis -->
            <div
                class="space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <h2 class="font-semibold text-gray-700 dark:text-gray-300">
                    Pengaturan Kuis
                </h2>

                <!-- Tipe Kuis -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium">
                        Tipe Kuis <span class="text-red-500">*</span>
                    </label>
                    <Select v-model="quizForm.type">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Pilih tipe kuis..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="opt in quizTypeOptions"
                                :key="opt.value"
                                :value="opt.value"
                            >
                                <span class="font-medium">{{ opt.label }}</span>
                                <span
                                    class="ml-1.5 text-xs text-muted-foreground"
                                    >— {{ opt.desc }}</span
                                >
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p
                        v-if="quizForm.errors.type"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ quizForm.errors.type }}
                    </p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium">
                        Judul Kuis <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="quizForm.title"
                        type="text"
                        placeholder="Contoh: Pre-Test Bagian 1"
                        class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-gray-700 dark:bg-gray-950"
                    />
                    <p
                        v-if="quizForm.errors.title"
                        class="mt-1 text-xs text-red-500"
                    >
                        {{ quizForm.errors.title }}
                    </p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium"
                        >Deskripsi</label
                    >
                    <textarea
                        v-model="quizForm.description"
                        rows="2"
                        placeholder="Petunjuk pengerjaan kuis..."
                        class="w-full resize-none rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-gray-700 dark:bg-gray-950"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">
                            Nilai Minimum Lulus (%)
                        </label>
                        <input
                            v-model="quizForm.passing_score"
                            type="number"
                            min="0"
                            max="100"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-gray-700 dark:bg-gray-950"
                        />
                        <p class="mt-1 text-xs text-gray-400">
                            0 = tidak ada nilai minimum
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <input
                            v-model="quizForm.is_required"
                            type="checkbox"
                            id="is_required"
                            class="h-4 w-4 rounded text-primary focus:ring-primary/50"
                        />
                        <label for="is_required" class="cursor-pointer text-sm">
                            Kuis wajib dikerjakan
                        </label>
                    </div>
                </div>

                <div
                    class="flex items-center gap-3 border-t border-gray-100 pt-2 dark:border-gray-800"
                >
                    <button
                        @click="submitQuiz"
                        :disabled="quizForm.processing"
                        class="rounded-lg bg-primary px-6 py-2 text-sm font-medium text-white transition-colors hover:opacity-90 disabled:opacity-60"
                    >
                        {{
                            quizForm.processing
                                ? 'Menyimpan...'
                                : isEdit
                                  ? 'Simpan Pengaturan'
                                  : 'Buat Kuis'
                        }}
                    </button>
                </div>
            </div>

            <!-- Daftar Soal — hanya tampil jika quiz sudah dibuat -->
            <div
                v-if="isEdit"
                class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
            >
                <!-- Header soal -->
                <div
                    class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-800"
                >
                    <div>
                        <h2 class="font-semibold">Daftar Soal</h2>
                        <p class="mt-0.5 text-xs text-gray-400">
                            {{ quiz!.questions.length }} soal · Total
                            {{ totalPoints() }} poin
                        </p>
                    </div>
                    <button
                        @click="openQuestionForm()"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90"
                    >
                        + Tambah Soal
                    </button>
                </div>

                <!-- Empty state -->
                <div
                    v-if="quiz!.questions.length === 0"
                    class="py-12 text-center text-gray-400"
                >
                    <svg
                        class="mx-auto mb-3 h-12 w-12 opacity-30"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <p class="text-sm font-medium">Belum ada soal</p>
                    <p class="mt-1 text-xs">Klik "Tambah Soal" untuk mulai</p>
                </div>

                <!-- List soal -->
                <div
                    v-else
                    class="divide-y divide-gray-100 dark:divide-gray-800"
                >
                    <div
                        v-for="(q, idx) in quiz!.questions"
                        :key="q.id"
                        class="px-5 py-4 transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/50"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 flex-1 items-start gap-3">
                                <!-- Nomor -->
                                <span
                                    class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary dark:bg-primary/20"
                                >
                                    {{ idx + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <!-- Badge tipe -->
                                    <div class="mb-1 flex items-center gap-2">
                                        <span
                                            :class="[
                                                'rounded-full px-2 py-0.5 text-xs font-medium',
                                                q.type === 'multiple_choice'
                                                    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            ]"
                                        >
                                            {{
                                                q.type === 'multiple_choice'
                                                    ? 'Pilihan Ganda'
                                                    : 'Esai'
                                            }}
                                        </span>
                                        <span class="text-xs text-gray-400"
                                            >{{ q.points }} poin</span
                                        >
                                    </div>
                                    <!-- Teks soal -->
                                    <p class="line-clamp-2 text-sm font-medium">
                                        {{ q.question }}
                                    </p>

                                    <!-- Preview options untuk PG -->
                                    <div
                                        v-if="q.type === 'multiple_choice'"
                                        class="mt-2 space-y-1"
                                    >
                                        <div
                                            v-for="opt in q.options"
                                            :key="opt.id"
                                            :class="[
                                                'flex items-center gap-2 text-xs',
                                                opt.is_correct
                                                    ? 'font-medium text-green-600 dark:text-green-400'
                                                    : 'text-gray-400',
                                            ]"
                                        >
                                            <svg
                                                v-if="opt.is_correct"
                                                class="h-3.5 w-3.5 shrink-0"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="3"
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                            <span
                                                v-else
                                                class="inline-block h-3.5 w-3.5 shrink-0 rounded-full border border-gray-300 dark:border-gray-600"
                                            />
                                            {{ opt.option_text }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex shrink-0 items-center gap-2">
                                <button
                                    @click="openQuestionForm(q)"
                                    class="text-xs font-medium text-primary hover:underline"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteQuestion(q.id)"
                                    class="text-xs text-red-400 hover:text-red-500"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── MODAL: Form Soal ─────────────────────────────── -->
        <div
            v-if="showQuestionForm"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
            @click.self="showQuestionForm = false"
        >
            <div
                class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900"
            >
                <h2 class="mb-5 text-lg font-bold">
                    {{ editingQuestion ? 'Edit Soal' : 'Tambah Soal Baru' }}
                </h2>

                <div class="space-y-4">
                    <!-- Tipe soal (hanya bisa diubah saat create) -->
                    <div v-if="!editingQuestion">
                        <label class="mb-1.5 block text-sm font-medium"
                            >Tipe Soal</label
                        >
                        <div class="flex gap-3">
                            <label
                                v-for="opt in [
                                    {
                                        value: 'multiple_choice',
                                        label: 'Pilihan Ganda',
                                    },
                                    { value: 'essay', label: 'Esai' },
                                ]"
                                :key="opt.value"
                                :class="[
                                    'flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-xl border-2 px-4 py-2.5 text-sm font-medium transition-colors',
                                    questionForm.type === opt.value
                                        ? 'border-primary bg-primary/5 text-primary dark:bg-primary/10'
                                        : 'border-gray-200 text-gray-500 dark:border-gray-700',
                                ]"
                            >
                                <input
                                    type="radio"
                                    :value="opt.value"
                                    v-model="questionForm.type"
                                    class="hidden"
                                />
                                {{ opt.label }}
                            </label>
                        </div>
                    </div>

                    <!-- Teks soal -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">
                            Soal <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            v-model="questionForm.question"
                            rows="3"
                            placeholder="Tuliskan pertanyaan di sini..."
                            class="w-full resize-none rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-gray-700 dark:bg-gray-950"
                        />
                        <p
                            v-if="questionForm.errors.question"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ questionForm.errors.question }}
                        </p>
                    </div>

                    <!-- Poin -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium"
                            >Poin</label
                        >
                        <input
                            v-model="questionForm.points"
                            type="number"
                            min="1"
                            max="100"
                            class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-gray-700 dark:bg-gray-950"
                        />
                    </div>

                    <!-- Options — hanya untuk PG -->
                    <div v-if="questionForm.type === 'multiple_choice'">
                        <div class="mb-2 flex items-center justify-between">
                            <label class="block text-sm font-medium">
                                Pilihan Jawaban
                                <span class="text-red-500">*</span>
                            </label>
                            <button
                                @click="addOption"
                                class="text-xs font-medium text-primary hover:underline"
                            >
                                + Tambah Pilihan
                            </button>
                        </div>
                        <p class="mb-3 text-xs text-gray-400">
                            Klik lingkaran untuk menandai jawaban benar
                        </p>

                        <div class="space-y-2">
                            <div
                                v-for="(opt, idx) in questionForm.options"
                                :key="idx"
                                class="flex items-center gap-2"
                            >
                                <!-- Radio pilih jawaban benar -->
                                <button
                                    @click="setCorrect(idx)"
                                    :class="[
                                        'flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition-colors',
                                        opt.is_correct
                                            ? 'border-green-500 bg-green-500'
                                            : 'border-gray-300 hover:border-green-400 dark:border-gray-600',
                                    ]"
                                >
                                    <svg
                                        v-if="opt.is_correct"
                                        class="h-3 w-3 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="3"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </button>

                                <input
                                    v-model="opt.option_text"
                                    type="text"
                                    :placeholder="`Pilihan ${String.fromCharCode(65 + idx)}`"
                                    class="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary/50 focus:outline-none dark:border-gray-700 dark:bg-gray-950"
                                />

                                <button
                                    v-if="questionForm.options.length > 2"
                                    @click="removeOption(idx)"
                                    class="shrink-0 text-red-400 hover:text-red-500"
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
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <p
                            v-if="questionForm.errors.options"
                            class="mt-2 text-xs text-red-500"
                        >
                            {{ questionForm.errors.options }}
                        </p>
                    </div>

                    <!-- Info esai -->
                    <div
                        v-if="questionForm.type === 'essay'"
                        class="rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-900/20"
                    >
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            Jawaban esai akan dinilai manual oleh instruktur
                            setelah siswa submit kuis.
                        </p>
                    </div>
                </div>

                <div
                    class="mt-6 flex items-center gap-3 border-t border-gray-100 pt-4 dark:border-gray-800"
                >
                    <button
                        @click="submitQuestion"
                        :disabled="questionForm.processing"
                        class="rounded-lg bg-primary px-6 py-2 text-sm font-medium text-white transition-colors hover:opacity-90 disabled:opacity-60"
                    >
                        {{
                            questionForm.processing
                                ? 'Menyimpan...'
                                : 'Simpan Soal'
                        }}
                    </button>
                    <button
                        @click="showQuestionForm = false"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
