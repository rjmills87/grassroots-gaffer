<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { capitalizeFirstLetter, formatDate } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { Event } from '@/types/Event';
import { Player } from '@/types/Player';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    event: Event;
}>();

const setAvailability = (player: Player, response: string) => {
    const form = useForm({
        player_response: response,
    });

    form.post(`/events/${props.event.id}/players/${player.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <h1>{{ capitalizeFirstLetter(props.event.type) }}</h1>
            <p>{{ formatDate(props.event.occurs_at) }}</p>
            <p>{{ props.event.location }}</p>
            <p>{{ props.event.details }}</p>
            <div v-if="props.event.players && props.event.players.length > 0" class="mt-4">
                <ul class="divide-y divide-gray-200">
                    <li v-for="player in props.event.players" :key="player.id" class="flex items-center justify-between py-2">
                        {{ player.name }}
                        <div class="flex gap-4">
                            <Button
                                @click="setAvailability(player, 'attending')"
                                class="cursor-pointer"
                                :class="{ 'bg-green-700': player.pivot?.player_response === 'attending' }"
                                variant="default"
                                >Attending</Button
                            >
                            <Button
                                @click="setAvailability(player, 'unavailable')"
                                class="cursor-pointer"
                                :class="{ 'bg-red-700': player.pivot?.player_response === 'unavailable' }"
                                variant="destructive"
                                >Unavailable</Button
                            >
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
