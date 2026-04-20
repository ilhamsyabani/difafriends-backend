<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';
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

// ── State ────────────────────────────────────────────────────────
const email = ref('');
const password = ref('');
const remember = ref(false);
const showPass = ref(false);
const loading = ref(false);
const serverErr = ref('');

const emailFocus = ref(false);
const passwordFocus = ref(false);
const emailTouched = ref(false);
const passTouched = ref(false);

// ── Floating labels ──────────────────────────────────────────────
const emailUp = computed(() => emailFocus.value || email.value.length > 0);
const passwordUp = computed(
    () => passwordFocus.value || password.value.length > 0,
);

// ── Validasi real-time ───────────────────────────────────────────
const emailErr = computed(() => {
    if (!emailTouched.value) {
        return '';
    }

    if (!email.value) {
        return 'Email wajib diisi.';
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        return 'Format email tidak valid.';
    }

    return '';
});

const passErr = computed(() => {
    if (!passTouched.value) {
        return '';
    }

    if (!password.value) {
        return 'Password wajib diisi.';
    }

    if (password.value.length < 6) {
        return 'Password minimal 6 karakter.';
    }

    return '';
});

const canSubmit = computed(
    () => !emailErr.value && !passErr.value && email.value && password.value,
);

// ── Submit ───────────────────────────────────────────────────────
async function submit() {
    emailTouched.value = true;
    passTouched.value = true;

    if (!canSubmit.value) {
        return;
    }

    loading.value = true;
    serverErr.value = '';

    try {
        const response = await axios.post(store.url(), {
            email: email.value,
            password: password.value,
            remember: remember.value,
        });
        window.location.href = response.request?.responseURL ?? '/dashboard';
    } catch (err: any) {
        const errs = err?.response?.data?.errors ?? {};
        serverErr.value =
            (errs.email && errs.email[0]) ||
            (errs.password && errs.password[0]) ||
            err?.response?.data?.message ||
            'Email atau password salah.';
        password.value = '';
        passTouched.value = false;
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <AuthBase>
        <Head title="Masuk — DifaFriends" />

        <!-- ── Heading ────────────────────────────────────────────── -->
        <div class="mb-8">
            <p
                class="mb-1 text-xs font-semibold tracking-widest text-primary uppercase"
            >
                Difafriends
            </p>
            <h1
                class="text-[26px] font-bold tracking-tight text-gray-900 dark:text-white"
            >
                Selamat datang
            </h1>
            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">
                Masuk untuk lanjutkan perjalanan Anda
            </p>
        </div>

        <!-- Status -->
        <div
            v-if="status"
            class="mb-5 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200 dark:bg-green-900/20 dark:text-green-400 dark:ring-green-800/40"
        >
            <svg
                class="h-4 w-4 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            {{ status }}
        </div>

        <!-- Server error -->
        <div
            v-if="serverErr"
            class="mb-5 flex items-start gap-2.5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-600 ring-1 ring-red-200 dark:bg-red-900/20 dark:text-red-400 dark:ring-red-800/40"
        >
            <svg
                class="mt-0.5 h-4 w-4 shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                />
            </svg>
            {{ serverErr }}
        </div>

        <form @submit.prevent="submit" novalidate class="flex flex-col gap-3">
            <!-- ── Email ──────────────────────────────────────────── -->
            <div class="group relative">
                <input
                    id="email"
                    v-model="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    placeholder=""
                    :tabindex="1"
                    autofocus
                    @focus="emailFocus = true"
                    @blur="
                        emailFocus = false;
                        emailTouched = true;
                    "
                    :class="[
                        'w-full rounded-xl border px-4 pt-[22px] pb-2.5 text-sm text-gray-900 transition-all duration-150 outline-none',
                        'dark:text-white',
                        emailErr
                            ? 'border-red-300 bg-red-50/30 focus:border-red-400 focus:ring-2 focus:ring-red-300/20 dark:border-red-700/60 dark:bg-red-900/10'
                            : emailFocus
                              ? 'border-gray-900 bg-white ring-4 ring-gray-900/5 dark:border-white/30 dark:bg-gray-900 dark:ring-white/5'
                              : 'border-gray-200 bg-gray-50/50 hover:border-gray-300 dark:border-gray-800 dark:bg-gray-900/50 dark:hover:border-gray-700',
                    ]"
                />
                <!-- Floating label -->
                <label
                    for="email"
                    :class="[
                        'pointer-events-none absolute left-4 font-medium transition-all duration-150',
                        emailUp
                            ? 'top-[7px] text-[10px] ' +
                              (emailErr
                                  ? 'text-red-400'
                                  : emailFocus
                                    ? 'text-gray-500 dark:text-gray-400'
                                    : 'text-gray-400 dark:text-gray-500')
                            : 'top-1/2 -translate-y-1/2 text-sm text-gray-400 dark:text-gray-500',
                    ]"
                >
                    Alamat Email
                </label>
                <!-- Status icon -->
                <div
                    class="pointer-events-none absolute inset-y-0 right-3.5 flex items-center"
                >
                    <transition name="fade">
                        <svg
                            v-if="emailTouched && email && !emailErr"
                            class="h-4 w-4 text-green-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </transition>
                </div>
                <!-- Error -->
                <p
                    v-if="emailErr"
                    class="mt-1.5 flex items-center gap-1 px-1 text-[11px] text-red-500"
                >
                    <svg
                        class="h-3 w-3 shrink-0"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ emailErr }}
                </p>
            </div>

            <!-- ── Password ───────────────────────────────────────── -->
            <div class="relative">
                <input
                    id="password"
                    v-model="password"
                    :type="showPass ? 'text' : 'password'"
                    name="password"
                    autocomplete="current-password"
                    placeholder=""
                    :tabindex="2"
                    @focus="passwordFocus = true"
                    @blur="
                        passwordFocus = false;
                        passTouched = true;
                    "
                    :class="[
                        'w-full rounded-xl border px-4 pt-[22px] pr-11 pb-2.5 text-sm text-gray-900 transition-all duration-150 outline-none',
                        'dark:text-white',
                        passErr
                            ? 'border-red-300 bg-red-50/30 focus:border-red-400 focus:ring-2 focus:ring-red-300/20 dark:border-red-700/60 dark:bg-red-900/10'
                            : passwordFocus
                              ? 'border-gray-900 bg-white ring-4 ring-gray-900/5 dark:border-white/30 dark:bg-gray-900 dark:ring-white/5'
                              : 'border-gray-200 bg-gray-50/50 hover:border-gray-300 dark:border-gray-800 dark:bg-gray-900/50 dark:hover:border-gray-700',
                    ]"
                />
                <!-- Floating label -->
                <label
                    for="password"
                    :class="[
                        'pointer-events-none absolute left-4 font-medium transition-all duration-150',
                        passwordUp
                            ? 'top-[7px] text-[10px] ' +
                              (passErr
                                  ? 'text-red-400'
                                  : passwordFocus
                                    ? 'text-gray-500 dark:text-gray-400'
                                    : 'text-gray-400 dark:text-gray-500')
                            : 'top-1/2 -translate-y-1/2 text-sm text-gray-400 dark:text-gray-500',
                    ]"
                >
                    Kata Sandi
                </label>
                <!-- Eye toggle -->
                <button
                    type="button"
                    tabindex="-1"
                    @click="showPass = !showPass"
                    class="absolute inset-y-0 right-3.5 flex items-center text-gray-300 transition-colors hover:text-gray-600 dark:text-gray-600 dark:hover:text-gray-300"
                >
                    <svg
                        v-if="!showPass"
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                    </svg>
                    <svg
                        v-else
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                        />
                    </svg>
                </button>
                <!-- Error -->
                <p
                    v-if="passErr"
                    class="mt-1.5 flex items-center gap-1 px-1 text-[11px] text-red-500"
                >
                    <svg
                        class="h-3 w-3 shrink-0"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ passErr }}
                </p>
            </div>

            <!-- ── Remember + Forgot ──────────────────────────────── -->
            <div class="flex items-center justify-between pt-0.5">
                <label class="flex cursor-pointer items-center gap-2">
                    <div
                        @click="remember = !remember"
                        :class="[
                            'flex h-4 w-4 items-center justify-center rounded border transition-colors duration-150',
                            remember
                                ? 'border-gray-900 bg-gray-900 dark:border-white dark:bg-white'
                                : 'border-gray-300 bg-white dark:border-gray-700 dark:bg-transparent',
                        ]"
                    >
                        <svg
                            v-if="remember"
                            :class="[
                                'h-2.5 w-2.5',
                                'text-white dark:text-gray-900',
                            ]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="3"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Ingat saya</span
                    >
                </label>
                <Link
                    v-if="canResetPassword"
                    :href="request()"
                    :tabindex="5"
                    class="text-sm font-medium text-gray-500 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                >
                    Lupa kata sandi?
                </Link>
            </div>

            <!-- ── Submit ──────────────────────────────────────────── -->
            <button
                type="submit"
                :tabindex="4"
                :disabled="loading"
                :class="[
                    'mt-1 flex h-11 w-full items-center justify-center gap-2 rounded-xl text-sm font-semibold transition-all duration-150',
                    'focus-visible:ring-2 focus-visible:ring-primary/40 focus-visible:ring-offset-2 focus-visible:outline-none',
                    loading
                        ? 'cursor-not-allowed bg-primary/60 text-white'
                        : 'bg-primary text-white shadow-md shadow-primary/20 hover:bg-orange-500 hover:shadow-lg hover:shadow-primary/25 active:scale-[0.98]',
                ]"
            >
                <Spinner v-if="loading" class="h-4 w-4" />
                <span>{{ loading ? 'Memproses...' : 'Masuk' }}</span>
                <svg
                    v-if="!loading"
                    class="h-4 w-4 opacity-70"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 7l5 5m0 0l-5 5m5-5H6"
                    />
                </svg>
            </button>

            <!-- ── Divider ─────────────────────────────────────────── -->
            <div class="relative my-1">
                <div class="absolute inset-0 flex items-center">
                    <div
                        class="w-full border-t border-gray-100 dark:border-gray-800"
                    ></div>
                </div>
                <div class="relative flex justify-center">
                    <span
                        class="bg-white px-3 text-[11px] text-gray-400 dark:bg-[#0a0a0a] dark:text-gray-600"
                    >
                        atau lanjutkan dengan
                    </span>
                </div>
            </div>

            <!-- ── Social ─────────────────────────────────────────── -->
            <div class="grid grid-cols-2 gap-2.5">
                <button
                    type="button"
                    class="flex h-10 items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-600 transition-all hover:border-gray-300 hover:shadow-sm active:scale-[0.98] dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-700"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24">
                        <path
                            fill="#4285F4"
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        />
                        <path
                            fill="#34A853"
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        />
                        <path
                            fill="#FBBC05"
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        />
                        <path
                            fill="#EA4335"
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        />
                    </svg>
                    Google
                </button>
                <button
                    type="button"
                    class="flex h-10 items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-600 transition-all hover:border-gray-300 hover:shadow-sm active:scale-[0.98] dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-700"
                >
                    <svg class="h-4 w-4 fill-[#1877F2]" viewBox="0 0 24 24">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                        />
                    </svg>
                    Facebook
                </button>
            </div>

            <!-- ── Register ───────────────────────────────────────── -->
            <p
                v-if="canRegister"
                class="pt-1 text-center text-sm text-gray-400 dark:text-gray-500"
            >
                Belum punya akun?
                <Link
                    :href="register()"
                    :tabindex="6"
                    class="font-semibold text-gray-900 underline-offset-2 hover:underline dark:text-white"
                >
                    Daftar gratis
                </Link>
            </p>
        </form>
    </AuthBase>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
