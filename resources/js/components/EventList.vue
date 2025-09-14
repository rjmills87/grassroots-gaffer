<script setup lang="ts">
import { capitalizeFirstLetter, formatDate } from '@/helpers';
import { Event } from '@/types/Event';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    events: Event[];
}>();
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold">Upcoming Events</h2>
        <div v-if="props.events && props.events.length > 0" class="mt-4">
            <div class="grid grid-cols-5 items-center gap-8 text-sm font-semibold">
                <span>Type & Date</span>
                <span>Location</span>
                <span>Details</span>
                <span>Attending</span>
                <span>Unavailable</span>
            </div>
            <ul class="divide-y divide-gray-200">
                <li v-for="event in props.events" :key="event.id" class="py-2">
                    <Link :href="route('event.show', event.id)">
                        <div class="grid grid-cols-5 items-center gap-8 text-sm">
                            <div class="flex justify-between">
                                <span>{{ capitalizeFirstLetter(event.type) }}</span> <span>{{ formatDate(event.occurs_at) }}</span>
                            </div>
                            <span>{{ event.location }}</span>
                            <span>{{ event.details }}</span>
                            <span>{{ event.attending_count }}</span>
                            <span>{{ event.unavailable_count }}</span>
                        </div>
                    </Link>
                </li>
            </ul>
        </div>
        <div v-else class="mt-4">
            <p>No events have been created to this team yet.</p>
        </div>
    </div>
</template>
