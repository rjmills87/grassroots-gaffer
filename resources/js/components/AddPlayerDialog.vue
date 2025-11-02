<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { Team } from '@/types/Team';
import { ref } from 'vue';
import AddPlayerForm from './AddPlayerForm.vue';

const props = defineProps<{
    team: Team;
}>();

const isAddPlayerOpen = ref(false);
</script>

<template>
    <div v-if="$page.props.auth.user.role === 'coach'" class="mt-8 mr-4 mb-4 flex justify-end">
        <Dialog v-model:open="isAddPlayerOpen">
            <DialogTrigger as-child><Button variant="default" class="cursor-pointer">Add Player</Button></DialogTrigger>
            <DialogContent>
                <AddPlayerForm :team="team" @close="isAddPlayerOpen = false" />
            </DialogContent>
        </Dialog>
    </div>
</template>
