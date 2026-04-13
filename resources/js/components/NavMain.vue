<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel
            class="mb-1 px-2 text-[11px] font-semibold tracking-widest text-sidebar-foreground/50 uppercase"
        >
            Menu
        </SidebarGroupLabel>
        <SidebarMenu class="gap-0.5">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href" class="flex items-center gap-3">
                        <component
                            :is="item.icon"
                            :class="[
                                'h-4 w-4 shrink-0 transition-colors',
                                isCurrentUrl(item.href)
                                    ? 'text-sidebar-primary'
                                    : 'text-sidebar-foreground/60',
                            ]"
                        />
                        <span
                            :class="[
                                'text-sm transition-colors',
                                isCurrentUrl(item.href)
                                    ? 'font-medium text-sidebar-accent-foreground'
                                    : 'text-sidebar-foreground',
                            ]"
                        >
                            {{ item.title }}
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
