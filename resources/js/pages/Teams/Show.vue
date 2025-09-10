<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import AddPlayerForm from '@/components/AddPlayerForm.vue';
import CreateEventForm from '@/components/CreateEventForm.vue';
import CreateMessageForm from '@/components/CreateMessageForm.vue';
import EventList from '@/components/EventList.vue';
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
            <div class="mt-8">
                <AddPlayerForm :team="team" />
            </div>
            <div class="mt-8">
                <CreateEventForm :team="team" />
            </div>
            <div class="mt-8">
                <CreateMessageForm :team="team" />
            </div>
        </div>
    </AppLayout>
</template>
