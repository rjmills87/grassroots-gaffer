<script setup lang="ts">
import CreateEventDialog from '@/components/CreateEventDialog.vue';
import EmptyState from '@/components/EmptyState.vue';
import EventList from '@/components/EventList.vue';
import { capitalizeFirstLetter } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, User } from '@/types';
import { type Team } from '@/types/Team';
import { Head, router } from '@inertiajs/vue3';
import { CalendarDays, ShieldPlus } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    user: User;
    teams: Team[];
    selectedTeam: Team | null;
}>();

const selectedTeamId = computed(() => props.selectedTeam?.id ?? null);
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Events',
        href: selectedTeamId.value ? route('events.index', { team: selectedTeamId.value }) : route('events.index'),
    },
]);

const changeTeam = (event: Event) => {
    const value = Number((event.target as HTMLSelectElement).value);

    router.get(route('events.index'), { team: value }, { preserveScroll: true, preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Events" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8 p-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Events</h1>
                    <p class="text-muted-foreground">Plan and track events for a selected team.</p>
                </div>
                <div v-if="teams.length > 1" class="w-full max-w-sm space-y-2">
                    <label class="text-sm font-medium" for="team-select">Team</label>
                    <select
                        id="team-select"
                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                        :value="selectedTeamId ?? undefined"
                        @change="changeTeam"
                    >
                        <option v-for="team in teams" :key="team.id" :value="team.id">
                            {{ team.name }} -
                            {{ capitalizeFirstLetter(team.age_group) }}
                        </option>
                    </select>
                </div>
            </div>

            <div v-if="!selectedTeam" class="rounded-lg border border-dashed p-8 text-center">
                <EmptyState title="No team selected" description="Create or join a team to manage events.">
                    <template #icon>
                        <ShieldPlus class="h-5 w-5" />
                    </template>
                </EmptyState>
            </div>

            <section v-else class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-semibold">{{ selectedTeam.name }}</h2>
                        <p class="text-sm text-muted-foreground">Upcoming and past team events appear below.</p>
                    </div>
                    <CreateEventDialog v-if="$page.props.auth.user.role === 'coach'" :team="selectedTeam" />
                </div>

                <EventList :events="selectedTeam.events ?? []" />
            </section>

            <div v-if="teams.length === 0" class="rounded-lg border border-dashed p-8 text-center">
                <EmptyState title="No teams yet" description="Your upcoming events will appear after you create a team.">
                    <template #icon>
                        <CalendarDays class="h-5 w-5" />
                    </template>
                </EmptyState>
            </div>
        </div>
    </AppLayout>
</template>
