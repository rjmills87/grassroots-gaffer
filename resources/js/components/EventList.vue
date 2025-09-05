<script setup lang="ts">
import { Event } from '@/types/Event';

const props = defineProps<{
    events: Event[];
}>();

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toDateString();
};

const capitalizeFirstLetter = (string: string) => {
    const firstLetterCapitalized = string.charAt(0).toUpperCase() + string.slice(1);
    return firstLetterCapitalized;
};
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold">Event List</h2>
        <div v-if="props.events && props.events.length > 0" class="mt-4">
            <div class="grid grid-cols-3 items-center gap-8 text-sm font-semibold">
                <span>Type & Date</span>
                <span>Location</span>
                <span>Details</span>
            </div>
            <ul class="divide-y divide-gray-200">
                <li v-for="event in props.events" :key="event.id" class="py-2">
                    <div class="grid grid-cols-3 items-center gap-8 text-sm">
                        <div class="flex justify-between">
                            <span>{{ capitalizeFirstLetter(event.type) }}</span> <span>{{ formatDate(event.occurs_at) }}</span>
                        </div>
                        <span>{{ event.location }}</span>
                        <span>{{ event.details }}</span>
                    </div>
                </li>
            </ul>
        </div>
        <div v-else class="mt-4">
            <p>No events have been created to this team yet.</p>
        </div>
    </div>
</template>
