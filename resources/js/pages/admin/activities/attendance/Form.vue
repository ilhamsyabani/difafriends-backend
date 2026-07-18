<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Loader2, Plus, Trash2, GripVertical } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';

interface Field {
    key: string;
    label: string;
    type: string;
    required: boolean;
    options?: string[];
}

const props = defineProps<{
    activity: { id: number; name: string };
    attendanceForm: { id: number; title: string; fields: Field[] } | null;
    presetFields: Field[];
    fieldTypes: Array<{ value: string; label: string }>;
}>();

const isEdit = !!props.attendanceForm;

const form = useForm<{ title: string; fields: Field[] }>({
    title: props.attendanceForm?.title ?? 'Absensi Peserta',
    fields: props.attendanceForm?.fields
        ? JSON.parse(JSON.stringify(props.attendanceForm.fields))
        : JSON.parse(JSON.stringify(props.presetFields)),
});

function isAdded(key: string): boolean {
    return form.fields.some((f) => f.key === key);
}

function addPreset(preset: Field) {
    if (isAdded(preset.key)) return;
    form.fields.push(JSON.parse(JSON.stringify(preset)));
}

function addCustom() {
    form.fields.push({
        key: `custom_${Date.now()}`,
        label: '',
        type: 'text',
        required: false,
    });
}

function removeField(index: number) {
    form.fields.splice(index, 1);
}

function optionsText(field: Field): string {
    return (field.options ?? []).join(', ');
}

function updateOptions(field: Field, value: string) {
    field.options = value
        .split(',')
        .map((o) => o.trim())
        .filter(Boolean);
}

function submit() {
    if (isEdit) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/admin/attendance-forms/${props.attendanceForm!.id}`,
        );
    } else {
        form.post(`/admin/activities/${props.activity.id}/attendance-forms`);
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Absensi' : 'Buat Absensi'" />

        <div class="mx-auto max-w-4xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-10 flex items-center gap-4">
                <Link
                    :href="`/admin/activities/${activity.id}`"
                    class="group flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-colors hover:border-purple-200 hover:bg-purple-50 hover:text-primary dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                >
                    <ArrowLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ isEdit ? 'Edit Absensi' : 'Buat Absensi' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ activity.name }} — pilih field yang ingin ditampilkan ke peserta.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Judul Form -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Absensi</label>
                    <Input v-model="form.title" type="text" placeholder="mis. Absensi Peserta" class="max-w-xl" />
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                </div>

                <!-- Field Bawaan -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Field Bawaan</h2>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Klik untuk menambahkan ke form.</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button
                            v-for="preset in presetFields"
                            :key="preset.key"
                            type="button"
                            :disabled="isAdded(preset.key)"
                            @click="addPreset(preset)"
                            class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium transition-colors"
                            :class="isAdded(preset.key)
                                ? 'cursor-not-allowed border-gray-100 bg-gray-50 text-gray-300 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-600'
                                : 'border-purple-200 bg-purple-50 text-primary hover:bg-purple-100 dark:border-purple-900/40 dark:bg-purple-900/20'"
                        >
                            <Plus class="h-3 w-3" /> {{ preset.label }}
                        </button>
                    </div>
                </div>

                <!-- Area Field Custom -->
                <div class="rounded-2xl border border-gray-200 bg-gray-50/50 p-6 dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                            Field Ditampilkan ({{ form.fields.length }})
                        </h2>
                        <button
                            type="button"
                            @click="addCustom"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            <Plus class="h-3.5 w-3.5" /> Field Custom
                        </button>
                    </div>

                    <p v-if="form.errors.fields" class="mb-3 text-sm text-red-500">{{ form.errors.fields }}</p>

                    <!-- Empty State -->
                    <div v-if="form.fields.length === 0" class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center dark:border-gray-700 dark:bg-gray-800">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                            <Plus class="h-6 w-6 text-gray-400" />
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-900 dark:text-gray-200">Belum ada field</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan dari field bawaan atau buat field custom.</p>
                    </div>

                    <!-- List Form Fields -->
                    <div v-else class="space-y-4">
                        <div
                            v-for="(field, index) in form.fields"
                            :key="index"
                            class="group rounded-xl border border-gray-200 bg-white shadow-sm transition-all focus-within:border-purple-300 focus-within:ring-1 focus-within:ring-purple-200 dark:border-gray-700 dark:bg-gray-800 dark:focus-within:border-purple-500/50"
                        >
                            <div class="flex items-start gap-4 p-5">
                                <!-- Drag Handle -->
                                <div class="mt-1 flex shrink-0 cursor-move items-center justify-center text-gray-300 transition-colors group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-400">
                                    <GripVertical class="h-5 w-5" />
                                </div>
                                
                                <div class="flex-1 space-y-5">
                                    <!-- Inputs Grid -->
                                    <div class="grid gap-5 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Label Pertanyaan</label>
                                            <Input v-model="field.label" type="text" placeholder="Masukkan nama field" class="h-10" />
                                        </div>
                                        <div>
                                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipe Jawaban</label>
                                            <select
                                                v-model="field.type"
                                                class="h-10 w-full rounded-md border border-gray-200 bg-white px-3 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                                            >
                                                <option v-for="t in fieldTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Conditional Input: Select Options -->
                                    <div v-if="field.type === 'select'" class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800">
                                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Pilihan Jawaban (pisahkan dengan koma)
                                        </label>
                                        <Input
                                            :model-value="optionsText(field)"
                                            @update:model-value="(v: string | number) => updateOptions(field, String(v))"
                                            type="text"
                                            placeholder="contoh: Laki-laki, Perempuan"
                                            class="h-10 bg-white dark:bg-gray-800"
                                        />
                                    </div>

                                    <!-- Footer Actions (Checkbox & Hapus) -->
                                    <div class="flex items-center justify-between border-t border-gray-100 pt-4 dark:border-gray-700/60">
                                        <label class="flex cursor-pointer items-center gap-2.5">
                                            <input 
                                                v-model="field.required" 
                                                type="checkbox" 
                                                class="h-4 w-4 cursor-pointer rounded border-gray-300 text-primary transition focus:ring-primary dark:border-gray-600 dark:bg-gray-800" 
                                            /> 
                                            <span class="text-sm font-medium text-gray-700 select-none dark:text-gray-300">Wajib diisi</span>
                                        </label>

                                        <button
                                            type="button"
                                            @click="removeField(index)"
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                            <span>Hapus Field</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end border-t border-gray-100 pt-6 dark:border-gray-800">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-3 text-sm font-medium text-white shadow-sm transition-colors hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <Save v-else class="h-4 w-4" />
                        {{ isEdit ? 'Simpan Perubahan' : 'Buat Absensi & Generate Link' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>