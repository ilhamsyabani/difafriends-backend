<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout title="Lupa Password">
        <Head title="Lupa Password" />

        <!-- Heading -->
        <div class="mb-7">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Lupa password?
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Masukkan email kamu dan kami kirimkan tautan reset password.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400"
        >
            {{ status }}
        </div>

        <Form v-bind="email.form()" v-slot="{ errors, processing }" class="flex flex-col gap-5">
            <div class="grid gap-1.5">
                <Label for="email" class="text-xs font-medium text-gray-600 dark:text-gray-400">Alamat Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    autofocus
                    placeholder="email@example.com"
                    class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                />
                <InputError :message="errors.email" />
            </div>

            <Button
                type="submit"
                class="h-11 w-full rounded-xl bg-primary text-sm font-semibold text-white shadow-md shadow-primary/25 transition-all hover:-translate-y-0.5 hover:bg-orange-500 disabled:opacity-70 disabled:hover:translate-y-0"
                :disabled="processing"
                data-test="email-password-reset-link-button"
            >
                <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                {{ processing ? 'Mengirim...' : 'Kirim Tautan Reset' }}
            </Button>

            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                Ingat password?
                <a :href="login()" class="font-semibold text-gray-900 underline-offset-2 hover:underline dark:text-white">
                    Masuk di sini
                </a>
            </p>
        </Form>
    </AuthLayout>
</template>
