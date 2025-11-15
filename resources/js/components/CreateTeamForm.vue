<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Form, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const form = useForm({
    'team-name': '',
    'age-group': null,
    'club-badge': null,
});

const emit = defineEmits<{
    close: [];
}>();

const createTeam = () => {
    form.post('/teams', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast('Your team has been created successfully');
            emit('close');
        },
    });
};

const teamBadgePreview = ref('');

const handleTeamBadgeChange = (event: any) => {
    const fileInput = (form['club-badge'] = event.target.files[0]);
    const tempUrl = URL.createObjectURL(fileInput);
    teamBadgePreview.value = tempUrl;
};
</script>
<template>
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <h1 class="text-xl font-bold">Create a New Team</h1>
        <Form @submit.prevent="createTeam" class="flex flex-col gap-4">
            <div class="grid gap-2">
                <Label for="team-name">Team Name</Label>
                <Input v-model="form['team-name']" type="text" name="team-name" placeholder="Enter Your Team Name" />
                <InputError :message="form.errors['team-name']" />
            </div>
            <div class="grid gap-2">
                <Label for="age-group">Age Group</Label>
                <Select v-model="form['age-group']" name="age-group">
                    <SelectTrigger>
                        <SelectValue placeholder="Select Your Teams Age Group" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="under-7s"> Under 7's </SelectItem>
                            <SelectItem value="under-8s"> Under 8's </SelectItem>
                            <SelectItem value="under-9s"> Under 9's </SelectItem>
                            <SelectItem value="under-10s"> Under 10's </SelectItem>
                            <SelectItem value="under-11s"> Under 11's </SelectItem>
                            <SelectItem value="under-12s"> Under 12's </SelectItem>
                            <SelectItem value="under-13s"> Under 13's </SelectItem>
                            <SelectItem value="under-14s"> Under 14's </SelectItem>
                            <SelectItem value="under-15s"> Under 15's </SelectItem>
                            <SelectItem value="under-16s"> Under 16's </SelectItem>
                            <SelectItem value="under-17s"> Under 17's </SelectItem>
                            <SelectItem value="under-18s"> Under 18's </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors['age-group']" />
            </div>
            <div class="grid gap-2">
                <Label for="club-badge">Club Badge <span class="text-gray-400">(can be added later)</span></Label>
                <Input class="cursor-pointer" id="club-badge" type="file" @change="handleTeamBadgeChange" />
                <img class="mt-4 h-20 w-20 rounded-full border-2 border-gray-300" v-if="teamBadgePreview" :src="teamBadgePreview" alt="" />
                <InputError :message="form.errors['club-badge']" />
            </div>
            <Button type="submit">Create Team</Button>
        </Form>
    </div>
</template>
