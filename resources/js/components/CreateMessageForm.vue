<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Team } from '@/types/Team';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    team: Team;
}>();

const form = useForm({
    message: '',
});

const submit = () => {
    form.post(route('teams.messages.store', props.team.id), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="mt-4 space-y-4">
        <div>
            <h3 class="text-lg font-semibold">Send a Message</h3>
            <p class="text-sm text-gray-500">Your message will be sent to all players and guardians on the team.</p>
        </div>
        <Textarea v-model="form.message" placeholder="Type your message here..." />
        <Button type="submit" :disabled="form.processing">Send Message</Button>
    </form>
</template>
