<script setup lang="ts">
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Team } from '@/types/Team';
import { Form, useForm } from '@inertiajs/vue3';
import type { DateValue } from '@internationalized/date';
import { DateFormatter, getLocalTimeZone, today } from '@internationalized/date';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import type { Ref } from 'vue';
import { ref, watch } from 'vue';
import Button from './ui/button/Button.vue';
import Calendar from './ui/calendar/Calendar.vue';
import Input from './ui/input/Input.vue';
import Label from './ui/label/Label.vue';
import Textarea from './ui/textarea/Textarea.vue';

const props = defineProps<{
    team: Team;
}>();

const value = ref(today(getLocalTimeZone())) as Ref<DateValue>;

const addEvent = () => {
    form.post(`/teams/${props.team.id}/events`, {
        onSuccess: () => form.reset(),
    });
};

const form = useForm({
    type: null,
    occurs_at: '',
    location: '',
    details: '',
});

watch(value, (newValue) => {
    if (newValue) {
        form.occurs_at = newValue.toDate(getLocalTimeZone()).toISOString();
    }
});

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
        <Form @submit.prevent="addEvent">
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
            <Label for="occurs_at">Event Date</Label>
            <Popover>
                <PopoverTrigger
                    ><Button variant="outline"
                        ><span>{{ value ? dateFormat.format(toDate(value)) : 'Select a Date' }}</span
                        ><CalendarIcon class="mr-2 h-4 w-4"
                    /></Button>
                </PopoverTrigger>
                <PopoverContent><Calendar v-model:model-value="value" :weekday-format="'short'" /></PopoverContent>
            </Popover>
            <Label for="location">Event Location</Label>
            <Input v-model="form.location" type="text" />
            <Label for="details">Event Details</Label>
            <Textarea id="details" v-model="form.details" rows="4" />
            <Button class="mt-4 cursor-pointer" type="submit">Add Event</Button>
        </Form>
    </div>
</template>
