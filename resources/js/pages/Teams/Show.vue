<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import AddOrEditPlayerDialog from '@/components/AddOrEditPlayerDialog.vue';
import CreateEventDialog from '@/components/CreateEventDialog.vue';
import CreateMessageDialog from '@/components/CreateMessageDialog.vue';
import DeleteConfirmationDialog from '@/components/DeleteConfirmationDialog.vue';
import EventList from '@/components/EventList.vue';
import MessageList from '@/components/MessageList.vue';
import { Button } from '@/components/ui/button';
import Label from '@/components/ui/label/Label.vue';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Player } from '@/types/Player';
import { Team } from '@/types/Team';
import { Head } from '@inertiajs/vue3';

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
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-1/5 font-bold">Name</TableHead>
                                <TableHead class="w-1/5 font-bold">Squad Number</TableHead>
                                <TableHead class="w-1/5 font-bold">Position</TableHead>
                                <TableHead class="w-1/5 font-bold">Guardian Details</TableHead>
                                <TableHead class="w-1/5 font-bold">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="player in props.team.players" :key="player.id">
                                <TableCell class="w-1/4">{{ player.name }}</TableCell>
                                <TableCell class="w-1/4 font-bold">{{ player.squad_number }}</TableCell>
                                <TableCell class="w-1/4 font-bold uppercase">{{ player.position }}</TableCell>
                                <TableCell class="w-1/4"
                                    ><Sheet>
                                        <SheetTrigger asChild>
                                            <Button variant="default">View Details</Button>
                                        </SheetTrigger>
                                        <SheetContent class="max-w-[400px]">
                                            <SheetHeader class="p-4">
                                                <SheetTitle class="flex flex-col">
                                                    <span class="font-bold">Guardian Details</span>
                                                    <span class="text-xl font-semibold">{{ player.name }}</span>
                                                </SheetTitle>
                                            </SheetHeader>
                                            <SheetDescription class="space-y-2 p-4">
                                                <div class="flex flex-col gap-2">
                                                    <Label class="font-bold">Name</Label>
                                                    <p>{{ player.guardian_name }}</p>
                                                    <Label class="font-bold">Email</Label>
                                                    <p>{{ player.guardian_email }}</p>
                                                    <Label class="font-bold">Phone</Label>
                                                    <p>{{ player.guardian_phone }}</p>
                                                </div>
                                            </SheetDescription>
                                        </SheetContent>
                                    </Sheet></TableCell
                                >
                                <TableCell class="m-0 flex items-center gap-4"
                                    ><AddOrEditPlayerDialog :team="team" :player="player" />
                                    <DeleteConfirmationDialog
                                        itemType="Player"
                                        :itemName="player.name"
                                        deleteRoute="players.destroy"
                                        :itemId="player.id"
                                        :toastMessage="`Player: ${player.name} has been deleted successfully`"
                                /></TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <div v-else class="mt-4">
                    <p>No players have been added to this team yet.</p>
                </div>
                <AddOrEditPlayerDialog class="pr-4" :team="team" />
            </div>
            <EventList :events="team.events" />
            <CreateEventDialog :team="team" />
            <MessageList :messages="team.messages" />
            <CreateMessageDialog :team="team" />
            <div class="mt-8 mr-4 mb-4 flex justify-end">
                <DeleteConfirmationDialog
                    itemType="Team"
                    :itemName="team.name"
                    deleteRoute="teams.destroy"
                    :itemId="team.id"
                    :toastMessage="`Team: ${team.name} has been deleted successfully`"
                />
            </div>
        </div>
    </AppLayout>
</template>
