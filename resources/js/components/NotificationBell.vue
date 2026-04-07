<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, onUnmounted } from 'vue';

const isOpen = ref(false);
const unreadCount = ref(0);
const notifications = ref<any[]>([]);
const loading = ref(false);
const bellContainer = ref<HTMLElement | null>(null);

async function fetchNotifications() {
    try {
        loading.value = true;
        const res = await axios.get('/notifications');
        notifications.value = res.data.notifications;
        unreadCount.value = res.data.unread_count;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
}

async function markAsRead(id: string, url: string) {
    await axios.post(`/notifications/${id}/read`);

    // Update local state
    const notif = notifications.value.find((n) => n.id === id);
    if (notif) notif.read_at = new Date().toISOString();
    unreadCount.value = Math.max(0, unreadCount.value - 1);

    isOpen.value = false;

    if (url) router.visit(url);
}

async function markAllRead() {
    await axios.post('/notifications/read-all');
    notifications.value.forEach((n) => (n.read_at = new Date().toISOString()));
    unreadCount.value = 0;
}

// Close dropdown kalau klik di luar
function handleClickOutside(e: MouseEvent) {
    if (
        bellContainer.value &&
        !bellContainer.value.contains(e.target as Node)
    ) {
        isOpen.value = false;
    }
}

// Auto polling setiap 30 detik
let interval: any;
onMounted(() => {
    fetchNotifications();
    interval = setInterval(fetchNotifications, 30000);
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    clearInterval(interval);
    document.removeEventListener('click', handleClickOutside);
});

function toggleOpen() {
    isOpen.value = !isOpen.value;
    if (isOpen.value) fetchNotifications();
}

function iconPath(icon: string): string {
    const icons: Record<string, string> = {
        'check-circle': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        calendar:
            'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'book-open':
            'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        default:
            'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
    };
    return icons[icon] ?? icons['default'];
}
</script>

<template>
    <div ref="bellContainer" class="relative">
        <!-- Bell Button -->
        <button
            @click="toggleOpen"
            class="relative rounded-lg p-2 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
        >
            <svg
                class="h-5 w-5 text-gray-600 dark:text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
            </svg>

            <!-- Badge -->
            <span
                v-if="unreadCount > 0"
                class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <div
            v-if="isOpen"
            class="absolute top-full right-0 z-50 mt-2 w-80 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
        >
            <!-- Header -->
            <div
                class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-700"
            >
                <h3 class="text-sm font-semibold">Notifikasi</h3>
                <button
                    v-if="unreadCount > 0"
                    @click="markAllRead"
                    class="text-xs font-medium text-purple-600 hover:text-purple-700"
                >
                    Tandai semua dibaca
                </button>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="py-8 text-center text-sm text-gray-400">
                Memuat...
            </div>

            <!-- Empty -->
            <div
                v-else-if="notifications.length === 0"
                class="py-10 text-center text-gray-400"
            >
                <svg
                    class="mx-auto mb-2 h-10 w-10 opacity-30"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                    />
                </svg>
                <p class="text-sm">Tidak ada notifikasi</p>
            </div>

            <!-- List -->
            <div
                v-else
                class="max-h-80 divide-y divide-gray-100 overflow-y-auto dark:divide-gray-700"
            >
                <button
                    v-for="notif in notifications"
                    :key="notif.id"
                    @click="markAsRead(notif.id, notif.data.url)"
                    :class="[
                        'flex w-full items-start gap-3 px-4 py-3 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50',
                        !notif.read_at
                            ? 'bg-purple-50/50 dark:bg-purple-900/10'
                            : '',
                    ]"
                >
                    <!-- Icon -->
                    <div
                        :class="[
                            'mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full',
                            !notif.read_at
                                ? 'bg-purple-100 dark:bg-purple-900/30'
                                : 'bg-gray-100 dark:bg-gray-700',
                        ]"
                    >
                        <svg
                            :class="[
                                'h-4 w-4',
                                !notif.read_at
                                    ? 'text-purple-600'
                                    : 'text-gray-400',
                            ]"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                :d="iconPath(notif.data.icon)"
                            />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="min-w-0 flex-1">
                        <p
                            :class="[
                                'truncate text-sm font-medium',
                                !notif.read_at
                                    ? 'text-gray-900 dark:text-white'
                                    : 'text-gray-600 dark:text-gray-400',
                            ]"
                        >
                            {{ notif.data.title }}
                        </p>
                        <p
                            class="mt-0.5 line-clamp-2 text-xs text-gray-500 dark:text-gray-400"
                        >
                            {{ notif.data.message }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ notif.created_at }}
                        </p>
                    </div>

                    <!-- Unread dot -->
                    <div
                        v-if="!notif.read_at"
                        class="mt-2 h-2 w-2 shrink-0 rounded-full bg-purple-600"
                    ></div>
                </button>
            </div>
        </div>
    </div>
</template>
