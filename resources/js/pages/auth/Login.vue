<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        title="Selamat Datang Kembali"
        description="Masukkan email dan password untuk masuk ke akun Anda"
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-6 rounded-lg border border-green-100 bg-green-50 px-4 py-3 text-center text-sm font-medium text-green-700"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-gray-300"
                        >Email address</Label
                    >
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                        class="h-11 rounded-xl bg-gray-50 transition-colors focus-visible:ring-purple-500 dark:bg-gray-900"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label
                            for="password"
                            class="text-gray-700 dark:text-gray-300"
                            >Password</Label
                        >
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400"
                            :tabindex="5"
                        >
                            Lupa password?
                        </TextLink>
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="h-11 rounded-xl bg-gray-50 transition-colors focus-visible:ring-purple-500 dark:bg-gray-900"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between pt-1">
                    <Label
                        for="remember"
                        class="flex cursor-pointer items-center space-x-3"
                    >
                        <Checkbox
                            id="remember"
                            name="remember"
                            :tabindex="3"
                            class="rounded border-gray-300 data-[state=checked]:border-purple-600 data-[state=checked]:bg-purple-600"
                        />
                        <span class="text-sm text-gray-600 dark:text-gray-400"
                            >Ingat saya</span
                        >
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-2 h-11 w-full rounded-xl bg-purple-600 text-white shadow-lg shadow-purple-600/20 transition-all hover:-translate-y-0.5 hover:bg-purple-700 hover:shadow-purple-600/30 focus-visible:ring-purple-500 disabled:opacity-70 disabled:hover:translate-y-0"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                    {{ processing ? 'Memproses...' : 'Masuk' }}
                </Button>
            </div>

            <div
                class="mt-2 text-center text-sm text-gray-500 dark:text-gray-400"
                v-if="canRegister"
            >
                Belum punya akun?
                <TextLink
                    :href="register()"
                    :tabindex="5"
                    class="font-semibold text-purple-600 hover:text-purple-700 dark:text-purple-400"
                >
                    Daftar di sini
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
