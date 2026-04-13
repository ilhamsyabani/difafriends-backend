<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Newspaper,
    Users,
    Tag,
    BookOpen,
    ShoppingCart,
    Calendar,
    ClipboardList,
    Settings,
    BarChart2,
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarSeparator,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const user = (page.props.auth as any).user;
const role = user?.role ?? 'user';

const adminNav: NavItem[] = [
    { title: 'Dashboard', href: '/admin/dashboard', icon: LayoutGrid },
    { title: 'Kelola User', href: '/admin/users', icon: Users },
    { title: 'Kategori', href: '/admin/categories', icon: Tag },
    { title: 'Kelas', href: '/admin/courses', icon: BookOpen },
    { title: 'Jadwal Sesi', href: '/admin/schedules', icon: Calendar },
    { title: 'Artikel', href: '/admin/articles', icon: Newspaper },
    { title: 'Transaksi', href: '/admin/orders', icon: ShoppingCart },
    { title: 'Laporan', href: '/admin/reports', icon: BarChart2 },
];

const instructorNav: NavItem[] = [
    { title: 'Dashboard', href: '/instructor/dashboard', icon: LayoutGrid },
    { title: 'Kelas Saya', href: '/instructor/courses', icon: BookOpen },
];

const companionNav: NavItem[] = [
    { title: 'Dashboard', href: '/companion/dashboard', icon: LayoutGrid },
    { title: 'Jadwal Saya', href: '/companion/schedules', icon: Calendar },
    { title: 'Booking', href: '/companion/bookings', icon: ClipboardList },
];

const userNav: NavItem[] = [
    { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
    { title: 'Kelas Saya', href: '/user/enrollments', icon: BookOpen },
    { title: 'Riwayat Order', href: '/user/orders', icon: ShoppingCart },
];

const mainNavItems =
    role === 'admin'
        ? adminNav
        : role === 'instructor'
          ? instructorNav
          : role === 'companion'
            ? companionNav
            : userNav;

// const footerNavItems: NavItem[] = [
//     { title: 'Pengaturan', href: '/settings/profile', icon: Settings },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <!-- Logo header -->
        <SidebarHeader class="border-b border-sidebar-border px-2 py-3">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- Navigation -->
        <SidebarContent class="py-3">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <!-- Footer -->
        <SidebarFooter class="border-t border-sidebar-border pb-3">
            <!-- <NavFooter :items="footerNavItems" class="pt-2" />
            <NavUser /> -->
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
