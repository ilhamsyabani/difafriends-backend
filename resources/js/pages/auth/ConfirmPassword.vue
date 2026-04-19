<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
</script>

<template>
    <AuthLayout title="Konfirmasi Password">
        <Head title="Konfirmasi Password" />

        <!-- Heading -->
        <div class="mb-7">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Area aman
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Konfirmasi password kamu untuk melanjutkan ke halaman ini.
            </p>
        </div>

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <div class="grid gap-1.5">
                <Label for="password" class="text-xs font-medium text-gray-600 dark:text-gray-400">Password</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="Masukkan password kamu"
                    class="h-10 rounded-lg border-gray-200 bg-gray-50 text-sm focus-visible:border-gray-900 focus-visible:ring-2 focus-visible:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-900 dark:focus-visible:border-white"
                />
                <InputError :message="errors.password" />
            </div>

            <Button
                type="submit"
                class="h-11 w-full rounded-xl bg-primary text-sm font-semibold text-white shadow-md shadow-primary/25 transition-all hover:-translate-y-0.5 hover:bg-orange-500 disabled:opacity-70 disabled:hover:translate-y-0"
                :disabled="processing"
                data-test="confirm-password-button"
            >
                <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                {{ processing ? 'Memverifikasi...' : 'Konfirmasi & Lanjutkan' }}
            </Button>
        </Form>
    </AuthLayout>
</template>
