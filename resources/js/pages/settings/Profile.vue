<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import type { BreadcrumbItem } from '@/types';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit(),
    },
];

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <h1 class="sr-only">Profile settings</h1>

        <SettingsLayout>
            <div class="flex flex-col space-y-8">
                <Heading
                    variant="small"
                    title="Profile Information"
                    description="Update your personal details, location, and bio."
                />

                <Form
                    v-bind="ProfileController.update.form()"
                    class="space-y-8"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <!-- Section: Names (Grid 2 Kolom) -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="first_name">First Name</Label>
                            <Input
                                id="first_name"
                                name="first_name"
                                :default-value="user.first_name"
                                required
                                autocomplete="given-name"
                                placeholder="e.g. John"
                            />
                            <InputError :message="errors.first_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="last_name">Last Name</Label>
                            <Input
                                id="last_name"
                                name="last_name"
                                :default-value="user.last_name"
                                required
                                autocomplete="family-name"
                                placeholder="e.g. Doe"
                            />
                            <InputError :message="errors.last_name" />
                        </div>
                    </div>

                    <!-- Section: Contact & Location (Grid 2 Kolom) -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="phone">Phone Number</Label>
                            <Input
                                id="phone"
                                name="phone"
                                type="tel"
                                :default-value="user.phone"
                                placeholder="+62..."
                            />
                            <InputError :message="errors.phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="city">City</Label>
                            <Input
                                id="city"
                                name="city"
                                :default-value="user.city"
                                placeholder="e.g. Jakarta"
                            />
                            <InputError :message="errors.city" />
                        </div>
                    </div>

                    <!-- Section: Email (Full Width) -->
                    <div class="grid gap-2">
                        <Label for="email">Email Address</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            :default-value="user.email"
                            required
                            autocomplete="email"
                            placeholder="email@example.com"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <!-- Section: Bio (Full Width) -->
                    <div class="grid gap-2">
                        <Label for="bio">Bio</Label>
                        <textarea
                            id="bio"
                            name="bio"
                            :default-value="user.bio"
                            rows="4"
                            placeholder="Tell us a little about yourself..."
                            class="resize-none"
                        />
                        <p class="text-xs text-muted-foreground">
                            Brief description for your profile.
                        </p>
                        <InputError :message="errors.bio" />
                    </div>

                    <!-- Email Verification Notice -->
                    <div
                        v-if="mustVerifyEmail && !user.email_verified_at"
                        class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900 dark:bg-yellow-900/20"
                    >
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            Your email address is unverified.
                            <Link
                                :href="send()"
                                as="button"
                                class="font-semibold underline underline-offset-4 hover:text-yellow-700"
                            >
                                Click here to resend verification.
                            </Link>
                        </p>
                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600"
                        >
                            A new verification link has been sent.
                        </div>
                    </div>

                    <!-- Submit Button Area -->
                    <div class="flex items-center gap-4 border-t pt-6">
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="px-8"
                        >
                            Save Changes
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm font-medium text-green-600"
                            >
                                ✓ Changes saved successfully.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>

            <!-- Divider -->
            <div
                class="my-10 border-t border-neutral-200 dark:border-neutral-800"
            ></div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
