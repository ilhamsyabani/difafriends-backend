<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { CalendarDays, MapPin, CheckCircle2, Eraser, Loader2, RefreshCcw } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

interface Field {
    key: string;
    label: string;
    type: string;
    required: boolean;
    options?: string[];
}

const props = defineProps<{
    session: {
        token: string;
        session_date: string;
        is_open: boolean;
        title: string;
        fields: Field[];
        activity: { name: string; location: string };
    };
}>();

const page = usePage();
const isSuccess = ref(false);
const successMessage = ref('');

computed(() => page.props.flash as { success?: string }).value;

const initialData: Record<string, string> = {};
props.session.fields.forEach((f) => (initialData[f.key] = ''));

const form = useForm({ answers: initialData });

const fieldError = (key: string): string | undefined =>
    (form.errors as Record<string, string>)[`answers.${key}`];

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

// ── Signature canvas (free drawing) ────────────────────
const signatureKey = computed(() => props.session.fields.find((f) => f.type === 'signature')?.key);
const canvasRef = ref<HTMLCanvasElement | HTMLCanvasElement[] | null>(null);
let drawing = false;
let ctx: CanvasRenderingContext2D | null = null;

const canvas = computed<HTMLCanvasElement | null>(() =>
    Array.isArray(canvasRef.value) ? (canvasRef.value[0] ?? null) : canvasRef.value,
);

function initCanvas() {
    setTimeout(() => {
        const el = canvas.value;
        if (!el) return;
        const ratio = window.devicePixelRatio || 1;
        el.width = el.offsetWidth * ratio;
        el.height = el.offsetHeight * ratio;
        ctx = el.getContext('2d');
        if (!ctx) return;
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#111827'; 
    }, 100);
}

function pointerPos(e: PointerEvent) {
    const rect = canvas.value!.getBoundingClientRect();
    return { x: e.clientX - rect.left, y: e.clientY - rect.top };
}

function startDraw(e: PointerEvent) {
    if (!ctx) return;
    drawing = true;
    const { x, y } = pointerPos(e);
    ctx.beginPath();
    ctx.moveTo(x, y);
}

function draw(e: PointerEvent) {
    if (!drawing || !ctx) return;
    const { x, y } = pointerPos(e);
    ctx.lineTo(x, y);
    ctx.stroke();
}

function endDraw() {
    if (!drawing || !signatureKey.value) return;
    drawing = false;
    form.answers[signatureKey.value] = canvas.value!.toDataURL('image/png');
}

function clearSignature() {
    if (!ctx || !canvas.value) return;
    ctx.clearRect(0, 0, canvas.value.width, canvas.value.height);
    if (signatureKey.value) form.answers[signatureKey.value] = '';
}

onMounted(() => {
    if (signatureKey.value && !isSuccess.value) initCanvas();
});

function submit() {
    form.post(`/absensi/${props.session.token}`, {
        preserveScroll: true,
        onSuccess: () => {
            const flash = page.props.flash as { success?: string };
            successMessage.value = flash.success || 'Absensi berhasil dikirim!';
            isSuccess.value = true;
            form.reset();
            clearSignature();
        },
    });
}

function fillAgain() {
    isSuccess.value = false;
    if (signatureKey.value) {
        initCanvas();
    }
}
</script>

<template>
    <div class="relative min-h-screen bg-[#F8FAFC] px-4 py-12 dark:bg-gray-950 flex flex-col justify-center">
        <!-- Background Vector Pattern (Polka Dots Ringan, BUKAN Gradasi) -->
        <!-- Light Mode Pattern -->
        <div class="absolute inset-0 z-0 bg-[url('data:image/svg+xml,%3Csvg width=\'24\' height=\'24\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'2\' cy=\'2\' r=\'1.5\' fill=\'%2364748b\'/%3E%3C/svg%3E')] opacity-[0.12] dark:hidden"></div>
        <!-- Dark Mode Pattern -->
        <div class="absolute inset-0 z-0 hidden bg-[url('data:image/svg+xml,%3Csvg width=\'24\' height=\'24\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'2\' cy=\'2\' r=\'1.5\' fill=\'%23ffffff\'/%3E%3C/svg%3E')] opacity-[0.05] dark:block"></div>

        <Head :title="`Absensi — ${session.activity.name}`" />

        <div class="relative z-10 mx-auto w-full max-w-xl">
            <!-- Header Kartu Baru (Lebih Keren & Bold) -->
            <div class="relative mb-8 overflow-hidden rounded-[2rem] bg-primary px-8 py-10 text-center shadow-xl shadow-primary/20 dark:bg-gray-900 dark:border dark:border-gray-800 dark:shadow-none">
                
                <!-- Ornamen Vektor Watermark di Header -->
                <div class="absolute -right-6 -top-6 text-white opacity-10 rotate-12 pointer-events-none">
                    <CalendarDays class="h-40 w-40" />
                </div>
                <div class="absolute -bottom-10 -left-6 text-white opacity-10 -rotate-12 pointer-events-none">
                    <MapPin class="h-32 w-32" />
                </div>

                <div class="relative z-10">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 text-white backdrop-blur-md shadow-inner border border-white/20">
                        <CalendarDays class="h-8 w-8" />
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">{{ session.activity.name }}</h1>
                    <p class="mt-2 text-base font-medium text-purple-100">{{ session.title }}</p>
                    
                    <div class="mt-7 flex flex-col items-center justify-center gap-3 text-sm text-white sm:flex-row sm:gap-4">
                        <span class="flex items-center gap-2 rounded-full bg-black/20 px-4 py-2 backdrop-blur-md border border-white/10 font-medium">
                            <CalendarDays class="h-4 w-4 text-purple-200" /> {{ formatDate(session.session_date) }}
                        </span>
                        <span class="flex items-center gap-2 rounded-full bg-black/20 px-4 py-2 backdrop-blur-md border border-white/10 font-medium">
                            <MapPin class="h-4 w-4 text-purple-200" /> {{ session.activity.location }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Kondisi: Absensi Ditutup -->
            <div
                v-if="!session.is_open"
                class="rounded-[2rem] border border-amber-200 bg-white p-12 text-center shadow-lg dark:border-amber-900/50 dark:bg-gray-900"
            >
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-50 text-amber-500 dark:bg-amber-900/20">
                    <CheckCircle2 class="h-10 w-10" />
                </div>
                <h2 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">Sesi Telah Berakhir</h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">
                    Mohon maaf, pengisian absensi untuk sesi ini sudah ditutup.
                </p>
            </div>

            <!-- Kondisi: Berhasil Submit -->
            <div
                v-else-if="isSuccess"
                class="rounded-[2rem] border border-green-200 bg-white p-12 text-center shadow-xl shadow-green-100/30 dark:border-gray-800 dark:bg-gray-900 dark:shadow-none"
            >
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-50 text-green-500 dark:bg-green-900/20">
                    <CheckCircle2 class="h-10 w-10" />
                </div>
                <h2 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">Terima Kasih!</h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">{{ successMessage }}</p>
                
                <button
                    @click="fillAgain"
                    class="mt-8 inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-3.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                >
                    <RefreshCcw class="h-4 w-4" />
                    Kembali isi absensi
                </button>
            </div>

            <!-- Kondisi: Form Terbuka -->
            <form
                v-else
                @submit.prevent="submit"
                class="space-y-6 rounded-[2rem] border border-gray-100 bg-white/95 p-6 shadow-xl shadow-gray-200/40 backdrop-blur-xl sm:p-8 dark:border-gray-800 dark:bg-gray-900/95 dark:shadow-none"
            >
                <div v-for="field in session.fields" :key="field.key" class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-200">
                        {{ field.label }}
                        <span v-if="field.required" class="text-red-500 ml-0.5">*</span>
                    </label>

                    <!-- Signature -->
                    <div v-if="field.type === 'signature'">
                        <div class="overflow-hidden rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10 transition-all dark:border-gray-700 dark:bg-gray-900">
                            <canvas
                                ref="canvasRef"
                                class="h-48 w-full touch-none cursor-crosshair"
                                @pointerdown="startDraw"
                                @pointermove="draw"
                                @pointerup="endDraw"
                                @pointerleave="endDraw"
                            ></canvas>
                        </div>
                        <div class="mt-2 flex justify-end">
                            <button
                                type="button"
                                @click="clearSignature"
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white transition-colors"
                            >
                                <Eraser class="h-3.5 w-3.5" /> Bersihkan tanda tangan
                            </button>
                        </div>
                    </div>

                    <!-- Select -->
                    <select
                        v-else-if="field.type === 'select'"
                        v-model="form.answers[field.key]"
                        class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm shadow-sm transition-shadow focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    >
                        <option value="" disabled>Pilih salah satu...</option>
                        <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt }}</option>
                    </select>

                    <!-- Textarea -->
                    <textarea
                        v-else-if="field.type === 'textarea'"
                        v-model="form.answers[field.key]"
                        rows="3"
                        placeholder="Ketik jawaban Anda di sini..."
                        class="w-full rounded-xl border border-gray-200 bg-white p-4 text-sm shadow-sm transition-shadow focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    ></textarea>

                    <!-- Input Default -->
                    <input
                        v-else
                        v-model="form.answers[field.key]"
                        :type="field.type === 'email' ? 'email' : field.type === 'number' ? 'number' : field.type === 'phone' ? 'tel' : 'text'"
                        :placeholder="`Masukkan ${field.label.toLowerCase()}`"
                        class="h-12 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm shadow-sm transition-shadow focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    />

                    <!-- Error Message -->
                    <p v-if="fieldError(field.key)" class="mt-1.5 text-sm font-medium text-red-500 flex items-center gap-1.5">
                        <span class="block h-1.5 w-1.5 rounded-full bg-red-500"></span> {{ fieldError(field.key) }}
                    </p>
                </div>

                <div class="pt-6">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-8 py-4 text-base font-bold text-white shadow-lg shadow-primary/30 transition-all hover:bg-purple-700 hover:shadow-primary/40 focus:outline-none focus:ring-4 focus:ring-primary/50 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                    >
                        <Loader2 v-if="form.processing" class="h-5 w-5 animate-spin" />
                        <span v-else>Kirim Absensi Sekarang</span>
                    </button>
                </div>
            </form>

            <p class="mt-8 text-center text-sm font-medium text-gray-400 dark:text-gray-500">
                Powered by <span class="font-bold text-gray-500 dark:text-gray-400">Difafriends</span>
            </p>
        </div>
    </div>
</template>