<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import AddPlayerForm from '@/components/AddPlayerForm.vue';
import CreateEventForm from '@/components/CreateEventForm.vue';
import CreateMessageForm from '@/components/CreateMessageForm.vue';
import DeleteTeam from '@/components/DeleteTeam.vue';
import EventList from '@/components/EventList.vue';
import MessageList from '@/components/MessageList.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { Team } from '@/types/Team';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    team: Team;
}>();

const isAddPlayerOpen = ref(false);
const isCreateEventOpen = ref(false);
const isCreateMessageOpen = ref(false);
const isDeleteTeamOpen = ref(false);
</script>

<template>
    <Head :title="team.name" />
    <AppLayout>
        <div class="flex flex-col gap-4 p-4">
            <h1>{{ team.name }}</h1>
            <div class="mt-8">
                <h2 class="text-xl font-semibold">Squad List</h2>
                <div v-if="props.team.players && props.team.players.length > 0" class="mt-4">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="player in props.team.players" :key="player.id" class="py-2">
                            {{ player.name }}
                        </li>
                    </ul>
                </div>
                <div v-else class="mt-4">
                    <p>No players have been added to this team yet.</p>
                </div>
            </div>
            <EventList :events="team.events" />
            <MessageList :messages="team.messages" />

            <div v-if="$page.props.auth.user.role === 'coach'" class="mt-8">
                <Dialog v-model:open="isAddPlayerOpen">
                    <DialogTrigger as-child><Button>Add Player</Button></DialogTrigger>
                    <DialogContent>
                        <AddPlayerForm :team="team" @close="isAddPlayerOpen = false" />
                    </DialogContent>
                </Dialog>
            </div>
            <div v-if="$page.props.auth.user.role === 'coach'" class="mt-8">
                <Dialog v-model:open="isCreateEventOpen">
                    <DialogTrigger as-child><Button>Create Event</Button></DialogTrigger>
                    <DialogContent>
                        <CreateEventForm :team="team" @close="isCreateEventOpen = false" />
                    </DialogContent>
                </Dialog>
            </div>
            <div v-if="$page.props.auth.user.role === 'coach'" class="mt-8">
                <Dialog v-model:open="isCreateMessageOpen">
                    <DialogTrigger as-child><Button>Create Message</Button></DialogTrigger>
                    <DialogContent>
                        <CreateMessageForm :team="team" @close="isCreateMessageOpen = false" />
                    </DialogContent>
                </Dialog>
            </div>
            <div v-if="$page.props.auth.user.role === 'coach'" class="mt-8">
                <Dialog v-model:open="isDeleteTeamOpen">
                    <DialogTrigger><Button variant="destructive">Delete Team</Button></DialogTrigger>
                    <DialogContent>
                        <DeleteTeam :team="team" @close="isDeleteTeamOpen = false" />
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    </AppLayout>
</template>
