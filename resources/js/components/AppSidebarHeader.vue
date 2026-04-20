<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import NotificationBell from '@/components/NotificationBell.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useInitials } from '@/composables/useInitials';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = computed(() => page.props.auth.user as any);
const { getInitials } = useInitials();

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <header
        class="sticky top-0 z-20 flex h-16 w-full shrink-0 items-center justify-between border-b border-border/60 bg-background/95 px-4 shadow-sm backdrop-blur-sm transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-6"
    >
        <!-- Kiri: Trigger + Breadcrumbs -->
        <div class="flex items-center gap-2">
            <SidebarTrigger
                class="-ml-1 text-muted-foreground hover:text-foreground"
            />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <div class="mx-1 h-4 w-px bg-border" />
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Kanan: Notif + User -->
        <div class="ml-auto flex items-center gap-2">
            <NotificationBell />

            <div class="mx-1 h-5 w-px bg-border" />

            <!-- User dropdown -->
            <DropdownMenu>
                <DropdownMenuTrigger
                    class="flex items-center gap-2.5 rounded-lg px-2 py-1.5 transition-colors hover:bg-accent focus-visible:outline-none"
                    data-test="header-user-trigger"
                >
                    <Avatar class="h-7 w-7 rounded-lg">
                        <AvatarImage
                            v-if="user?.avatar"
                            :src="user.avatar"
                            :alt="user.name"
                        />
                        <AvatarFallback
                            class="rounded-lg bg-primary text-xs font-semibold text-white"
                        >
                            {{ getInitials(user?.name ?? '') }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="hidden text-left md:block">
                        <p class="text-sm leading-none font-medium">
                            {{ user?.name }}
                        </p>
                        <p
                            class="mt-0.5 text-xs leading-none text-muted-foreground"
                        >
                            {{ user?.email }}
                        </p>
                    </div>
                    <svg
                        class="ml-1 hidden h-3.5 w-3.5 text-muted-foreground md:block"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </DropdownMenuTrigger>

                <DropdownMenuContent
                    align="end"
                    class="w-56 rounded-xl shadow-lg"
                    :side-offset="8"
                >
                    <DropdownMenuLabel class="p-0 font-normal">
                        <div class="flex items-center gap-2.5 px-3 py-2.5">
                            <Avatar class="h-8 w-8 rounded-lg">
                                <AvatarImage
                                    v-if="user?.avatar"
                                    :src="user.avatar"
                                    :alt="user.name"
                                />
                                <AvatarFallback
                                    class="rounded-lg bg-primary text-xs font-semibold text-white"
                                >
                                    {{ getInitials(user?.name ?? '') }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="grid flex-1 text-left leading-tight">
                                <span class="truncate text-sm font-semibold">{{
                                    user?.name
                                }}</span>
                                <span
                                    class="truncate text-xs text-muted-foreground"
                                    >{{ user?.email }}</span
                                >
                            </div>
                        </div>
                    </DropdownMenuLabel>

                    <DropdownMenuSeparator />

                    <DropdownMenuGroup>
                        <DropdownMenuItem as-child>
                            <Link
                                :href="edit()"
                                class="flex cursor-pointer items-center gap-2"
                                prefetch
                            >
                                <Settings class="h-4 w-4" />
                                Pengaturan Profil
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuGroup>

                    <DropdownMenuSeparator />

                    <DropdownMenuItem as-child>
                        <Link
                            :href="logout()"
                            as="button"
                            class="flex w-full cursor-pointer items-center gap-2 text-red-600 focus:text-red-600 dark:text-red-400"
                            @click="handleLogout"
                            data-test="logout-button"
                        >
                            <LogOut class="h-4 w-4" />
                            Keluar
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
