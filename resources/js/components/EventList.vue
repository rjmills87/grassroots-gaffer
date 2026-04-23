<script setup lang="ts">
import EmptyState from '@/components/EmptyState.vue';
import { capitalizeFirstLetter, formatTimeRange } from '@/helpers';
import { Event } from '@/types/Event';
import { Link } from '@inertiajs/vue3';
import { CalendarDays } from 'lucide-vue-next';

const props = defineProps<{
    events: Event[];
}>();

const formatEventDay = (dateValue: string) => new Date(dateValue).toLocaleDateString('en-GB', { day: '2-digit' });
const formatEventMonth = (dateValue: string) => new Date(dateValue).toLocaleDateString('en-GB', { month: 'short' });
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold">Team Events</h2>
        <div v-if="props.events && props.events.length > 0" class="mt-4">
            <ul class="grid gap-3 sm:grid-cols-2">
                <li v-for="event in props.events" :key="event.id">
                    <Link :href="route('event.show', event.id)">
                        <div class="rounded-lg border bg-card p-4 shadow-sm transition-colors hover:bg-accent/30">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex min-w-14 shrink-0 flex-col items-center rounded-md border bg-muted/40 px-2 py-2">
                                    <span class="text-lg leading-none font-bold">{{ formatEventDay(event.starts_at) }}</span>
                                    <span class="mt-1 text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                                        {{ formatEventMonth(event.starts_at) }}
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 flex-col items-start gap-1">
                                    <span class="rounded-full border px-2 py-0.5 text-xs font-semibold">
                                        {{ capitalizeFirstLetter(event.type) }}
                                    </span>
                                    <p class="text-sm font-medium text-muted-foreground">{{ formatTimeRange(event.starts_at, event.ends_at) }}</p>
                                </div>
                            </div>
                            <p class="mt-3 text-sm font-medium text-muted-foreground">{{ event.location }}</p>
                            <p class="mt-1 line-clamp-2 text-sm text-muted-foreground">
                                {{ event.details || 'No additional details provided.' }}
                            </p>
                            <div class="mt-4 flex items-center gap-2 text-xs">
                                <span class="rounded-full border border-emerald-300 bg-emerald-50 px-2 py-1 text-emerald-700">
                                    {{ event.attending_count }} attending
                                </span>
                                <span class="rounded-full border border-red-300 bg-red-50 px-2 py-1 text-red-700">
                                    {{ event.unavailable_count }} unavailable
                                </span>
                            </div>
                        </div>
                    </Link>
                </li>
            </ul>
        </div>
        <div v-else class="mt-4">
            <EmptyState title="No events scheduled yet" description="Create your first event to start tracking attendance.">
                <template #icon>
                    <CalendarDays class="h-5 w-5" />
                </template>
            </EmptyState>
        </div>
    </div>
</template>
