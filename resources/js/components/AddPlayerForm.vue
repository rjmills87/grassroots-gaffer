<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Player } from '@/types/Player';
import { Team } from '@/types/Team';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import InputError from './InputError.vue';

const props = defineProps<{
    team: Team;
    player?: Player;
}>();

const emit = defineEmits<{
    close: [];
}>();

const form = useForm({
    name: props.player ? props.player.name : '',
    guardian_name: props.player ? props.player.guardian_name : '',
    guardian_email: props.player ? props.player.guardian_email : '',
    guardian_phone: props.player ? props.player.guardian_phone : '',
    squad_number: props.player ? props.player.squad_number : '',
    position: props.player ? props.player.position : '',
});

const submitForm = () => {
    if (props.player) {
        form.patch(`/players/${props.team.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('close');
                toast('Your Player edits have been saved successfully');
            },
        });
    } else {
        form.post(`/teams/${props.team.id}/players`, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                emit('close');
                toast('Player has been successfully added to the team');
            },
        });
    }
};
</script>

<template>
    <div class="mt-8 flex w-full flex-col items-center justify-center gap-4">
        <h2 class="text-xl font-semibold">{{ props.player ? 'Edit Player' : 'Add a New Player' }}</h2>
        <form @submit.prevent="submitForm" class="w-full space-y-4">
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
            <div class="grid gap-2">
                <Label for="squad_number">Squad Number (1-99)</Label>
                <Input v-model="form.squad_number" type="number" />
                <InputError :message="form.errors.squad_number" />
            </div>
            <div class="grid gap-2">
                <Label for="position">Position</Label>
                <Select v-model="form['position']" name="position">
                    <SelectTrigger>
                        <SelectValue placeholder="Select Players Preferred Position" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="gk"> Goalkeeper </SelectItem>
                            <SelectItem value="cb"> Center Back </SelectItem>
                            <SelectItem value="rb"> Right Back </SelectItem>
                            <SelectItem value="lb"> Left Back </SelectItem>
                            <SelectItem value="rwb"> Right Wing Back </SelectItem>
                            <SelectItem value="lwb"> Left Wing Back </SelectItem>
                            <SelectItem value="cm"> Center Midfield </SelectItem>
                            <SelectItem value="cdm"> Center Defensive Midfield </SelectItem>
                            <SelectItem value="amf"> Attacking Midfield </SelectItem>
                            <SelectItem value="rm"> Right Midfield </SelectItem>
                            <SelectItem value="lm"> Left Midfield </SelectItem>
                            <SelectItem value="lwf"> Left Wing Forward </SelectItem>
                            <SelectItem value="rwf"> Right Wing Forward </SelectItem>
                            <SelectItem value="cf"> Center Forward </SelectItem>
                            <SelectItem value="st"> Striker </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.position" />
            </div>
            <Button type="submit">{{ props.player ? 'Save Edits' : 'Add Player' }}</Button>
        </form>
    </div>
</template>
