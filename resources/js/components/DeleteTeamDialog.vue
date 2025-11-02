<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { Team } from '@/types/Team';
import { ref } from 'vue';
import DeleteTeam from './DeleteTeam.vue';

const props = defineProps<{
    team: Team;
}>();

const isDeleteTeamOpen = ref(false);
</script>
<template>
    <div v-if="$page.props.auth.user.role === 'coach'" class="mt-8 mr-4 mb-4 flex justify-end">
        <Dialog v-model:open="isDeleteTeamOpen">
            <DialogTrigger><Button class="cursor-pointer" variant="destructive">Delete Team</Button></DialogTrigger>
            <DialogContent>
                <DeleteTeam :team="team" @close="isDeleteTeamOpen = false" />
            </DialogContent>
        </Dialog>
    </div>
</template>
