<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <AuthLayout title="Reset Password">
        <Head title="Reset Password" />

        <!-- Heading -->
        <div class="mb-7">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Buat password baru
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Pastikan password baru kamu cukup kuat dan mudah diingat.
            </p>
        </div>

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <div class="grid gap-1.5">
                <Label for="email" class="text-xs font-medium text-gray-600 dark:text-gray-400">Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    v-model="inputEmail"
                    readonly
                    class="h-10 rounded-lg border-gray-200 bg-gray-100 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password" class="text-xs font-medium text-gray-600 dark:text-gray-400">Password Baru</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    autofocus
                    placeholder="Min. 8 karakter"
                    class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-1.5">
                <Label for="password_confirmation" class="text-xs font-medium text-gray-600 dark:text-gray-400">Konfirmasi Password</Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    placeholder="Ulangi password baru"
                    class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-1 h-11 w-full rounded-xl bg-primary text-sm font-semibold text-white shadow-md shadow-primary/25 transition-all hover:-translate-y-0.5 hover:bg-orange-500 disabled:opacity-70 disabled:hover:translate-y-0"
                :disabled="processing"
                data-test="reset-password-button"
            >
                <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                {{ processing ? 'Menyimpan...' : 'Simpan Password Baru' }}
            </Button>
        </Form>
    </AuthLayout>
</template>
