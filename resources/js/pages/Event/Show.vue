<script setup lang="ts">
import DeleteConfirmationDialog from '@/components/DeleteConfirmationDialog.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { capitalizeFirstLetter, formatDate, formatTimeRange } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { Event } from '@/types/Event';
import { Player } from '@/types/Player';
import { Form, router, useForm } from '@inertiajs/vue3';
import { CalendarDays, LoaderCircle, Mail, MapPin } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    event: Event;
    user: User;
}>();

const availabilityLoadingPlayerId = ref<number | null>(null);
const reminderProcessing = ref(false);
const isEditDialogOpen = ref(false);

const attendingCount = computed(() => props.event.players.filter((player) => player.pivot?.player_response === 'attending').length);
const unavailableCount = computed(() => props.event.players.filter((player) => player.pivot?.player_response === 'unavailable').length);
const noResponseCount = computed(() => props.event.players.filter((player) => !player.pivot?.player_response).length);
const isPastEvent = computed(() => new Date(props.event.starts_at) <= new Date());
const canManageEvent = computed(() => props.user.role === 'coach' && !isPastEvent.value);

const editForm = useForm({
    type: props.event.type,
    starts_at: '',
    ends_at: '',
    location: props.event.location,
    details: props.event.details ?? '',
});

const toLocalDateTimeInputValue = (value: string) => {
    const date = new Date(value);
    const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
    return localDate.toISOString().slice(0, 16);
};

const syncEditFormFromEvent = () => {
    editForm.type = props.event.type;
    editForm.starts_at = toLocalDateTimeInputValue(props.event.starts_at);
    editForm.ends_at = toLocalDateTimeInputValue(props.event.ends_at);
    editForm.location = props.event.location;
    editForm.details = props.event.details ?? '';
};

const openEditDialog = () => {
    syncEditFormFromEvent();
    isEditDialogOpen.value = true;
};

const updateEvent = () => {
    editForm.patch(route('events.manage.update', props.event.id), {
        preserveScroll: true,
        onSuccess: () => {
            isEditDialogOpen.value = false;
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Event',
        href: `/events/${props.event.id}`,
    },
];

const statusClasses = (player: Player) => {
    if (player.pivot?.player_response === 'attending') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300';
    }

    if (player.pivot?.player_response === 'unavailable') {
        return 'border-red-200 bg-red-50 text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300';
    }

    return 'border-muted-foreground/20 bg-muted/40 text-muted-foreground';
};

const setAvailability = (player: Player, response: string) => {
    availabilityLoadingPlayerId.value = player.id;

    router.post(
        route('events.players.update', [props.event.id, player.id]),
        {
            player_response: response,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                availabilityLoadingPlayerId.value = null;
            },
        },
    );
};

const sendEventReminder = () => {
    reminderProcessing.value = true;

    router.post(
        `/events/${props.event.id}/send-reminders`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                reminderProcessing.value = false;
            },
        },
    );
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <section class="rounded-xl border bg-card p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-2">
                        <p class="text-xs tracking-wide text-muted-foreground uppercase">Event</p>
                        <h1 class="text-2xl font-semibold">{{ capitalizeFirstLetter(props.event.type) }}</h1>
                        <div class="flex flex-wrap items-center gap-2 text-sm font-medium text-muted-foreground">
                            <CalendarDays class="h-4 w-4" />
                            <span>{{ formatDate(props.event.starts_at) }}</span>
                            <span>•</span>
                            <span>{{ formatTimeRange(props.event.starts_at, props.event.ends_at) }}</span>
                        </div>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm text-muted-foreground">
                        <MapPin class="h-4 w-4" />
                        <span class="font-medium">{{ props.event.location }}</span>
                    </div>
                </div>
                <p class="mt-4 text-sm text-muted-foreground">
                    {{ props.event.details || 'No additional details provided.' }}
                </p>
                <div v-if="canManageEvent" class="mt-5 flex flex-wrap justify-end gap-2 border-t pt-4">
                    <Dialog v-model:open="isEditDialogOpen">
                        <DialogTrigger as-child>
                            <Button class="cursor-pointer" variant="outline" @click="openEditDialog">Edit Event</Button>
                        </DialogTrigger>
                        <DialogContent>
                            <div class="mt-4 flex flex-col gap-4">
                                <h2 class="text-xl font-semibold">Edit Event</h2>
                                <Form @submit.prevent="updateEvent" class="flex flex-col gap-4">
                                    <div class="grid gap-2">
                                        <Label for="edit-event-type" class="text-sm font-medium">Event Type</Label>
                                        <Select v-model="editForm.type" name="type">
                                            <SelectTrigger id="edit-event-type">
                                                <SelectValue placeholder="Select Your Event Type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem value="training"> Training </SelectItem>
                                                    <SelectItem value="match"> Match </SelectItem>
                                                    <SelectItem value="general"> General </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                        <p v-if="editForm.errors.type" class="text-sm text-red-500">{{ editForm.errors.type }}</p>
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="edit-event-start">Event Start</Label>
                                        <Input id="edit-event-start" v-model="editForm.starts_at" type="datetime-local" />
                                        <p v-if="editForm.errors.starts_at" class="text-sm text-red-500">{{ editForm.errors.starts_at }}</p>
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="edit-event-end">Event Finish</Label>
                                        <Input id="edit-event-end" v-model="editForm.ends_at" type="datetime-local" />
                                        <p v-if="editForm.errors.ends_at" class="text-sm text-red-500">{{ editForm.errors.ends_at }}</p>
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="edit-event-location">Event Location</Label>
                                        <Input id="edit-event-location" v-model="editForm.location" type="text" />
                                        <p v-if="editForm.errors.location" class="text-sm text-red-500">{{ editForm.errors.location }}</p>
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="edit-event-details">Event Details</Label>
                                        <Textarea id="edit-event-details" v-model="editForm.details" rows="4" />
                                        <p v-if="editForm.errors.details" class="text-sm text-red-500">{{ editForm.errors.details }}</p>
                                    </div>
                                    <Button class="mt-2 cursor-pointer" type="submit" :disabled="editForm.processing">
                                        <LoaderCircle v-if="editForm.processing" class="h-4 w-4 animate-spin" />
                                        {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                                    </Button>
                                </Form>
                            </div>
                        </DialogContent>
                    </Dialog>
                    <DeleteConfirmationDialog
                        itemType="Event"
                        :itemName="capitalizeFirstLetter(props.event.type)"
                        deleteRoute="events.destroy"
                        :itemId="props.event.id"
                        :toastMessage="`Event has been deleted successfully`"
                    />
                </div>
            </section>

            <section class="rounded-xl border bg-card p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold">Player Availability</h2>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="rounded-full border border-emerald-300 bg-emerald-50 px-2 py-1 text-emerald-700">
                            {{ attendingCount }} attending
                        </span>
                        <span class="rounded-full border border-red-300 bg-red-50 px-2 py-1 text-red-700"> {{ unavailableCount }} unavailable </span>
                        <span class="rounded-full border border-muted-foreground/30 bg-muted px-2 py-1 text-muted-foreground">
                            {{ noResponseCount }} no response
                        </span>
                    </div>
                </div>

                <div v-if="props.event.players && props.event.players.length > 0" class="space-y-3">
                    <div v-for="player in props.event.players" :key="player.id" class="rounded-lg border p-4" :class="statusClasses(player)">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-medium">{{ player.name }}</p>
                                <p class="text-xs capitalize">
                                    {{ player.pivot?.player_response ?? 'no response yet' }}
                                </p>
                                <p v-if="props.user.email === player.guardian_email" class="mt-1 text-xs font-medium text-primary">Your player</p>
                            </div>

                            <div v-if="props.user.email === player.guardian_email" class="flex gap-2">
                                <Button
                                    @click="setAvailability(player, 'attending')"
                                    class="h-11 cursor-pointer px-4"
                                    :disabled="availabilityLoadingPlayerId === player.id"
                                    :class="{ 'bg-emerald-700 text-white': player.pivot?.player_response === 'attending' }"
                                    variant="outline"
                                >
                                    <LoaderCircle v-if="availabilityLoadingPlayerId === player.id" class="h-4 w-4 animate-spin" />
                                    {{ availabilityLoadingPlayerId === player.id ? 'Saving...' : 'Attending' }}
                                </Button>
                                <Button
                                    @click="setAvailability(player, 'unavailable')"
                                    class="h-11 cursor-pointer px-4"
                                    :disabled="availabilityLoadingPlayerId === player.id"
                                    :class="{ 'bg-red-700 text-white': player.pivot?.player_response === 'unavailable' }"
                                    variant="outline"
                                >
                                    <LoaderCircle v-if="availabilityLoadingPlayerId === player.id" class="h-4 w-4 animate-spin" />
                                    {{ availabilityLoadingPlayerId === player.id ? 'Saving...' : 'Unavailable' }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-muted-foreground">No players have been added to this event yet.</p>
            </section>

            <section v-if="props.user.role === 'coach'" class="flex justify-end">
                <Button @click="sendEventReminder" :disabled="reminderProcessing || noResponseCount === 0" class="h-11 cursor-pointer px-4">
                    <LoaderCircle v-if="reminderProcessing" class="h-4 w-4 animate-spin" />
                    <Mail v-else class="h-4 w-4" />
                    {{ reminderProcessing ? 'Sending reminders...' : `Send reminder (${noResponseCount})` }}
                </Button>
            </section>
        </div>
    </AppLayout>
</template>
