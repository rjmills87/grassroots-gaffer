<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { Team } from '@/types/Team';
import { ref } from 'vue';
import CreateEventForm from './CreateEventForm.vue';
const props = defineProps<{
    team: Team;
}>();

const isCreateEventOpen = ref(false);
</script>
<template>
    <div v-if="$page.props.auth.user.role === 'coach'" class="mt-8 mb-4 flex justify-end">
        <Dialog v-model:open="isCreateEventOpen">
            <DialogTrigger as-child><Button>Create Event</Button></DialogTrigger>
            <DialogContent>
                <CreateEventForm :team="team" @close="isCreateEventOpen = false" />
            </DialogContent>
        </Dialog>
    </div>
</template>
