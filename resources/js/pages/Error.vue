<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    status: number;
}>();

const title = computed(() => {
    if (props.status === 403) {
        return 'Access denied';
    }

    if (props.status === 404) {
        return 'Page not found';
    }

    return 'Something went wrong';
});

const description = computed(() => {
    if (props.status === 403) {
        return "You don't have permission to access this page.";
    }

    if (props.status === 404) {
        return 'The page you were looking for does not exist.';
    }

    return 'An unexpected error occurred. Please try again.';
});
</script>

<template>
    <Head :title="title" />
    <AppLayout>
        <div class="flex min-h-[70vh] items-center justify-center p-6">
            <div class="w-full max-w-xl rounded-xl border bg-card p-10 text-center shadow-sm">
                <p class="text-sm font-medium text-muted-foreground">Error {{ status }}</p>
                <h1 class="mt-2 text-3xl font-semibold">{{ title }}</h1>
                <p class="mt-3 text-sm text-muted-foreground">{{ description }}</p>
                <div class="mt-6 flex justify-center gap-3">
                    <Link
                        href="/dashboard"
                        class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                    >
                        Back to dashboard
                    </Link>
                    <Link
                        href="/"
                        class="inline-flex items-center rounded-md border px-4 py-2 text-sm font-medium transition-colors hover:bg-accent"
                    >
                        Go to home
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
