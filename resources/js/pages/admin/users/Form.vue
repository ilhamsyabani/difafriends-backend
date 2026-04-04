<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Save, Loader2 } from 'lucide-vue-next';
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

const props = defineProps<{
    user: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
        phone: string | null;
        role: string;
        is_active: boolean;
        city: string | null;
        gender: string | null;
    } | null;
}>();

const isEdit = !!props.user;

const form = useForm({
    first_name: props.user?.first_name ?? '',
    last_name: props.user?.last_name ?? '',
    email: props.user?.email ?? '',
    password: '',
    phone: props.user?.phone ?? '',
    role: props.user?.role ?? 'user',
    is_active: props.user?.is_active ?? true,
    city: props.user?.city ?? '',
    gender: props.user?.gender ?? 'male',
});

function submit() {
    if (isEdit) {
        form.put(`/admin/users/${props.user!.id}`);
    } else {
        form.post('/admin/users');
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna'" />

        <div class="mx-auto max-w-5xl p-6 sm:p-10">
            <!-- Header -->
            <div class="mb-10 flex items-center gap-4">
                <Link
                    href="/admin/users"
                    class="group flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-all hover:border-purple-200 hover:bg-purple-50 hover:text-purple-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-purple-400"
                >
                    <ArrowLeft
                        class="h-5 w-5 transition-transform group-hover:-translate-x-1"
                    />
                </Link>
                <div>
                    <h1
                        class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{ isEdit ? 'Edit Pengguna' : 'Buat Pengguna Baru' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Kelola informasi profil, kontak, dan hak akses pengguna.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <!-- Section 1: Profil & Kontak -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Profil Pribadi
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Informasi dasar pengguna seperti nama, alamat email
                            untuk login, dan kontak.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="space-y-6">
                            <!-- Nama Lengkap (Grid 2 Kolom) -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Nama Depan
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <Input
                                        v-model="form.first_name"
                                        type="text"
                                        placeholder="John"
                                        :class="[
                                            'w-full rounded-xl',
                                            form.errors.first_name
                                                ? 'border-red-300 focus-visible:ring-red-500'
                                                : '',
                                        ]"
                                    />
                                    <p
                                        v-if="form.errors.first_name"
                                        class="mt-1.5 text-sm text-red-500"
                                    >
                                        {{ form.errors.first_name }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Nama Belakang
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <Input
                                        v-model="form.last_name"
                                        type="text"
                                        placeholder="Doe"
                                        :class="[
                                            'w-full rounded-xl',
                                            form.errors.last_name
                                                ? 'border-red-300 focus-visible:ring-red-500'
                                                : '',
                                        ]"
                                    />
                                    <p
                                        v-if="form.errors.last_name"
                                        class="mt-1.5 text-sm text-red-500"
                                    >
                                        {{ form.errors.last_name }}
                                    </p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Alamat Email
                                    <span class="text-red-500">*</span>
                                </label>
                                <Input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="email@contoh.com"
                                    :class="[
                                        'w-full rounded-xl',
                                        form.errors.email
                                            ? 'border-red-300 focus-visible:ring-red-500'
                                            : '',
                                    ]"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label
                                    class="mb-2 flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    <span>
                                        Password
                                        <span
                                            v-if="!isEdit"
                                            class="text-red-500"
                                            >*</span
                                        >
                                    </span>
                                    <span
                                        v-if="isEdit"
                                        class="text-xs font-normal text-gray-400"
                                    >
                                        (Kosongkan jika tidak ingin mengubah)
                                    </span>
                                </label>
                                <Input
                                    v-model="form.password"
                                    type="password"
                                    placeholder="Minimal 8 karakter"
                                    :class="[
                                        'w-full rounded-xl',
                                        form.errors.password
                                            ? 'border-red-300 focus-visible:ring-red-500'
                                            : '',
                                    ]"
                                />
                                <p
                                    v-if="form.errors.password"
                                    class="mt-1.5 text-sm text-red-500"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <!-- Phone & City (Grid 2 Kolom) -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        No. HP
                                    </label>
                                    <Input
                                        v-model="form.phone"
                                        type="text"
                                        placeholder="08xxxxxxxxxx"
                                        class="w-full rounded-xl"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Kota
                                    </label>
                                    <Input
                                        v-model="form.city"
                                        type="text"
                                        placeholder="Contoh: Jakarta"
                                        class="w-full rounded-xl"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pemisah -->
                <div class="h-px w-full bg-gray-100 dark:bg-gray-800"></div>

                <!-- Section 2: Pengaturan Akun -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <h2
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            Pengaturan Sistem
                        </h2>
                        <p
                            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                        >
                            Tentukan hak akses pengguna dalam aplikasi dan
                            status keaktifan akun.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] md:col-span-2 dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="space-y-6">
                            <!-- Role & Gender (Grid 2 Kolom) -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Role -->
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Peran (Role)
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <!-- Menggunakan standar select yang di-style ala modern input -->
                                    <Select v-model="form.role">
                                        <SelectTrigger
                                            :class="[
                                                'w-full rounded-xl bg-transparent',
                                                form.errors.role
                                                    ? 'border-red-300 focus:ring-red-500'
                                                    : 'focus:ring-purple-500',
                                            ]"
                                        >
                                            <SelectValue
                                                placeholder="Pilih peran..."
                                            />
                                        </SelectTrigger>
                                        <SelectContent
                                            class="rounded-xl border-gray-100 dark:border-gray-800"
                                        >
                                            <SelectGroup>
                                                <SelectItem value="user"
                                                    >Orang Tua /
                                                    User</SelectItem
                                                >
                                                <SelectItem value="instructor"
                                                    >Tentor</SelectItem
                                                >
                                                <SelectItem value="companion"
                                                    >Guru Pendamping</SelectItem
                                                >
                                                <SelectItem value="admin"
                                                    >Administrator</SelectItem
                                                >
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <p
                                        v-if="form.errors.role"
                                        class="mt-1.5 text-sm text-red-500"
                                    >
                                        {{ form.errors.role }}
                                    </p>
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label
                                        class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        Jenis Kelamin
                                    </label>
                                    <Select v-model="form.gender">
                                        <SelectTrigger
                                            class="w-full rounded-xl bg-transparent focus:ring-purple-500"
                                        >
                                            <SelectValue
                                                placeholder="Pilih jenis kelamin..."
                                            />
                                        </SelectTrigger>
                                        <SelectContent
                                            class="rounded-xl border-gray-100 dark:border-gray-800"
                                        >
                                            <SelectGroup>
                                                <SelectItem value="male"
                                                    >Laki-laki</SelectItem
                                                >
                                                <SelectItem value="female"
                                                    >Perempuan</SelectItem
                                                >
                                                <SelectItem value="other"
                                                    >Lainnya</SelectItem
                                                >
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>

                            <!-- Modern Toggle Switch (Is Active) -->
                            <div
                                class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800"
                            >
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        Status Akun
                                    </p>
                                    <p
                                        class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        Jika dimatikan, pengguna tidak akan bisa
                                        login ke dalam sistem.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    @click="form.is_active = !form.is_active"
                                    :class="
                                        form.is_active
                                            ? 'bg-purple-600'
                                            : 'bg-gray-200 dark:bg-gray-700'
                                    "
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:ring-2 focus:ring-purple-600 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-gray-900"
                                >
                                    <span class="sr-only"
                                        >Toggle status aktif</span
                                    >
                                    <span
                                        aria-hidden="true"
                                        :class="
                                            form.is_active
                                                ? 'translate-x-5'
                                                : 'translate-x-0'
                                        "
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <Link
                        href="/admin/users"
                        class="rounded-xl px-5 py-2.5 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-purple-700 hover:shadow focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-70 dark:focus:ring-offset-gray-900"
                    >
                        <Loader2
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin"
                        />
                        <Save v-else class="h-4 w-4" />
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : isEdit
                                  ? 'Simpan Perubahan'
                                  : 'Buat Pengguna'
                        }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
