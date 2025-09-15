<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    'team-name': '',
    'age-group': null,
});

const createTeam = () => {
    form.post('/teams');
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Create Team',
        href: ' /teams/create',
    },
];
</script>
<template>
    <Head title="Create Team" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <h1 class="text-xl font-bold">Create a New Team</h1>
            <Form @submit.prevent="createTeam">
                <Label for="team-name">Name</Label>
                <Input v-model="form['team-name']" type="text" name="team-name" placeholder="Enter Your Team Name" />
                <InputError :message="form.errors['team-name']" />
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
                <Button type="submit">Create Team</Button>
            </Form>
        </div>
    </AppLayout>
</template>
