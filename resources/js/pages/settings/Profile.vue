<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { Camera } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import type { BreadcrumbItem } from '@/types';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Pengaturan Profil', href: edit() },
];

const page = usePage();
const user = computed(() => page.props.auth.user as any);

// Preview foto profil
const photoPreview = ref<string | null>(null);
const photoInput = ref<HTMLInputElement | null>(null);

function onPhotoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];

    if (!file) {
return;
}

    const reader = new FileReader();
    reader.onload = (ev) => {
        photoPreview.value = ev.target?.result as string;
    };
    reader.readAsDataURL(file);
}

const avatarUrl = computed(() => {
    if (photoPreview.value) {
return photoPreview.value;
}

    if (user.value?.photo) {
return `/storage/${user.value.photo}`;
}

    const name = encodeURIComponent(
        `${user.value?.first_name ?? ''} ${user.value?.last_name ?? ''}`,
    );

    return `https://ui-avatars.com/api/?name=${name}&background=7B2D8B&color=fff&size=128`;
});

const roleLabel: Record<string, string> = {
    admin: 'Administrator',
    instructor: 'Tentor / Instruktur',
    companion: 'Guru Pendamping',
    user: 'Orang Tua / User',
};

const genderOptions = [
    { value: '', label: 'Pilih jenis kelamin' },
    { value: 'male', label: 'Laki-laki' },
    { value: 'female', label: 'Perempuan' },
    { value: 'other', label: 'Lainnya' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Pengaturan Profil" />

        <SettingsLayout>
            <div class="flex flex-col space-y-10">
                <!-- ── Avatar Section ─────────────────────��──── -->
                <div>
                    <Heading
                        variant="small"
                        title="Foto Profil"
                        description="Upload foto profil Anda. Maks. 2 MB, format JPG/PNG/WEBP."
                    />

                    <div class="mt-6 flex items-center gap-6">
                        <div class="relative shrink-0">
                            <img
                                :src="avatarUrl"
                                :alt="user?.first_name"
                                class="h-24 w-24 rounded-2xl object-cover ring-4 ring-purple-100 dark:ring-purple-900/30"
                            />
                            <button
                                type="button"
                                @click="photoInput?.click()"
                                class="absolute -right-2 -bottom-2 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white shadow-md transition hover:bg-purple-700"
                                title="Ganti foto"
                            >
                                <Camera class="h-4 w-4" />
                            </button>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <p
                                class="font-semibold text-gray-900 dark:text-white"
                            >
                                {{ user?.first_name }} {{ user?.last_name }}
                            </p>
                            <span
                                class="w-fit rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-primary-hover dark:bg-purple-900/30 dark:text-purple-400"
                            >
                                {{ roleLabel[user?.role] ?? user?.role }}
                            </span>
                            <p class="text-xs text-gray-400">
                                Klik ikon kamera untuk mengganti foto
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ── Form Informasi Profil ──────────────────── -->
                <div>
                    <Heading
                        variant="small"
                        title="Informasi Pribadi"
                        description="Perbarui data diri, kontak, dan bio Anda."
                    />

                    <Form
                        v-bind="ProfileController.update.form()"
                        enctype="multipart/form-data"
                        class="mt-6 space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <!-- Input file tersembunyi untuk foto -->
                        <input
                            ref="photoInput"
                            type="file"
                            name="photo"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden"
                            @change="onPhotoChange"
                        />

                        <!-- Nama -->
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="first_name">Nama Depan</Label>
                                <Input
                                    id="first_name"
                                    name="first_name"
                                    :default-value="user?.first_name"
                                    required
                                    autocomplete="given-name"
                                    placeholder="cth. Budi"
                                />
                                <InputError :message="errors.first_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="last_name">Nama Belakang</Label>
                                <Input
                                    id="last_name"
                                    name="last_name"
                                    :default-value="user?.last_name"
                                    required
                                    autocomplete="family-name"
                                    placeholder="cth. Santoso"
                                />
                                <InputError :message="errors.last_name" />
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="grid gap-2">
                            <Label for="email">Alamat Email</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                :default-value="user?.email"
                                required
                                autocomplete="email"
                                placeholder="email@contoh.com"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <!-- Kontak & Lokasi -->
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="phone">Nomor Telepon</Label>
                                <Input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    :default-value="user?.phone"
                                    placeholder="+62..."
                                    autocomplete="tel"
                                />
                                <InputError :message="errors.phone" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="city">Kota</Label>
                                <Input
                                    id="city"
                                    name="city"
                                    :default-value="user?.city"
                                    placeholder="cth. Jakarta"
                                />
                                <InputError :message="errors.city" />
                            </div>
                        </div>

                        <!-- Tanggal Lahir & Jenis Kelamin -->
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="birth_date">Tanggal Lahir</Label>
                                <Input
                                    id="birth_date"
                                    name="birth_date"
                                    type="date"
                                    :default-value="user?.birth_date"
                                />
                                <InputError :message="errors.birth_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="gender">Jenis Kelamin</Label>
                                <select
                                    id="gender"
                                    name="gender"
                                    :value="user?.gender ?? ''"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none"
                                >
                                    <option
                                        v-for="opt in genderOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <InputError :message="errors.gender" />
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="grid gap-2">
                            <Label for="bio">Bio</Label>
                            <textarea
                                id="bio"
                                name="bio"
                                rows="4"
                                :defaultValue="user?.bio ?? ''"
                                placeholder="Ceritakan sedikit tentang Anda... (maks. 500 karakter)"
                                class="flex w-full resize-none rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none"
                            />
                            <p class="text-xs text-muted-foreground">
                                <span
                                    v-if="
                                        user?.role === 'companion' ||
                                        user?.role === 'instructor'
                                    "
                                >
                                    Bio Anda akan tampil di halaman profil
                                    publik.
                                </span>
                                <span v-else
                                    >Deskripsi singkat tentang Anda.</span
                                >
                            </p>
                            <InputError :message="errors.bio" />
                        </div>

                        <!-- Email Verifikasi -->
                        <div
                            v-if="mustVerifyEmail && !user?.email_verified_at"
                            class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900 dark:bg-yellow-900/20"
                        >
                            <p
                                class="text-sm text-yellow-800 dark:text-yellow-200"
                            >
                                Email Anda belum diverifikasi.
                                <Link
                                    :href="send()"
                                    as="button"
                                    class="font-semibold underline underline-offset-4 hover:text-yellow-700"
                                >
                                    Klik di sini untuk kirim ulang link
                                    verifikasi.
                                </Link>
                            </p>
                            <div
                                v-if="status === 'verification-link-sent'"
                                class="mt-2 text-sm font-medium text-green-600"
                            >
                                Link verifikasi baru telah dikirim ke email
                                Anda.
                            </div>
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="flex items-center gap-4 border-t pt-6">
                            <Button
                                type="submit"
                                :disabled="processing"
                                class="px-8"
                            >
                                {{
                                    processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Perubahan'
                                }}
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="text-sm font-medium text-green-600"
                                >
                                    ✓ Perubahan berhasil disimpan.
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </div>

                <div
                    class="border-t border-neutral-200 dark:border-neutral-800"
                />

                <DeleteUser />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
