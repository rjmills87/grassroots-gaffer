<script setup lang="ts">
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Team } from '@/types/Team';
import { Form, useForm } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { DateFormatter, getLocalTimeZone } from '@internationalized/date';
import { Calendar as CalendarIcon, LoaderCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import InputError from './InputError.vue';
import Button from './ui/button/Button.vue';
import Calendar from './ui/calendar/Calendar.vue';
import Input from './ui/input/Input.vue';
import Label from './ui/label/Label.vue';
import Textarea from './ui/textarea/Textarea.vue';

// Define the shape of the form data
interface EventForm {
    type: string | null;
    starts_at: string | null;
    ends_at: string | null;
    location: string;
    details: string;
}

const props = defineProps<{
    team: Team;
}>();

const emit = defineEmits<{
    close: [];
}>();

const value = ref<DateValue | undefined>();
const startsAtTime = ref('18:00');
const endsAtTime = ref('19:30');

// Apply the interface to useForm and set initial values
const form = useForm<EventForm>({
    type: null,
    starts_at: null,
    ends_at: null,
    location: '',
    details: '',
});

const addEvent = () => {
    form.post(`/teams/${props.team.id}/events`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            value.value = undefined;
            startsAtTime.value = '18:00';
            endsAtTime.value = '19:30';
            toast('The event has been created successfully');
            emit('close');
        },
    });
};

const updateDateTimes = () => {
    if (!value.value || !startsAtTime.value || !endsAtTime.value) {
        form.starts_at = null;
        form.ends_at = null;
        return;
    }

    const startsAtLocalDate = value.value.toDate(getLocalTimeZone());
    const [startHours, startMinutes] = startsAtTime.value.split(':').map(Number);
    startsAtLocalDate.setHours(startHours, startMinutes, 0, 0);
    form.starts_at = startsAtLocalDate.toISOString();

    const endsAtLocalDate = value.value.toDate(getLocalTimeZone());
    const [endHours, endMinutes] = endsAtTime.value.split(':').map(Number);
    endsAtLocalDate.setHours(endHours, endMinutes, 0, 0);
    form.ends_at = endsAtLocalDate.toISOString();
};

watch(value, updateDateTimes);
watch(startsAtTime, updateDateTimes);
watch(endsAtTime, updateDateTimes);

const dateFormat = new DateFormatter('en-GB', {
    dateStyle: 'long',
});

const toDate = (date: DateValue) => {
    return date.toDate(getLocalTimeZone());
};
</script>

<template>
    <div class="mt-8 flex flex-col gap-4">
        <h2 class="text-xl font-semibold">Add a New Event</h2>
        <Form @submit.prevent="addEvent" class="flex flex-col gap-4">
            <div class="grid gap-2">
                <Label for="type" class="text-sm font-medium">Event Type</Label>
                <Select v-model="form['type']" name="type">
                    <SelectTrigger>
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
                <InputError :message="form.errors.type" />
            </div>
            <div class="grid gap-2">
                <Label for="location">Event Location</Label>
                <Input v-model="form.location" type="text" />
                <InputError :message="form.errors.location" />
            </div>
            <div class="grid gap-2">
                <Label for="details">Event Details</Label>
                <Textarea id="details" v-model="form.details" rows="4" />
                <InputError :message="form.errors.details" />
            </div>
            <div class="grid gap-2">
                <Label for="starts_at">Event Date</Label>
                <Popover>
                    <PopoverTrigger as-child>
                        <Button variant="outline" class="w-full justify-between text-left font-normal">
                            <span>{{ value ? dateFormat.format(toDate(value)) : 'Select a Date' }}</span>
                            <CalendarIcon class="h-4 w-4" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent><Calendar v-model:model-value="value" :weekday-format="'short'" /></PopoverContent>
                </Popover>
                <InputError :message="form.errors.starts_at" />
            </div>
            <div class="grid gap-2">
                <Label for="starts_at_time">Event Start Time</Label>
                <Input id="starts_at_time" v-model="startsAtTime" type="time" />
            </div>
            <div class="grid gap-2">
                <Label for="ends_at_time">Event Finish Time</Label>
                <Input id="ends_at_time" v-model="endsAtTime" type="time" />
                <InputError :message="form.errors.ends_at" />
            </div>
            <Button class="mt-4 cursor-pointer" type="submit" :disabled="form.processing">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ form.processing ? 'Adding...' : 'Add Event' }}
            </Button>
        </Form>
    </div>
</template>
