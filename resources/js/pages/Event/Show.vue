<script setup lang="ts">
import DeleteConfirmationDialog from '@/components/DeleteConfirmationDialog.vue';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { capitalizeFirstLetter, formatDate, formatTimeRange } from '@/helpers';
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { Event, type EventAttachment } from '@/types/Event';
import { Player } from '@/types/Player';
import { Form, router, useForm } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { DateFormatter, getLocalTimeZone, parseDate } from '@internationalized/date';
import { CalendarDays, File as FileIcon, FileText, Image, LoaderCircle, Mail, MapPin } from 'lucide-vue-next';
import { computed, ref, type Component } from 'vue';
import Calendar from '@/components/ui/calendar/Calendar.vue';

const props = defineProps<{
    event: Event;
    user: User;
}>();

const availabilityLoadingPlayerId = ref<number | null>(null);
const reminderProcessing = ref(false);
const isEditDialogOpen = ref(false);
const attachmentsToRemove = ref<number[]>([]);
const selectedAttachment = ref<EventAttachment | null>(null);
const isAttachmentPreviewOpen = ref(false);

const attendingCount = computed(() => props.event.players.filter((player) => player.pivot?.player_response === 'attending').length);
const unavailableCount = computed(() => props.event.players.filter((player) => player.pivot?.player_response === 'unavailable').length);
const noResponseCount = computed(() => props.event.players.filter((player) => !player.pivot?.player_response).length);
const isPastEvent = computed(() => new Date(props.event.starts_at) <= new Date());
const canManageEvent = computed(() => props.user.role === 'coach' && !isPastEvent.value);

const editForm = useForm({
    type: props.event.type,
    starts_at: null as string | null,
    ends_at: null as string | null,
    location: props.event.location,
    details: props.event.details ?? '',
    attachments: [] as File[],
    removed_attachment_ids: [] as number[],
    _method: 'patch',
});
const editDateValue = ref<DateValue | undefined>();
const editStartsAtTime = ref('18:00');
const editEndsAtTime = ref('19:30');
const dateFormat = new DateFormatter('en-GB', { dateStyle: 'long' });

const syncEditFormFromEvent = () => {
    const startsAtDate = new Date(props.event.starts_at);
    const endsAtDate = new Date(props.event.ends_at);
    editDateValue.value = parseDate(startsAtDate.toISOString().slice(0, 10));
    editStartsAtTime.value = startsAtDate.toTimeString().slice(0, 5);
    editEndsAtTime.value = endsAtDate.toTimeString().slice(0, 5);
    editForm.type = props.event.type;
    editForm.location = props.event.location;
    editForm.details = props.event.details ?? '';
    editForm.attachments = [];
    editForm.removed_attachment_ids = [];
    syncEditDateTimes();
    attachmentsToRemove.value = [];
};

const openEditDialog = () => {
    syncEditFormFromEvent();
    isEditDialogOpen.value = true;
};

const updateEvent = () => {
    editForm.removed_attachment_ids = attachmentsToRemove.value;
    syncEditDateTimes();
    editForm.post(route('events.manage.update', props.event.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            isEditDialogOpen.value = false;
        },
    });
};
const syncEditDateTimes = () => {
    if (!editDateValue.value || !editStartsAtTime.value || !editEndsAtTime.value) {
        editForm.starts_at = null;
        editForm.ends_at = null;
        return;
    }

    const startsAtLocalDate = editDateValue.value.toDate(getLocalTimeZone());
    const [startHours, startMinutes] = editStartsAtTime.value.split(':').map(Number);
    startsAtLocalDate.setHours(startHours, startMinutes, 0, 0);
    editForm.starts_at = startsAtLocalDate.toISOString();

    const endsAtLocalDate = editDateValue.value.toDate(getLocalTimeZone());
    const [endHours, endMinutes] = editEndsAtTime.value.split(':').map(Number);
    endsAtLocalDate.setHours(endHours, endMinutes, 0, 0);
    editForm.ends_at = endsAtLocalDate.toISOString();
};

const formatAttachmentSize = (size: number) => {
    if (size < 1024) {
        return `${size} B`;
    }

    if (size < 1024 * 1024) {
        return `${(size / 1024).toFixed(1)} KB`;
    }

    return `${(size / (1024 * 1024)).toFixed(1)} MB`;
};

const isPreviewableImage = (attachment: EventAttachment) => attachment.mime_type.startsWith('image/');
const isPreviewablePdf = (attachment: EventAttachment) => attachment.mime_type === 'application/pdf';

const attachmentListIcon = (attachment: EventAttachment): Component => {
    if (attachment.mime_type === 'application/pdf') {
        return FileText;
    }

    if (attachment.mime_type.startsWith('image/')) {
        return Image;
    }

    return FileIcon;
};

const attachmentIconClass = (attachment: EventAttachment): string => {
    if (attachment.mime_type === 'application/pdf') {
        return 'mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-400';
    }

    if (attachment.mime_type.startsWith('image/')) {
        return 'mt-0.5 h-5 w-5 shrink-0 text-sky-600 dark:text-sky-400';
    }

    return 'mt-0.5 h-5 w-5 shrink-0 text-muted-foreground';
};

const openAttachmentPreview = (attachment: EventAttachment) => {
    selectedAttachment.value = attachment;
    isAttachmentPreviewOpen.value = true;
};

const toggleAttachmentRemoval = (attachmentId: number) => {
    if (attachmentsToRemove.value.includes(attachmentId)) {
        attachmentsToRemove.value = attachmentsToRemove.value.filter((id) => id !== attachmentId);
        return;
    }

    attachmentsToRemove.value.push(attachmentId);
};

const onEditAttachmentChange = (inputEvent: globalThis.Event) => {
    const files = (inputEvent.target as HTMLInputElement).files;
    editForm.attachments = files ? Array.from(files) : [];
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
                <div v-if="props.event.attachments?.length" class="mt-5 space-y-2 border-t pt-4">
                    <h2 class="text-sm font-semibold">Attachments</h2>
                    <div class="space-y-2">
                        <div
                            v-for="attachment in props.event.attachments"
                            :key="attachment.id"
                            class="flex flex-wrap items-center justify-between gap-2 rounded-lg border p-3"
                        >
                            <div class="flex min-w-0 flex-1 items-start gap-3">
                                <component :is="attachmentListIcon(attachment)" :class="attachmentIconClass(attachment)" aria-hidden="true" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ attachment.original_name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ formatAttachmentSize(attachment.size) }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <Button class="cursor-pointer" variant="outline" size="sm" @click="openAttachmentPreview(attachment)">View</Button>
                                <Button class="cursor-pointer" variant="outline" size="sm" as-child>
                                    <a :href="route('events.attachments.download', attachment.id)">Download</a>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
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
                                        <Popover>
                                            <PopoverTrigger as-child>
                                                <Button variant="outline" class="w-full justify-between text-left font-normal">
                                                    <span>{{ editDateValue ? dateFormat.format(editDateValue.toDate(getLocalTimeZone())) : 'Select a Date' }}</span>
                                                    <CalendarDays class="h-4 w-4" />
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent><Calendar v-model:model-value="editDateValue" :weekday-format="'short'" /></PopoverContent>
                                        </Popover>
                                        <p v-if="editForm.errors.starts_at" class="text-sm text-red-500">{{ editForm.errors.starts_at }}</p>
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="edit-event-start-time">Event Start Time</Label>
                                        <Input id="edit-event-start-time" v-model="editStartsAtTime" type="time" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="edit-event-end-time">Event Finish Time</Label>
                                        <Input id="edit-event-end-time" v-model="editEndsAtTime" type="time" />
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
                                    <div class="grid gap-2">
                                        <Label for="edit-event-attachments">Add Attachments</Label>
                                        <Input
                                            id="edit-event-attachments"
                                            type="file"
                                            multiple
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            @change="onEditAttachmentChange"
                                        />
                                        <p class="text-xs text-muted-foreground">Up to 3 files total. PDF, JPG, JPEG, PNG only. 10MB each max.</p>
                                        <p v-if="editForm.attachments.length > 0" class="text-xs text-muted-foreground">
                                            {{ editForm.attachments.length }} new file(s) selected
                                        </p>
                                        <p v-if="editForm.errors.attachments" class="text-sm text-red-500">{{ editForm.errors.attachments }}</p>
                                        <p v-if="editForm.errors['attachments.0']" class="text-sm text-red-500">{{ editForm.errors['attachments.0'] }}</p>
                                    </div>
                                    <div v-if="props.event.attachments?.length" class="grid gap-2">
                                        <Label>Existing Attachments</Label>
                                        <div class="space-y-2">
                                            <div
                                                v-for="attachment in props.event.attachments"
                                                :key="`edit-attachment-${attachment.id}`"
                                                class="flex items-center justify-between rounded-lg border p-2"
                                            >
                                                <div class="flex min-w-0 flex-1 items-start gap-2">
                                                    <component :is="attachmentListIcon(attachment)" :class="attachmentIconClass(attachment)" aria-hidden="true" />
                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-sm font-medium">{{ attachment.original_name }}</p>
                                                        <p class="text-xs text-muted-foreground">{{ formatAttachmentSize(attachment.size) }}</p>
                                                    </div>
                                                </div>
                                                <Button
                                                    class="cursor-pointer"
                                                    :variant="attachmentsToRemove.includes(attachment.id) ? 'default' : 'outline'"
                                                    size="sm"
                                                    @click.prevent="toggleAttachmentRemoval(attachment.id)"
                                                >
                                                    {{ attachmentsToRemove.includes(attachment.id) ? 'Undo Remove' : 'Remove' }}
                                                </Button>
                                            </div>
                                        </div>
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

    <Dialog v-model:open="isAttachmentPreviewOpen">
        <DialogContent>
            <div v-if="selectedAttachment" class="space-y-4">
                <h2 class="text-lg font-semibold">{{ selectedAttachment.original_name }}</h2>
                <div class="max-h-[70vh] overflow-auto rounded-md border p-2">
                    <img
                        v-if="isPreviewableImage(selectedAttachment)"
                        :src="route('events.attachments.preview', selectedAttachment.id)"
                        :alt="selectedAttachment.original_name"
                        class="mx-auto max-h-[60vh] rounded-md object-contain"
                    />
                    <iframe
                        v-else-if="isPreviewablePdf(selectedAttachment)"
                        :src="route('events.attachments.preview', selectedAttachment.id)"
                        class="h-[60vh] w-full rounded-md"
                        title="Attachment preview"
                    />
                    <p v-else class="text-sm text-muted-foreground">Preview not available for this file type. Please download to view.</p>
                </div>
                <div class="flex justify-end">
                    <Button class="cursor-pointer" as-child>
                        <a :href="route('events.attachments.download', selectedAttachment.id)">Download</a>
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
