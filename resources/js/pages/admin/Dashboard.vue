<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    auth: { user: { first_name: string; last_name: string; role: string } }
}>();
</script>

<template>
    <AppLayout>
        <Head title="Admin Dashboard" />
        <div class="p-6">
            <h1 class="text-2xl font-semibold">
                Selamat datang, {{ props.auth.user.first_name }}!
            </h1>
            <p class="text-muted-foreground mt-1">
                Panel Administrator DifaFriends
            </p>
        </div>
    </AppLayout>
</template>
