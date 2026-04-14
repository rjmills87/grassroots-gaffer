<script setup lang="ts">
import CreateTeamDialog from '@/components/CreateTeamDialog.vue';
import EmptyState from '@/components/EmptyState.vue';
import { capitalizeFirstLetter, formatDateTime } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { type Team } from '@/types/Team';
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, MessageSquareText, ShieldPlus, Users } from 'lucide-vue-next';
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
                    <CreateTeamDialog v-if="user.role === 'coach'" />
                </div>

                <div v-if="teams.length === 0" class="rounded-lg border border-dashed p-8 text-center">
                    <EmptyState title="No teams yet" description="Create your first team and start managing your squad.">
                        <template #icon>
                            <ShieldPlus class="h-5 w-5" />
                        </template>
                    </EmptyState>
                </div>

                <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="team in teams"
                        :key="team.id"
                        :href="`/teams/${team.id}`"
                        class="group block rounded-lg border p-6 transition-colors hover:bg-accent/50"
                    >
                        <div class="flex items-center justify-between">
                            <img :src="team.team_badge_url" alt="Team Badge" class="h-32 w-32 rounded-full" />
                            <h3 class="text-lg font-medium">{{ team.name }}</h3>
                        </div>
                        <p class="mt-2 text-sm text-muted-foreground">
                            {{ capitalizeFirstLetter(team.age_group) }}
                        </p>
                        <div class="mt-4 flex items-center gap-2 text-xs text-muted-foreground">
                            <Users class="h-4 w-4" />
                            <span>{{ team.players?.length ?? 0 }} players</span>
                        </div>
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
                                    <Link :href="`/events/${event.id}`">
                                        <div class="flex items-start">
                                            <div class="mb-2 flex items-center gap-2 border-b border-gray-200 pb-2">
                                                <p class="font-medium">{{ capitalizeFirstLetter(event.type) }}</p>
                                                <p class="text-sm text-muted-foreground">{{ formatDateTime(event.occurs_at) }} • {{ event.location }}</p>
                                            </div>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <EmptyState v-else title="No upcoming events" description="Scheduled events will appear here for quick access.">
                            <template #icon>
                                <CalendarDays class="h-5 w-5" />
                            </template>
                        </EmptyState>
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
                                                {{ formatDateTime(message.created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <EmptyState v-else title="No recent messages" description="Team announcements and updates will show up here.">
                            <template #icon>
                                <MessageSquareText class="h-5 w-5" />
                            </template>
                        </EmptyState>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
