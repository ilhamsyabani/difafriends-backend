<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Plus,
    Edit2,
    Trash2,
    GripVertical,
    FileVideo,
    FileText,
    CheckCircle2,
    BookText,
    Loader2,
} from 'lucide-vue-next';
import { ref } from 'vue';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { useConfirm } from '@/composables/useConfirm';

const confirm = useConfirm();

const props = defineProps<{
    course: {
        id: number;
        title: string;
        slug: string;
        sections: Array<{
            id: number;
            title: string;
            sort_order: number;
            quiz?: { id: number; title: string } | null; // ✅ tambah title
            lectures: Array<{
                id: number;
                title: string;
                type: string;
                url: string | null;
                content: string | null;
                video_duration: number;
                is_free_preview: boolean;
                sort_order: number;
            }>;
        }>;
    };
}>();

const showSectionForm = ref(false);
const editingSection = ref<any>(null);

const sectionForm = useForm({ title: '', sort_order: 0 });

function openSectionForm(section: any = null) {
    editingSection.value = section;
    sectionForm.title = section?.title ?? '';
    sectionForm.sort_order = section?.sort_order ?? 0;
    showSectionForm.value = true;
}

function submitSection() {
    if (editingSection.value) {
        sectionForm.put(
            `/instructor/courses/${props.course.id}/sections/${editingSection.value.id}`,
            {
                onSuccess: () => {
                    showSectionForm.value = false;
                },
            },
        );
    } else {
        sectionForm.post(`/instructor/courses/${props.course.id}/sections`, {
            onSuccess: () => {
                showSectionForm.value = false;
                sectionForm.reset();
            },
        });
    }
}

async function deleteSection(sectionId: number) {
    const ok = await confirm('Hapus Bagian', 'Yakin ingin menghapus bagian ini beserta semua materinya?');
    if (!ok) return;
    router.delete(
        `/instructor/courses/${props.course.id}/sections/${sectionId}`,
    );
}

const showLectureForm = ref(false);
const activeSectionId = ref<number | null>(null);
const editingLecture = ref<any>(null);

const lectureForm = useForm({
    title: '',
    type: 'video',
    url: '',
    content: '',
    video_duration: 0,
    is_free_preview: false,
    sort_order: 0,
});

function openLectureForm(sectionId: number, lecture: any = null) {
    activeSectionId.value = sectionId;
    editingLecture.value = lecture;
    lectureForm.title = lecture?.title ?? '';
    lectureForm.type = lecture?.type ?? 'video';
    lectureForm.url = lecture?.url ?? '';
    lectureForm.content = lecture?.content ?? '';
    lectureForm.video_duration = lecture?.video_duration ?? 0;
    lectureForm.is_free_preview = lecture?.is_free_preview ?? false;
    lectureForm.sort_order = lecture?.sort_order ?? 0;
    showLectureForm.value = true;
}

function submitLecture() {
    const base = `/instructor/courses/${props.course.id}/sections/${activeSectionId.value}/lectures`;

    if (editingLecture.value) {
        lectureForm.put(`${base}/${editingLecture.value.id}`, {
            onSuccess: () => {
                showLectureForm.value = false;
            },
        });
    } else {
        lectureForm.post(base, {
            onSuccess: () => {
                showLectureForm.value = false;
                lectureForm.reset();
            },
        });
    }
}

async function deleteLecture(sectionId: number, lectureId: number) {
    const ok = await confirm('Hapus Materi', 'Yakin ingin menghapus materi ini?');
    if (!ok) return;
    router.delete(
        `/instructor/courses/${props.course.id}/sections/${sectionId}/lectures/${lectureId}`,
    );
}

function formatDuration(seconds: number): string {
    const m = Math.floor(seconds / 60);
    const h = Math.floor(m / 60);

    return h > 0 ? `${h}j ${m % 60}m` : `${m}m`;
}
</script>

<template>
    <AppLayout>
        <Head :title="`Kurikulum: ${course.title}`" />

        <div class="mx-auto max-w-7xl p-6 sm:p-10">
            <!-- Header halaman -->
            <div
                class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-4">
                    <Link
                        href="/instructor/courses"
                        class="group flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-all hover:border-purple-200 hover:bg-purple-50 hover:text-primary dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-purple-400"
                    >
                        <ArrowLeft
                            class="h-5 w-5 transition-transform group-hover:-translate-x-1"
                        />
                    </Link>
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            Kurikulum Kelas
                        </h1>
                        <p
                            class="mt-1 line-clamp-1 text-sm font-medium text-primary dark:text-purple-400"
                        >
                            {{ course.title }}
                        </p>
                    </div>
                </div>

                <button
                    @click="openSectionForm()"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                >
                    <Plus class="h-4 w-4" />
                    Tambah Bagian Baru
                </button>
            </div>

            <!-- Empty state -->
            <div
                v-if="course.sections.length === 0"
                class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-gray-200 bg-gray-50 py-20 dark:border-gray-800 dark:bg-gray-900/50"
            >
                <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 text-primary dark:bg-purple-900/50 dark:text-purple-400"
                >
                    <BookText class="h-6 w-6" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    Belum Ada Kurikulum
                </h3>
                <p
                    class="mt-1 max-w-sm text-center text-sm text-gray-500 dark:text-gray-400"
                >
                    Mulailah dengan membuat "Bagian" (Section) pertama untuk
                    mengelompokkan materi kelas Anda.
                </p>
            </div>

            <!-- Sections list -->
            <div v-else class="space-y-6">
                <div
                    v-for="(section, index) in course.sections"
                    :key="section.id"
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
                >
                    <!-- Section header -->
                    <div
                        class="flex flex-col gap-4 border-b border-gray-100 bg-gray-50/80 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800 dark:bg-gray-800/50"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-sm font-bold text-gray-700 shadow-sm dark:bg-gray-700 dark:text-gray-300"
                            >
                                {{ index + 1 }}
                            </div>
                            <div>
                                <h3
                                    class="text-base font-bold text-gray-900 dark:text-white"
                                >
                                    Bagian {{ section.sort_order }}:
                                    {{ section.title }}
                                </h3>
                                <p
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                >
                                    {{ section.lectures.length }} Materi di
                                    bagian ini
                                    <!-- ✅ Tampilkan nama kuis jika ada -->
                                    <span
                                        v-if="section.quiz"
                                        class="ml-2 text-amber-600 dark:text-amber-400"
                                    >
                                        · Kuis: {{ section.quiz.title }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- ✅ Action buttons — termasuk tombol kuis -->
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Buat Kuis (jika belum ada) -->
                            <Link
                                v-if="!section.quiz"
                                :href="`/instructor/courses/${course.id}/sections/${section.id}/quiz/create`"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700 transition-colors hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50"
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
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                                Buat Kuis
                            </Link>

                            <!-- Edit Kuis + Nilai Esai (jika sudah ada kuis) -->
                            <template v-if="section.quiz">
                                <Link
                                    :href="`/instructor/courses/${course.id}/sections/${section.id}/quiz/${section.quiz.id}/edit`"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700 transition-colors hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50"
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
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        />
                                    </svg>
                                    Edit Kuis
                                </Link>
                                <Link
                                    :href="`/instructor/courses/${course.id}/quizzes/${section.quiz.id}/grade`"
                                    class="text-primary-hover inline-flex items-center gap-1.5 rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-medium transition-colors hover:bg-purple-100 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50"
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
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                        />
                                    </svg>
                                    Nilai Esai
                                </Link>
                            </template>

                            <!-- Tombol + Materi -->
                            <button
                                @click="openLectureForm(section.id)"
                                class="text-primary-hover inline-flex items-center gap-1.5 rounded-xl border border-purple-200 bg-purple-50 px-3.5 py-1.5 text-sm font-semibold transition-colors hover:bg-purple-100 dark:border-purple-800/50 dark:bg-purple-900/30 dark:text-purple-300 dark:hover:bg-purple-900/50"
                            >
                                <Plus class="h-4 w-4" />
                                Materi
                            </button>

                            <div
                                class="h-6 w-px bg-gray-200 dark:bg-gray-700"
                            />

                            <button
                                @click="openSectionForm(section)"
                                class="p-2 text-gray-400 hover:text-primary dark:hover:text-purple-400"
                                title="Edit Bagian"
                            >
                                <Edit2 class="h-4 w-4" />
                            </button>
                            <button
                                @click="deleteSection(section.id)"
                                class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400"
                                title="Hapus Bagian"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Lecture list -->
                    <div class="p-2">
                        <div
                            v-if="section.lectures.length === 0"
                            class="px-4 py-6 text-center text-sm text-gray-400 dark:text-gray-500"
                        >
                            Belum ada materi di bagian ini.
                        </div>

                        <div class="space-y-1">
                            <div
                                v-for="lecture in section.lectures"
                                :key="lecture.id"
                                class="group flex items-center justify-between rounded-xl border border-transparent p-3 transition-colors hover:border-gray-100 hover:bg-gray-50 dark:hover:border-gray-800 dark:hover:bg-gray-800/30"
                            >
                                <div class="flex items-center gap-3">
                                    <GripVertical
                                        class="h-4 w-4 cursor-grab text-gray-300 hover:text-gray-500 dark:text-gray-600"
                                    />
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                    >
                                        <FileVideo
                                            v-if="lecture.type === 'video'"
                                            class="h-4 w-4"
                                        />
                                        <FileText
                                            v-else-if="lecture.type === 'text'"
                                            class="h-4 w-4"
                                        />
                                        <CheckCircle2 v-else class="h-4 w-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ lecture.sort_order }}.
                                                {{ lecture.title }}
                                            </span>
                                            <span
                                                v-if="lecture.is_free_preview"
                                                class="rounded border border-green-200 bg-green-50 px-1.5 py-0.5 text-[10px] font-bold tracking-wider text-green-600 uppercase dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-400"
                                            >
                                                Free
                                            </span>
                                        </div>
                                        <span
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{
                                                lecture.type === 'video'
                                                    ? formatDuration(
                                                          lecture.video_duration,
                                                      )
                                                    : lecture.type === 'text'
                                                      ? 'Artikel'
                                                      : 'Kuis'
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <button
                                        @click="
                                            openLectureForm(section.id, lecture)
                                        "
                                        class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-white hover:text-primary hover:shadow-sm dark:hover:bg-gray-700 dark:hover:text-purple-400"
                                    >
                                        <Edit2 class="h-4 w-4" />
                                    </button>
                                    <button
                                        @click="
                                            deleteLecture(
                                                section.id,
                                                lecture.id,
                                            )
                                        "
                                        class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-white hover:text-red-500 hover:shadow-sm dark:hover:bg-gray-700 dark:hover:text-red-400"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Section Form — sama persis, tidak berubah -->
        <div
            v-if="showSectionForm"
            class="relative z-50"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"
                @click="showSectionForm = false"
            />
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0"
                >
                    <div
                        class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left shadow-xl dark:border dark:border-gray-800 dark:bg-gray-900"
                    >
                        <h3
                            class="mb-6 text-lg font-bold text-gray-900 dark:text-white"
                        >
                            {{
                                editingSection
                                    ? 'Edit Bagian (Section)'
                                    : 'Tambah Bagian Baru'
                            }}
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Judul Bagian
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="sectionForm.title"
                                    type="text"
                                    placeholder="Contoh: Pengenalan Dasar"
                                    class="w-full rounded-xl"
                                    @keyup.enter="submitSection"
                                />
                                <p
                                    v-if="sectionForm.errors.title"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ sectionForm.errors.title }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Urutan Tampil
                                </label>
                                <Input
                                    v-model="sectionForm.sort_order"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-xl"
                                />
                            </div>
                        </div>
                        <div class="mt-8 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                @click="showSectionForm = false"
                                class="rounded-xl px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                @click="submitSection"
                                :disabled="sectionForm.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-purple-700 disabled:opacity-70"
                            >
                                <Loader2
                                    v-if="sectionForm.processing"
                                    class="h-4 w-4 animate-spin"
                                />
                                {{
                                    sectionForm.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan'
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Lecture Form — sama persis, tidak berubah -->
        <div
            v-if="showLectureForm"
            class="relative z-50"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"
                @click="showLectureForm = false"
            />
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-0"
                >
                    <div
                        class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white p-6 text-left shadow-xl dark:border dark:border-gray-800 dark:bg-gray-900"
                    >
                        <h3
                            class="mb-6 text-lg font-bold text-gray-900 dark:text-white"
                        >
                            {{
                                editingLecture
                                    ? 'Edit Materi (Lecture)'
                                    : 'Tambah Materi Baru'
                            }}
                        </h3>
                        <div class="space-y-5">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Judul Materi
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="lectureForm.title"
                                    type="text"
                                    placeholder="Contoh: Apa itu Terapi Wicara?"
                                    class="w-full rounded-xl"
                                />
                                <p
                                    v-if="lectureForm.errors.title"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ lectureForm.errors.title }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Tipe Konten
                                    </label>
                                    <Select v-model="lectureForm.type">
                                        <SelectTrigger
                                            class="w-full rounded-xl bg-transparent focus:ring-purple-500"
                                        >
                                            <SelectValue
                                                placeholder="Pilih tipe..."
                                            />
                                        </SelectTrigger>
                                        <SelectContent
                                            class="rounded-xl border-gray-100 shadow-lg dark:border-gray-800"
                                        >
                                            <SelectGroup>
                                                <SelectItem value="video"
                                                    >Video (YouTube)</SelectItem
                                                >
                                                <SelectItem value="text"
                                                    >Teks / Artikel</SelectItem
                                                >
                                                <SelectItem value="quiz"
                                                    >Kuis</SelectItem
                                                >
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Urutan Tampil
                                    </label>
                                    <Input
                                        v-model="lectureForm.sort_order"
                                        type="number"
                                        min="0"
                                        class="w-full rounded-xl"
                                    />
                                </div>
                            </div>
                            <div
                                v-if="lectureForm.type === 'video'"
                                class="space-y-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50"
                            >
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        URL Video (YouTube)
                                    </label>
                                    <Input
                                        v-model="lectureForm.url"
                                        type="url"
                                        placeholder="https://youtu.be/..."
                                        class="w-full rounded-xl bg-white dark:bg-gray-900"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Durasi Video (Detik)
                                    </label>
                                    <Input
                                        v-model="lectureForm.video_duration"
                                        type="number"
                                        min="0"
                                        placeholder="Contoh: 600 untuk 10 menit"
                                        class="w-full rounded-xl bg-white dark:bg-gray-900"
                                    />
                                </div>
                            </div>
                            <div
                                v-if="
                                    lectureForm.type === 'text' ||
                                    lectureForm.type === 'quiz'
                                "
                            >
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Konten Artikel / Kuis
                                </label>
                                <textarea
                                    v-model="lectureForm.content"
                                    rows="5"
                                    placeholder="Tulis konten secara detail di sini..."
                                    class="block w-full resize-y rounded-xl border-transparent bg-gray-50 px-4 py-3 text-sm focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-900"
                                />
                            </div>
                            <div
                                class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Gratis Preview
                                    </p>
                                    <p
                                        class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Calon siswa bisa melihat materi ini
                                        sebelum membeli kelas.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="
                                        lectureForm.is_free_preview =
                                            !lectureForm.is_free_preview
                                    "
                                    :class="
                                        lectureForm.is_free_preview
                                            ? 'bg-primary'
                                            : 'bg-gray-200 dark:bg-gray-700'
                                    "
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200"
                                >
                                    <span class="sr-only">Toggle Preview</span>
                                    <span
                                        :class="
                                            lectureForm.is_free_preview
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200"
                                    />
                                </button>
                            </div>
                        </div>
                        <div class="mt-8 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                @click="showLectureForm = false"
                                class="rounded-xl px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                @click="submitLecture"
                                :disabled="lectureForm.processing"
                                class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-medium text-white hover:bg-purple-700 disabled:opacity-70"
                            >
                                <Loader2
                                    v-if="lectureForm.processing"
                                    class="h-4 w-4 animate-spin"
                                />
                                {{
                                    lectureForm.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan'
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
