<script setup lang="ts">
import CreateTeamForm from '@/components/CreateTeamForm.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { capitalizeFirstLetter } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { type Team } from '@/types/Team';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    user: User;
    teams: Array<Team>;
}>();

const teamHeadingText = computed(() => {
    return props.teams.length === 1 ? 'My Team' : 'My Teams';
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <h2>Welcome {{ props.user.name }}</h2>
            <div v-if="props.teams.length === 0">
                <p>You don't currently have any teams yet</p>
                <Dialog>
                    <DialogTrigger as-child
                        ><Button v-if="props.user.role === 'coach'" class="cursor-pointer"> Create Your First Team </Button></DialogTrigger
                    >
                    <DialogContent>
                        <CreateTeamForm />
                    </DialogContent>
                </Dialog>
            </div>
            <div v-else>
                <h2 class="mb-4 text-2xl font-bold">{{ teamHeadingText }}</h2>
                <div class="flex flex-col gap-4">
                    <div class="flex gap-4 rounded-lg border p-4" v-for="team in props.teams" :key="team.id">
                        <Link :href="`/teams/${team.id}`">
                            <span class="text-xl">{{ team.name }}</span>
                            <span class="text-xl text-muted-foreground">{{ capitalizeFirstLetter(team.age_group) }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
