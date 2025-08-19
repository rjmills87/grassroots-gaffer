<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { type Team } from '@/types/Team';
import { Head, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    teams: Array<Team>;
}>();

const clickEvent = () => {
    router.get('/teams/create');
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div v-if="props.teams.length === 0">
                <h2>Welcome</h2>
                <p>You don't currently have any teams yet</p>
                <Button @click="clickEvent" class="cursor-pointer"> Create Your First Team </Button>
            </div>
        </div>
    </AppLayout>
</template>
