<script setup lang="ts">
import AddOrEditPlayerDialog from '@/components/AddOrEditPlayerDialog.vue';
import EmptyState from '@/components/EmptyState.vue';
import GuardianDetailsSheet from '@/components/GuardianDetailsSheet.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { capitalizeFirstLetter } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, User } from '@/types';
import { type Team } from '@/types/Team';
import { Head, router } from '@inertiajs/vue3';
import { ShieldPlus, Users } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    user: User;
    teams: Team[];
    selectedTeam: Team | null;
}>();

const selectedTeamId = computed(() => props.selectedTeam?.id ?? null);
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Squad',
        href: selectedTeamId.value ? route('squad.index', { team: selectedTeamId.value }) : route('squad.index'),
    },
]);

const changeTeam = (event: Event) => {
    const value = Number((event.target as HTMLSelectElement).value);

    router.get(route('squad.index'), { team: value }, { preserveScroll: true, preserveState: true, replace: true });
};
</script>

<template>
    <Head title="Squad" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-8 p-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Squad</h1>
                    <p class="text-muted-foreground">Manage players and view your team roster.</p>
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
                <EmptyState title="No team selected" description="Create or join a team to manage a squad.">
                    <template #icon>
                        <ShieldPlus class="h-5 w-5" />
                    </template>
                </EmptyState>
            </div>

            <section v-else class="space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-semibold">{{ selectedTeam.name }}</h2>
                        <p class="text-sm text-muted-foreground">View and update squad details for this team.</p>
                    </div>
                    <AddOrEditPlayerDialog v-if="$page.props.auth.user.role === 'coach'" :team="selectedTeam" />
                </div>

                <div v-if="selectedTeam.players && selectedTeam.players.length > 0" class="space-y-3">
                    <ul class="space-y-3 md:hidden">
                        <li v-for="player in selectedTeam.players" :key="`mobile-${player.id}`" class="rounded-lg border bg-card p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold">{{ player.name }}</p>
                                <span class="text-xs text-muted-foreground">#{{ player.squad_number ?? '-' }}</span>
                            </div>
                            <p class="mt-1 text-sm text-muted-foreground uppercase">{{ player.position || 'Not set' }}</p>
                            <div v-if="$page.props.auth.user.role === 'coach'" class="mt-3 flex flex-wrap items-center gap-2">
                                <GuardianDetailsSheet :player="player" />
                                <AddOrEditPlayerDialog :team="selectedTeam" :player="player" />
                            </div>
                        </li>
                    </ul>

                    <div class="hidden md:block">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-auto font-bold">Name</TableHead>
                                    <TableHead class="w-auto font-bold">Squad Number</TableHead>
                                    <TableHead class="w-auto font-bold">Position</TableHead>
                                    <TableHead v-if="$page.props.auth.user.role === 'coach'" class="w-auto font-bold">Guardian Details</TableHead>
                                    <TableHead v-if="$page.props.auth.user.role === 'coach'" class="w-auto font-bold">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="player in selectedTeam.players" :key="player.id">
                                    <TableCell>{{ player.name }}</TableCell>
                                    <TableCell class="font-bold">{{ player.squad_number }}</TableCell>
                                    <TableCell class="font-bold uppercase">{{ player.position }}</TableCell>
                                    <TableCell>
                                        <GuardianDetailsSheet v-if="$page.props.auth.user.role === 'coach'" :player="player" />
                                    </TableCell>
                                    <TableCell v-if="$page.props.auth.user.role === 'coach'" class="flex items-center gap-4">
                                        <AddOrEditPlayerDialog :team="selectedTeam" :player="player" />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </div>

                <div v-else>
                    <EmptyState title="No players yet" description="Add players to build your squad and start tracking responses.">
                        <template #icon>
                            <Users class="h-5 w-5" />
                        </template>
                    </EmptyState>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
