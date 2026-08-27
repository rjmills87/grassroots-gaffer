<script setup lang="ts">
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const join = computed(() => page.props.flash?.join ?? null);
</script>

<template>
    <Head title="Join request sent" />

    <PublicLayout>
        <div class="mx-auto max-w-lg px-4 py-16 sm:px-6">
            <div class="rounded-2xl border bg-white p-6 text-center shadow-sm dark:bg-gray-900">
                <p class="text-sm font-semibold text-teal-600 dark:text-teal-400">Request sent</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    We've told the coach
                </h1>
                <p v-if="join" class="mt-3 text-sm leading-6 text-muted-foreground">
                    {{ join.coach_first_name }} will confirm that you are a parent of
                    <span class="font-medium text-foreground">{{ join.child_name }}</span>
                    on {{ join.team_name }}. You'll get an email when you can log in and RSVP.
                </p>
                <p v-else class="mt-3 text-sm leading-6 text-muted-foreground">
                    The coach will confirm your request. You'll get an email when you can log in and RSVP for your child.
                </p>
                <Link :href="route('home')" class="mt-6 inline-flex">
                    <Button class="h-11 cursor-pointer bg-teal-600 hover:bg-teal-500">Back to Squadra365</Button>
                </Link>
            </div>
        </div>
    </PublicLayout>
</template>
