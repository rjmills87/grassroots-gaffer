<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import AddPlayerDialog from '@/components/AddPlayerDialog.vue';
import CreateEventDialog from '@/components/CreateEventDialog.vue';
import CreateMessageDialog from '@/components/CreateMessageDialog.vue';
import DeleteTeamDialog from '@/components/DeleteTeamDialog.vue';
import EventList from '@/components/EventList.vue';
import MessageList from '@/components/MessageList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Team } from '@/types/Team';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    team: Team;
}>();
</script>

<template>
    <Head :title="team.name" />
    <AppLayout>
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center gap-4" v-if="team.team_badge_url">
                <img :src="`/storage/${team.team_badge_url}`" alt="Team Badge" class="h-20 w-20 rounded-full" />
                <h1 class="text-2xl font-semibold">{{ team.name }}</h1>
            </div>
            <div v-else>
                <h1 class="text-2xl font-semibold">{{ team.name }}</h1>
            </div>
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
                <AddPlayerDialog :team="team" />
            </div>
            <EventList :events="team.events" />
            <CreateEventDialog :team="team" />
            <MessageList :messages="team.messages" />
            <CreateMessageDialog :team="team" />
            <DeleteTeamDialog :team="team" />
        </div>
    </AppLayout>
</template>
