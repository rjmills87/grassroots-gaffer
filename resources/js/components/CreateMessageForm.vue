<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Team } from '@/types/Team';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import InputError from './InputError.vue';

const props = defineProps<{
    team: Team;
}>();

const form = useForm({
    message: '',
});

const submit = () => {
    form.post(route('teams.messages.store', props.team.id), {
        onSuccess: () => {
            form.reset();
            toast('Your message has been created successfully');
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="mt-4 space-y-4">
        <div class="grid gap-2">
            <h3 class="text-lg font-semibold">Send a Message</h3>
            <p class="text-sm text-gray-500">Your message will be sent to all players and guardians on the team.</p>
        </div>
        <div class="grid gap-2">
            <Textarea v-model="form.message" placeholder="Type your message here..." />
            <InputError :message="form.errors.message" />
        </div>
        <Button type="submit" :disabled="form.processing">Send Message</Button>
    </form>
</template>
