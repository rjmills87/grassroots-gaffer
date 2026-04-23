<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import AddOrEditPlayerDialog from '@/components/AddOrEditPlayerDialog.vue';
import CreateEventDialog from '@/components/CreateEventDialog.vue';
import CreateMessageDialog from '@/components/CreateMessageDialog.vue';
import DeleteConfirmationDialog from '@/components/DeleteConfirmationDialog.vue';
import EmptyState from '@/components/EmptyState.vue';
import EventList from '@/components/EventList.vue';
import GuardianDetailsSheet from '@/components/GuardianDetailsSheet.vue';
import MessageList from '@/components/MessageList.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Player } from '@/types/Player';
import { Team } from '@/types/Team';
import { Head } from '@inertiajs/vue3';
import { Users } from 'lucide-vue-next';

const props = defineProps<{
    team: Team;
    players: Player;
}>();
</script>

<template>
    <Head :title="team.name" />
    <AppLayout>
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center gap-4" v-if="team.team_badge_url">
                <img :src="`${team.team_badge_url}`" alt="Team Badge" class="h-20 w-20 rounded-full" />
                <h1 class="text-2xl font-semibold">{{ team.name }}</h1>
            </div>
            <div v-else>
                <h1 class="text-2xl font-semibold">{{ team.name }}</h1>
            </div>
            <div class="mt-8">
                <h2 class="text-xl font-semibold">Squad List</h2>
                <div v-if="props.team.players && props.team.players.length > 0" class="mt-4">
                    <ul class="space-y-3 md:hidden">
                        <li v-for="player in props.team.players" :key="`mobile-${player.id}`" class="rounded-lg border bg-card p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold">{{ player.name }}</p>
                                <span class="text-xs text-muted-foreground">#{{ player.squad_number ?? '-' }}</span>
                            </div>
                            <p class="mt-1 text-sm uppercase text-muted-foreground">{{ player.position || 'Not set' }}</p>
                            <div v-if="$page.props.auth.user.role === 'coach'" class="mt-3 flex flex-wrap items-center gap-2">
                                <GuardianDetailsSheet :player="player" />
                                <AddOrEditPlayerDialog :team="team" :player="player" />
                                <DeleteConfirmationDialog
                                    itemType="Player"
                                    :itemName="player.name"
                                    deleteRoute="players.destroy"
                                    :itemId="player.id"
                                    :toastMessage="`Player: ${player.name} has been deleted successfully`"
                                />
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
                                <TableRow v-for="player in props.team.players" :key="player.id">
                                    <TableCell class="w-auto">{{ player.name }}</TableCell>
                                    <TableCell class="w-auto font-bold">{{ player.squad_number }}</TableCell>
                                    <TableCell class="w-auto font-bold uppercase">{{ player.position }}</TableCell>
                                    <TableCell class="w-auto">
                                        <GuardianDetailsSheet v-if="$page.props.auth.user.role === 'coach'" :player="player" />
                                    </TableCell>
                                    <TableCell v-if="$page.props.auth.user.role === 'coach'" class="m-0 flex items-center gap-4">
                                        <AddOrEditPlayerDialog :team="team" :player="player" />
                                        <DeleteConfirmationDialog
                                            itemType="Player"
                                            :itemName="player.name"
                                            deleteRoute="players.destroy"
                                            :itemId="player.id"
                                            :toastMessage="`Player: ${player.name} has been deleted successfully`"
                                        />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </div>
                <div v-else class="mt-4">
                    <EmptyState title="No players yet" description="Add players to build your squad and start tracking responses.">
                        <template #icon>
                            <Users class="h-5 w-5" />
                        </template>
                    </EmptyState>
                </div>
                <AddOrEditPlayerDialog v-if="$page.props.auth.user.role === 'coach'" class="pr-4" :team="team" />
            </div>
            <EventList :events="team.events" />
            <CreateEventDialog v-if="$page.props.auth.user.role === 'coach'" :team="team" />
            <MessageList :messages="team.messages" />
            <CreateMessageDialog v-if="$page.props.auth.user.role === 'coach'" :team="team" />
        </div>
    </AppLayout>
</template>
