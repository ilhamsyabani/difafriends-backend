<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login, register } from '@/routes';
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">

        <!-- Navbar -->
        <header class="sticky top-0 z-50 border-b border-gray-100 dark:border-gray-800 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-sm">DF</span>
                        </div>
                        <span class="font-bold text-lg text-purple-700 dark:text-purple-400">
                            DifaFriends
                        </span>
                    </Link>

                    <!-- Nav -->
                    <nav class="hidden md:flex items-center gap-6 text-sm">
                        <Link href="/courses" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors">
                            Kelas
                        </Link>
                        <a href="/#categories" class="text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors">
                            Kategori
                        </a>
                    </nav>

                    <!-- Auth -->
                    <div class="flex items-center gap-3">
                        <template v-if="$page.props.auth.user">
                            <Link
                                href="/dashboard"
                                class="text-sm font-medium text-purple-600 hover:text-purple-700"
                            >
                                Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-purple-600 transition-colors"
                            >
                                Masuk
                            </Link>
                            <Link
                                :href="register()"
                                class="text-sm font-medium bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors"
                            >
                                Daftar
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="py-8 border-t border-gray-100 dark:border-gray-800 mt-20">
            <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-400">
                © 2026 DifaFriends. Platform Edukasi Inklusif Indonesia.
            </div>
        </footer>
    </div>
</template>
