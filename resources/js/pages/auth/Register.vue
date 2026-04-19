<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
</script>

<template>
    <AuthBase title="Daftar Akun Difafriends">
        <Head title="Daftar" />

        <!-- Heading -->
        <div class="mb-7">
            <span
                class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary"
            >
                <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                Gratis selamanya
            </span>
            <h1
                class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
            >
                Buat akun baru
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Bergabung dengan ribuan orang tua & pendidik di Difafriends.
            </p>
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <!-- Nama depan + belakang -->
            <div class="grid grid-cols-2 gap-3">
                <div class="grid gap-1.5">
                    <Label
                        for="first_name"
                        class="text-xs font-medium text-gray-600 dark:text-gray-400"
                        >Nama Depan</Label
                    >
                    <Input
                        id="first_name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="given-name"
                        name="first_name"
                        placeholder="Budi"
                        class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm transition-colors focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                    />
                    <InputError :message="errors.first_name" />
                </div>

                <div class="grid gap-1.5">
                    <Label
                        for="last_name"
                        class="text-xs font-medium text-gray-600 dark:text-gray-400"
                        >Nama Belakang</Label
                    >
                    <Input
                        id="last_name"
                        type="text"
                        required
                        :tabindex="2"
                        autocomplete="family-name"
                        name="last_name"
                        placeholder="Santoso"
                        class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm transition-colors focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                    />
                    <InputError :message="errors.last_name" />
                </div>
            </div>

            <!-- Email -->
            <div class="grid gap-1.5">
                <Label
                    for="email"
                    class="text-xs font-medium text-gray-600 dark:text-gray-400"
                    >Alamat Email</Label
                >
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="3"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                    class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm transition-colors focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Password -->
            <div class="grid gap-1.5">
                <Label
                    for="password"
                    class="text-xs font-medium text-gray-600 dark:text-gray-400"
                    >Password</Label
                >
                <PasswordInput
                    id="password"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Min. 8 karakter"
                    class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm transition-colors focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Konfirmasi Password -->
            <div class="grid gap-1.5">
                <Label
                    for="password_confirmation"
                    class="text-xs font-medium text-gray-600 dark:text-gray-400"
                    >Konfirmasi Password</Label
                >
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="5"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Ulangi password"
                    class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm transition-colors focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <!-- Submit -->
            <Button
                type="submit"
                class="mt-1 h-11 w-full rounded-xl bg-primary text-sm font-semibold text-white shadow-md shadow-primary/25 transition-all hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-primary/40 disabled:opacity-70 disabled:hover:translate-y-0"
                tabindex="6"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                {{ processing ? 'Memproses...' : 'Buat Akun Gratis' }}
            </Button>

            <!-- Login link -->
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                Sudah punya akun?
                <a
                    href="/login"
                    :tabindex="7"
                    class="font-semibold text-gray-900 underline-offset-2 hover:underline dark:text-white"
                >
                    Masuk di sini
                </a>
            </p>
        </Form>
    </AuthBase>
</template>
