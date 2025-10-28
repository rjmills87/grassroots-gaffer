<script setup lang="ts">
import { Team } from '@/types/Team';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Button from './ui/button/Button.vue';
import Input from './ui/input/Input.vue';

const props = defineProps<{
    team: Team;
}>();

const teamName = ref('');

const deleteTeam = (teamId: number) => {
    router.delete(route('teams.destroy', teamId), {
        onSuccess: () => {
            toast('Your team has been deleted successfully');
        },
    });
};
</script>
<template>
    <div v-if="props.team">
        <p class="pb-2 font-bold text-red-600">Danger Zone!</p>
        <p class="pb-2 text-black dark:text-white">Are you sure you want to delete this team?</p>
        <p class="pb-4 text-black dark:text-white">This cannot be undone.</p>
        <Input v-model="teamName" type="text" placeholder="Type the team name to confirm deletion" />
        <p v-if="teamName !== team.name" class="text-red-600">Team names must match.</p>
        <p v-else class="text-green-600">Team names match.</p>
        <div v-if="teamName === team.name" class="flex justify-end">
            <Button @click="deleteTeam(team.id)" variant="destructive">Confirm Deletion</Button>
        </div>
    </div>
</template>
