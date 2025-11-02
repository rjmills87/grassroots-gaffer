<script setup lang="ts">
import CreateTeamForm from '@/components/CreateTeamForm.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { capitalizeFirstLetter, formatDate } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { type Team } from '@/types/Team';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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

const isCreateTeamOpen = ref(false);

const teamHeadingText = computed(() => {
    if (props.teams.length === 0) return 'Create your first team';
    if (props.teams.length === 1) return 'My Team';
    return 'My Teams';
});

const hasEvents = computed(() => {
    return props.teams.some((team) => team.events?.length > 0);
});

const hasMessages = computed(() => {
    return props.teams.some((team) => team.messages?.length > 0);
});
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8 p-8">
            <h1 class="text-3xl font-bold tracking-tight">Welcome back, {{ user.name.split(' ')[0] }}!</h1>
            <p class="text-muted-foreground">Here's what's happening with your teams</p>
        </div>

        <!-- Main Content -->
        <div class="space-y-8 p-8 md:space-y-12">
            <!-- Teams Section -->
            <section>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-2xl font-semibold">{{ teamHeadingText }}</h2>

                    <Dialog v-model:open="isCreateTeamOpen">
                        <DialogTrigger as-child>
                            <Button>
                                <span class="hidden sm:inline">Create New Team</span>
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <CreateTeamForm @close="isCreateTeamOpen = false" />
                        </DialogContent>
                    </Dialog>
                </div>

                <div v-if="teams.length === 0" class="rounded-lg border border-dashed p-8 text-center">
                    <p class="mb-4 text-muted-foreground">You don't have any teams yet</p>
                </div>

                <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="team in teams"
                        :key="team.id"
                        :href="`/teams/${team.id}`"
                        class="group block rounded-lg border p-6 transition-colors hover:bg-accent/50"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">{{ team.name }}</h3>
                        </div>
                        <p class="mt-2 text-sm text-muted-foreground">
                            {{ capitalizeFirstLetter(team.age_group) }}
                        </p>
                    </Link>
                </div>
            </section>

            <!-- Upcoming Events & Recent Messages -->
            <div class="grid gap-8 md:grid-cols-2">
                <!-- Upcoming Events -->
                <section>
                    <h2 class="mb-4 text-xl font-semibold">Upcoming Events</h2>
                    <div class="rounded-lg border p-4">
                        <div v-if="hasEvents" class="space-y-4">
                            <div v-for="team in teams.filter((t) => t.events?.length > 0)" :key="team.id">
                                <h3 class="mb-2 font-medium">{{ team.name }}</h3>
                                <div v-for="event in team.events?.slice(0, 2)" :key="event.id" class="mb-3">
                                    <div class="flex items-start">
                                        <div>
                                            <p class="font-medium">{{ capitalizeFirstLetter(event.type) }}</p>
                                            <p class="text-sm text-muted-foreground">{{ formatDate(event.occurs_at) }} • {{ event.location }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-8 text-center text-muted-foreground">
                            <p>No upcoming events</p>
                        </div>
                    </div>
                </section>

                <!-- Recent Messages -->
                <section>
                    <h2 class="mb-4 text-xl font-semibold">Recent Messages</h2>
                    <div class="rounded-lg border p-4">
                        <div v-if="hasMessages" class="space-y-4">
                            <div v-for="team in teams.filter((t) => t.messages?.length > 0)" :key="team.id">
                                <h3 class="mb-2 font-medium">{{ team.name }}</h3>
                                <div v-for="message in team.messages?.slice(0, 2)" :key="message.id" class="mb-3">
                                    <div class="flex items-start">
                                        <div
                                            class="mr-3 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm text-primary"
                                        >
                                            {{ message.user?.name?.charAt(0) || 'U' }}
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ message.user?.name || 'User' }}</p>
                                            <p class="line-clamp-2 text-sm text-muted-foreground">
                                                {{ message.message }}
                                            </p>
                                            <p class="mt-1 text-xs text-muted-foreground">
                                                {{ formatDate(message.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-8 text-center text-muted-foreground">
                            <p>No recent messages</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
