<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Team } from '@/types/Team';
import { Form, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    team: Team;
}>();

const form = useForm({
    name: '',
    guardian_name: '',
    guardian_email: '',
    guardian_phone: '',
});

const addPlayer = () => {
    form.post(`/teams/${props.team.id}/players`, {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <div>
        <h2>Add a New Player</h2>
        <Form @submit.prevent="addPlayer">
            <Label for="name">Player Name</Label>
            <Input v-model="form.name" type="text" />
            <Label for="guardian_name">Parent/Guardian Name</Label>
            <Input v-model="form.guardian_name" type="text" />
            <Label for="guardian_email">Parent/Guardian Email</Label>
            <Input v-model="form.guardian_email" type="email" />
            <Label for="guardian_phone">Parent/Guardian Phone Number</Label>
            <Input v-model="form.guardian_phone" type="text" />
            <Button type="submit">Add Player</Button>
        </Form>
    </div>
</template>
