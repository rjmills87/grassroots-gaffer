<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { Player } from '@/types/Player';
import { Team } from '@/types/Team';
import { ref } from 'vue';
import AddPlayerForm from './AddOrEditPlayerForm.vue';

const props = defineProps<{
    team: Team;
    player?: Player;
}>();

const isAddPlayerOpen = ref(false);
</script>

<template>
    <div v-if="$page.props.auth.user.role === 'coach'" class="flex justify-end">
        <Dialog v-model:open="isAddPlayerOpen">
            <DialogTrigger as-child
                ><Button variant="default" class="cursor-pointer">{{ props.player ? 'Edit' : 'Add Player' }}</Button></DialogTrigger
            >
            <DialogContent>
                <AddPlayerForm :team="team" :player="player" @close="isAddPlayerOpen = false" />
            </DialogContent>
        </Dialog>
    </div>
</template>
