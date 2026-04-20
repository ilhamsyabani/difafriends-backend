<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout title="Verifikasi Email">
        <Head title="Verifikasi Email" />

        <!-- Heading -->
        <div class="mb-7">
            <div
                class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10"
            >
                <svg
                    class="h-6 w-6 text-primary"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
                    />
                </svg>
            </div>
            <h1
                class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
            >
                Cek email kamu
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kami telah mengirim tautan verifikasi ke email kamu. Klik tautan
                tersebut untuk mengaktifkan akun.
            </p>
        </div>

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400"
        >
            Tautan verifikasi baru telah dikirim ke email kamu.
        </div>

        <Form
            v-bind="send.form()"
            v-slot="{ processing }"
            class="flex flex-col gap-4"
        >
            <Button
                type="submit"
                class="h-11 w-full rounded-xl bg-primary text-sm font-semibold text-white shadow-md shadow-primary/25 transition-all hover:-translate-y-0.5 hover:bg-orange-500 disabled:opacity-70 disabled:hover:translate-y-0"
                :disabled="processing"
            >
                <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                {{
                    processing ? 'Mengirim...' : 'Kirim Ulang Email Verifikasi'
                }}
            </Button>

            <a
                :href="logout()"
                class="block text-center text-sm text-gray-500 underline-offset-2 hover:text-gray-700 hover:underline dark:text-gray-400 dark:hover:text-gray-300"
            >
                Keluar dari akun
            </a>
        </Form>
    </AuthLayout>
</template>
