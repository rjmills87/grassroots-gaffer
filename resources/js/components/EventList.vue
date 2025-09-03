<script setup lang="ts">
import { Event } from '@/types/Event';

const props = defineProps<{
    events: Event[];
}>();
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold">Event List</h2>
        <div v-if="props.events && props.events.length > 0" class="mt-4">
            <ul class="divide-y divide-gray-200">
                <li v-for="event in props.events" :key="event.id" class="py-2">
                    <div class="flex flex-col">
                        <span>{{ event.type }}</span>
                        <span>{{ event.occurs_at }}</span>
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
