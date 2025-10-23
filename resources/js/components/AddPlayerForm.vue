<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Team } from '@/types/Team';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import InputError from './InputError.vue';

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
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast('Player has been successfully added to the team');
        },
    });
};
</script>

<template>
    <div class="mt-8 flex flex-col gap-4">
        <h2 class="text-xl font-semibold">Add a New Player</h2>
        <form @submit.prevent="addPlayer" class="space-y-4">
            <div class="grid gap-2">
                <Label for="name">Player Name</Label>
                <Input v-model="form.name" type="text" />
                <InputError :message="form.errors.name" />
            </div>
            <div class="grid gap-2">
                <Label for="guardian_name">Parent/Guardian Name</Label>
                <Input v-model="form.guardian_name" type="text" />
                <InputError :message="form.errors.guardian_name" />
            </div>
            <div class="grid gap-2">
                <Label for="guardian_email">Parent/Guardian Email</Label>
                <Input v-model="form.guardian_email" type="email" />
                <InputError :message="form.errors.guardian_email" />
            </div>
            <div class="grid gap-2">
                <Label for="guardian_phone">Parent/Guardian Phone Number</Label>
                <Input v-model="form.guardian_phone" type="text" />
                <InputError :message="form.errors.guardian_phone" />
            </div>
            <Button type="submit">Add Player</Button>
        </form>
    </div>
</template>
