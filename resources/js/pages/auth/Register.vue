<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
</script>

<template>
    <AuthBase
        title="Daftar Akun DifaFriends"
        description="Isi data di bawah untuk membuat akun baru"
    >
        <Head title="Daftar" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- First Name + Last Name dalam satu baris -->
                <!-- <div class="grid grid-cols-2 gap-4"> -->
                <div class="grid gap-2">
                    <Label
                        for="first_name"
                        class="text-gray-700 dark:text-gray-300"
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
                        placeholder="Nama depan"
                        class="h-11 rounded-xl bg-gray-50 transition-colors focus-visible:ring-primary/50 dark:bg-gray-900"
                    />
                    <InputError :message="errors.first_name" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="last_name"
                        class="text-gray-700 dark:text-gray-300"
                        >Nama Belakang</Label
                    >
                    <Input
                        id="last_name"
                        type="text"
                        required
                        :tabindex="2"
                        autocomplete="family-name"
                        name="last_name"
                        placeholder="Nama belakang"
                        class="h-11 rounded-xl bg-gray-50 transition-colors focus-visible:ring-primary/50 dark:bg-gray-900"
                    />
                    <InputError :message="errors.last_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-gray-300"
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
                        class="h-11 rounded-xl bg-gray-50 transition-colors focus-visible:ring-primary/50 dark:bg-gray-900"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="password"
                        class="text-gray-700 dark:text-gray-300"
                        >Password</Label
                    >
                    <PasswordInput
                        id="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password"
                        placeholder="••••••••"
                        class="h-11 rounded-xl bg-gray-50 transition-colors focus-visible:ring-primary/50 dark:bg-gray-900"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="password_confirmation"
                        class="text-gray-700 dark:text-gray-300"
                    >
                        Konfirmasi Password
                    </Label>
                    <PasswordInput
                        id="password_confirmation"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="••••••••"
                        class="h-11 rounded-xl bg-gray-50 transition-colors focus-visible:ring-primary/50 dark:bg-gray-900"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 h-11 w-full rounded-xl bg-primary text-white shadow-lg shadow-purple-600/20 transition-all hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-primary/30 focus-visible:ring-primary/50 disabled:opacity-70 disabled:hover:translate-y-0"
                    tabindex="6"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                    {{ processing ? 'Memproses...' : 'Buat Akun' }}
                </Button>
            </div>

            <div
                class="mt-2 text-center text-sm text-gray-500 dark:text-gray-400"
            >
                Sudah punya akun?
                <TextLink
                    :href="login()"
                    :tabindex="7"
                    class="hover:text-primary-hover font-semibold text-primary dark:text-primary/60"
                    >Masuk di sini</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
