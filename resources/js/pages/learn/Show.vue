<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    course: {
        id: number;
        title: string;
        slug: string;
        instructor: { first_name: string; last_name: string };
        sections: Array<{
            id: number;
            title: string;
            lectures: Array<{
                id: number;
                title: string;
                type: string;
                url: string | null;
                content: string | null;
                video_duration: number;
                is_free_preview: boolean;
            }>;
            quiz: {                    // ✅ tambahkan ini
                id: number;
                title: string;
            } | null;
        }>;
    };
    enrollment: {
        id: number;
        status: string;
    };
    progress: Array<{
        lecture_id: number;
        is_completed: boolean;
        watch_seconds: number;
    }>;
    activeLectureId: number | null;
    progressPercent: number;
    totalLectures: number;
    completedLectures: number;
}>();

// State
const currentLectureId = ref(props.activeLectureId);
const completedIds = ref<number[]>(
    props.progress.filter((p) => p.is_completed).map((p) => p.lecture_id),
);
const progressPercent = ref(props.progressPercent);
const sidebarOpen = ref(true);
const expandedSections = ref<number[]>(props.course.sections.map((s) => s.id));

// Current lecture object
const currentLecture = computed(() => {
    for (const section of props.course.sections) {
        const found = section.lectures.find((l) => l.id === currentLectureId.value);
        if (found) return found;
    }
    return null;
});

// Flat list semua lectures untuk navigasi prev/next
const allLectures = computed(() =>
    props.course.sections.flatMap((s) => s.lectures),
);

const currentIndex = computed(() =>
    allLectures.value.findIndex((l) => l.id === currentLectureId.value),
);

const prevLecture = computed(() =>
    currentIndex.value > 0 ? allLectures.value[currentIndex.value - 1] : null,
);

const nextLecture = computed(() =>
    currentIndex.value < allLectures.value.length - 1
        ? allLectures.value[currentIndex.value + 1]
        : null,
);

function selectLecture(lectureId: number) {
    currentLectureId.value = lectureId;
}

function toggleSection(sectionId: number) {
    if (expandedSections.value.includes(sectionId)) {
        expandedSections.value = expandedSections.value.filter((s) => s !== sectionId);
    } else {
        expandedSections.value.push(sectionId);
    }
}

function isCompleted(lectureId: number): boolean {
    return completedIds.value.includes(lectureId);
}

function formatDuration(seconds: number): string {
    const m = Math.floor(seconds / 60);
    const h = Math.floor(m / 60);
    const rem = m % 60;
    return h > 0 ? `${h}j ${rem}m` : `${m}m`;
}

function getYoutubeEmbedUrl(url: string): string {
    const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/);
    return match ? `https://www.youtube.com/embed/${match[1]}?autoplay=0` : url;
}

async function markCompleted(lectureId: number) {
    if (isCompleted(lectureId)) return;

    try {
        const res = await axios.post(`/learn/${props.course.slug}/progress`, {
            lecture_id:    lectureId,
            watch_seconds: currentLecture.value?.video_duration ?? 0,
            is_completed:  true,
        });

        completedIds.value.push(lectureId);
        progressPercent.value = res.data.progress_percent;

        if (nextLecture.value) {
            setTimeout(() => {
                currentLectureId.value = nextLecture.value!.id;
            }, 1000);
        }
    } catch (e) {
        console.error('Failed to update progress', e);
    }
}
</script>

<template>
    <Head :title="`Belajar: ${course.title} — DifaFriends`" />

    <div class="flex h-screen flex-col overflow-hidden bg-gray-950 text-white">

        <!-- TOP BAR -->
        <header class="flex shrink-0 items-center justify-between border-b border-gray-800 bg-gray-900 px-4 py-3">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="rounded-lg p-1.5 transition-colors hover:bg-gray-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex items-center gap-2">
                    <a href="/" class="flex items-center gap-1.5">
                        <div class="flex h-6 w-6 items-center justify-center rounded bg-purple-600">
                            <span class="text-xs font-bold text-white">DF</span>
                        </div>
                    </a>
                    <span class="hidden text-sm text-gray-400 md:block">/</span>
                    <span class="hidden max-w-xs truncate text-sm font-medium md:block">
                        {{ course.title }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden items-center gap-2 text-sm md:flex">
                    <span class="text-gray-400">
                        {{ completedLectures }}/{{ totalLectures }} selesai
                    </span>
                    <div class="h-2 w-32 overflow-hidden rounded-full bg-gray-700">
                        <div
                            class="h-2 rounded-full bg-purple-500 transition-all duration-500"
                            :style="{ width: progressPercent + '%' }"
                        />
                    </div>
                    <span class="font-medium text-purple-400">{{ progressPercent }}%</span>
                </div>
                <a :href="`/courses/${course.slug}`"
                    class="text-sm text-gray-400 transition-colors hover:text-white">
                    ← Kembali
                </a>
            </div>
        </header>

        <!-- MAIN -->
        <div class="flex flex-1 overflow-hidden">

            <!-- SIDEBAR -->
            <aside :class="[
                'shrink-0 overflow-y-auto border-r border-gray-800 bg-gray-900 transition-all duration-300',
                sidebarOpen ? 'w-72' : 'w-0 overflow-hidden',
            ]">
                <div class="border-b border-gray-800 p-4">
                    <p class="mb-1 text-xs tracking-wider text-gray-400 uppercase">
                        Konten Kelas
                    </p>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-700">
                        <div
                            class="h-1.5 rounded-full bg-purple-500 transition-all duration-500"
                            :style="{ width: progressPercent + '%' }"
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-400">{{ progressPercent }}% selesai</p>
                </div>

                <!-- Sections & Lectures -->
                <div>
                    <div v-for="section in course.sections" :key="section.id">

                        <!-- Section header -->
                        <button
                            @click="toggleSection(section.id)"
                            class="flex w-full items-center justify-between px-4 py-3 text-left transition-colors hover:bg-gray-800"
                        >
                            <span class="text-sm leading-tight font-medium text-gray-200">
                                {{ section.title }}
                            </span>
                            <svg
                                class="ml-2 h-4 w-4 shrink-0 text-gray-500 transition-transform"
                                :class="{ 'rotate-180': expandedSections.includes(section.id) }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Lectures + Quiz -->
                        <div v-show="expandedSections.includes(section.id)">

                            <!-- Lecture items -->
                            <button
                                v-for="lecture in section.lectures"
                                :key="lecture.id"
                                @click="selectLecture(lecture.id)"
                                :class="[
                                    'flex w-full items-start gap-3 border-l-2 px-4 py-3 text-left transition-colors',
                                    currentLectureId === lecture.id
                                        ? 'border-purple-500 bg-purple-900/40'
                                        : 'border-transparent hover:bg-gray-800',
                                ]"
                            >
                                <div class="mt-0.5 shrink-0">
                                    <div v-if="isCompleted(lecture.id)"
                                        class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-600">
                                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div v-else class="h-5 w-5 rounded-full border-2 border-gray-600"/>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p :class="[
                                        'text-sm leading-tight',
                                        currentLectureId === lecture.id ? 'text-white' : 'text-gray-300',
                                    ]">{{ lecture.title }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        {{ formatDuration(lecture.video_duration) }}
                                    </p>
                                </div>
                            </button>

                            <!-- ✅ Quiz item — tampil setelah lectures jika section punya quiz -->
                            <Link
                                v-if="section.quiz"
                                :href="`/learn/${course.slug}/quiz/${section.quiz.id}`"
                                class="flex w-full items-center gap-3 border-l-2 border-transparent px-4 py-3 text-left transition-colors hover:bg-gray-800"
                            >
                                <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-amber-500/20">
                                    <svg class="h-3 w-3 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-amber-400">Kuis</p>
                                    <p class="text-sm text-gray-300">{{ section.quiz.title }}</p>
                                </div>
                            </Link>

                        </div>
                    </div>
                </div>
            </aside>

            <!-- KONTEN UTAMA -->
            <main class="flex-1 overflow-y-auto">
                <div v-if="!currentLecture" class="flex h-full items-center justify-center">
                    <div class="text-center text-gray-500">
                        <svg class="mx-auto mb-4 h-16 w-16 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                        <p>Pilih lecture dari sidebar</p>
                    </div>
                </div>

                <div v-else class="mx-auto max-w-4xl px-4 py-6">

                    <!-- Video Player -->
                    <div v-if="currentLecture.url"
                        class="mb-6 aspect-video overflow-hidden rounded-xl bg-black">
                        <iframe
                            :src="getYoutubeEmbedUrl(currentLecture.url)"
                            class="h-full w-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        />
                    </div>

                    <!-- Konten teks -->
                    <div v-if="currentLecture.content && currentLecture.type === 'text'"
                        class="prose prose-invert mb-6 max-w-none rounded-xl bg-gray-900 p-6">
                        <p class="leading-relaxed whitespace-pre-line text-gray-300">
                            {{ currentLecture.content }}
                        </p>
                    </div>

                    <!-- Judul & Actions -->
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            <h1 class="mb-1 text-xl font-bold">{{ currentLecture.title }}</h1>
                            <p class="text-sm text-gray-400">
                                {{ course.instructor.first_name }} {{ course.instructor.last_name }}
                                · {{ formatDuration(currentLecture.video_duration) }}
                            </p>
                        </div>

                        <button
                            @click="markCompleted(currentLecture.id)"
                            :class="[
                                'flex shrink-0 items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition-colors',
                                isCompleted(currentLecture.id)
                                    ? 'cursor-default bg-green-900/40 text-green-400'
                                    : 'bg-purple-600 text-white hover:bg-purple-700',
                            ]"
                            :disabled="isCompleted(currentLecture.id)"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ isCompleted(currentLecture.id) ? 'Sudah Selesai' : 'Tandai Selesai' }}
                        </button>
                    </div>

                    <!-- Navigasi prev/next -->
                    <div class="flex items-center justify-between border-t border-gray-800 pt-4">
                        <button
                            v-if="prevLecture"
                            @click="selectLecture(prevLecture.id)"
                            class="flex items-center gap-2 text-sm text-gray-400 transition-colors hover:text-white"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <div class="text-left">
                                <p class="text-xs text-gray-500">Sebelumnya</p>
                                <p class="max-w-xs truncate">{{ prevLecture.title }}</p>
                            </div>
                        </button>
                        <div v-else />

                        <button
                            v-if="nextLecture"
                            @click="selectLecture(nextLecture.id)"
                            class="flex items-center gap-2 text-right text-sm text-gray-400 transition-colors hover:text-white"
                        >
                            <div>
                                <p class="text-xs text-gray-500">Berikutnya</p>
                                <p class="max-w-xs truncate">{{ nextLecture.title }}</p>
                            </div>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <div v-else-if="progressPercent === 100" class="text-center">
                            <p class="font-medium text-green-400">🎉 Kelas selesai!</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
