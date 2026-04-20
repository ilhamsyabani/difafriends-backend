<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit, update, destroy } from '@/routes/settings/logo';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    currentLogo: string | null;
    appName: string;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Logo & Branding',
        href: edit(),
    },
];

const page = usePage();
const flash = computed(
    () => page.props.flash as { success?: string; error?: string },
);

const previewUrl = ref<string | null>(props.currentLogo);

const form = useForm({
    logo: null as File | null,
    app_name: props.appName,
    _method: 'POST',
});

const destroyForm = useForm({
    _method: 'DELETE',
});

function onFileChange(e: Event) {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;
    form.logo = file;

    if (file) {
        previewUrl.value = URL.createObjectURL(file);
    }
}

function submit() {
    form.post(update(), {
        forceFormData: true,
    });
}

function removeLogo() {
    destroyForm.delete(destroy(), {
        onSuccess: () => {
            previewUrl.value = null;
        },
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Logo & Branding" />

        <h1 class="sr-only">Logo & Branding</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="Logo & Branding"
                    description="Ganti logo dan nama aplikasi yang ditampilkan di seluruh halaman"
                />

                <div
                    v-if="flash.success"
                    class="rounded-md bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/20 dark:text-green-400"
                >
                    {{ flash.success }}
                </div>

                <form class="space-y-6" @submit.prevent="submit">
                    <!-- App Name -->
                    <div class="space-y-2">
                        <Label for="app_name">Nama Aplikasi</Label>
                        <Input
                            id="app_name"
                            v-model="form.app_name"
                            type="text"
                            placeholder="DifaFriends"
                            :disabled="form.processing"
                        />
                        <p
                            v-if="form.errors.app_name"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.app_name }}
                        </p>
                    </div>

                    <!-- Logo Upload -->
                    <div class="space-y-3">
                        <Label for="logo">Logo</Label>

                        <!-- Preview -->
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-xl border border-border bg-muted"
                            >
                                <img
                                    v-if="previewUrl"
                                    :src="previewUrl"
                                    alt="Preview logo"
                                    class="h-full w-full object-contain p-2"
                                />
                                <span
                                    v-else
                                    class="text-xl font-bold text-muted-foreground"
                                    >DF</span
                                >
                            </div>

                            <div
                                class="space-y-1 text-sm text-muted-foreground"
                            >
                                <p>Format: PNG, JPG, SVG, WebP</p>
                                <p>Ukuran maks: 2MB</p>
                                <p>Rekomendasi: gambar persegi (1:1)</p>
                            </div>
                        </div>

                        <Input
                            id="logo"
                            type="file"
                            accept="image/png,image/jpeg,image/svg+xml,image/webp"
                            :disabled="form.processing"
                            @change="onFileChange"
                        />
                        <p v-if="form.errors.logo" class="text-sm text-red-500">
                            {{ form.errors.logo }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <Button type="submit" :disabled="form.processing">
                            Simpan Perubahan
                        </Button>

                        <Button
                            v-if="currentLogo"
                            type="button"
                            variant="destructive"
                            :disabled="destroyForm.processing"
                            @click="removeLogo"
                        >
                            Hapus Logo
                        </Button>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
