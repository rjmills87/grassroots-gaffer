<script setup lang="ts">
import CreateTeamDialog from '@/components/CreateTeamDialog.vue';
import CreateTeamForm from '@/components/CreateTeamForm.vue';
import DeleteConfirmation from '@/components/DeleteConfirmation.vue';
import EmptyState from '@/components/EmptyState.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { capitalizeFirstLetter, formatDateTime } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { type Team } from '@/types/Team';
import { Head, Link, router } from '@inertiajs/vue3';
import { CalendarDays, ChevronRight, EllipsisVertical, MessageSquareText, Plus, ShieldPlus, Trash2, Users } from 'lucide-vue-next';
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
    selectedTeam: Team | null;
    selectedTeamId: number | null;
}>();

const hasEvents = computed(() => (props.selectedTeam?.events?.length ?? 0) > 0);
const hasMessages = computed(() => (props.selectedTeam?.messages?.length ?? 0) > 0);
const isCreateTeamOpen = ref(false);
const isDeleteTeamOpen = ref(false);

const changeTeam = (event: Event) => {
    const value = Number((event.target as HTMLSelectElement).value);
    router.get(route('dashboard'), { team: value }, { preserveScroll: true, preserveState: true, replace: true });
};
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
            <!-- Team Summary Section -->
            <section>
                <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold">Team Summary</h2>

                        <p v-if="teams.length > 1" class="text-sm text-muted-foreground">Switch teams to view a focused summary.</p>
                    </div>
                    <div v-if="teams.length > 1" class="w-full max-w-sm space-y-2">
                        <label class="text-sm font-medium" for="dashboard-team-select">Team</label>
                        <select
                            id="dashboard-team-select"
                            class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                            :value="selectedTeamId ?? undefined"
                            @change="changeTeam"
                        >
                            <option v-for="team in teams" :key="team.id" :value="team.id">
                                {{ team.name }} - {{ capitalizeFirstLetter(team.age_group) }}
                            </option>
                        </select>
                    </div>
                    <CreateTeamDialog v-if="user.role === 'coach' && teams.length === 0" />
                    <DropdownMenu v-else-if="user.role === 'coach'">
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" size="icon" class="h-9 w-9 cursor-pointer">
                                <span class="sr-only">Team actions</span>
                                <EllipsisVertical class="h-5 w-5" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-44">
                            <DropdownMenuItem class="cursor-pointer" @select.prevent="isCreateTeamOpen = true">
                                <Plus class="mr-2 h-4 w-4" />
                                Create Team
                            </DropdownMenuItem>
                            <DropdownMenuSeparator v-if="selectedTeam" />
                            <DropdownMenuItem
                                v-if="selectedTeam"
                                class="cursor-pointer text-destructive focus:text-destructive"
                                @select.prevent="isDeleteTeamOpen = true"
                            >
                                <Trash2 class="mr-2 h-4 w-4" />
                                Delete Team
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>

                <div v-if="teams.length === 0" class="rounded-lg border border-dashed p-8 text-center">
                    <EmptyState title="No teams yet" description="Create your first team and start managing your squad.">
                        <template #icon>
                            <ShieldPlus class="h-5 w-5" />
                        </template>
                    </EmptyState>
                </div>
                <div v-else-if="selectedTeam" class="group block rounded-lg border p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div class="mt-4 flex items-center gap-2 text-xs text-muted-foreground">
                            <img :src="selectedTeam.team_badge_url" alt="Team Badge" class="h-24 w-24 rounded-full" />
                            <div>
                                <h3 class="text-xl font-bold">{{ selectedTeam.name }}</h3>
                                <p class="mt-2 text-base font-medium">
                                    {{ capitalizeFirstLetter(selectedTeam.age_group) }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-base font-medium text-muted-foreground">
                            <Users class="h-5 w-5" />
                            <span>{{ selectedTeam.players?.length ?? 0 }} players</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Navigation -->
            <section class="grid gap-4 md:grid-cols-3">
                <Link
                    :href="selectedTeamId ? route('squad.index', { team: selectedTeamId }) : route('squad.index')"
                    class="rounded-lg border p-5 transition-colors hover:bg-accent/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="font-semibold">Squad</h3>
                        <ChevronRight class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <p class="text-sm text-muted-foreground">Manage players, positions, and squad details by team.</p>
                </Link>
                <Link
                    :href="selectedTeamId ? route('events.index', { team: selectedTeamId }) : route('events.index')"
                    class="rounded-lg border p-5 transition-colors hover:bg-accent/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="font-semibold">Events</h3>
                        <ChevronRight class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <p class="text-sm text-muted-foreground">Review and manage upcoming fixtures, training, and team events.</p>
                </Link>
                <Link
                    :href="selectedTeamId ? route('announcements.index', { team: selectedTeamId }) : route('announcements.index')"
                    class="rounded-lg border p-5 transition-colors hover:bg-accent/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="font-semibold">Announcements</h3>
                        <ChevronRight class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <p class="text-sm text-muted-foreground">Send updates and review recent team announcements.</p>
                </Link>
            </section>

            <!-- Upcoming Events & Recent Announcements -->
            <div class="grid gap-8 md:grid-cols-2">
                <!-- Upcoming Events -->
                <section>
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold">Upcoming Events</h2>
                        <Link
                            :href="selectedTeamId ? route('events.index', { team: selectedTeamId }) : route('events.index')"
                            class="text-sm text-primary underline-offset-4 hover:underline"
                        >
                            View all
                        </Link>
                    </div>
                    <div class="rounded-lg border p-4">
                        <div v-if="hasEvents" class="space-y-4">
                            <div>
                                <h3 class="mb-2 font-medium">{{ selectedTeam?.name }}</h3>
                                <div v-for="event in selectedTeam?.events?.slice(0, 2)" :key="event.id" class="mb-3">
                                    <Link :href="`/events/${event.id}`">
                                        <div class="flex items-start">
                                            <div class="mb-2 flex items-center gap-2 border-b border-gray-200 pb-2">
                                                <p class="font-medium">{{ capitalizeFirstLetter(event.type) }}</p>
                                                <p class="text-sm text-muted-foreground">
                                                    {{ formatDateTime(event.occurs_at) }} • {{ event.location }}
                                                </p>
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

                <!-- Recent Announcements -->
                <section>
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold">Recent Announcements</h2>
                        <Link
                            :href="selectedTeamId ? route('announcements.index', { team: selectedTeamId }) : route('announcements.index')"
                            class="text-sm text-primary underline-offset-4 hover:underline"
                        >
                            View all
                        </Link>
                    </div>
                    <div class="rounded-lg border p-4">
                        <div v-if="hasMessages" class="space-y-4">
                            <div>
                                <h3 class="mb-2 font-medium">{{ selectedTeam?.name }}</h3>
                                <div v-for="message in selectedTeam?.messages?.slice(0, 2)" :key="message.id" class="mb-3">
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
                        <EmptyState v-else title="No recent announcements" description="Team announcements and updates will show up here.">
                            <template #icon>
                                <MessageSquareText class="h-5 w-5" />
                            </template>
                        </EmptyState>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>

    <Dialog v-model:open="isCreateTeamOpen">
        <DialogContent>
            <CreateTeamForm @close="isCreateTeamOpen = false" />
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="isDeleteTeamOpen">
        <DialogContent>
            <DeleteConfirmation
                v-if="selectedTeam"
                itemType="Team"
                :itemName="selectedTeam.name"
                deleteRoute="teams.destroy"
                :itemId="selectedTeam.id"
                :toastMessage="`Team: ${selectedTeam.name} has been deleted successfully`"
                @close="isDeleteTeamOpen = false"
            />
        </DialogContent>
    </Dialog>
</template>
