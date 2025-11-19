<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { Team } from '@/types/Team';
import { ref } from 'vue';
import CreateMessageForm from './CreateMessageForm.vue';

const props = defineProps<{
    team: Team;
}>();

const isCreateMessageOpen = ref(false);
</script>
<template>
    <div class="mt-8 mr-4 mb-4 flex justify-end">
        <Dialog v-model:open="isCreateMessageOpen">
            <DialogTrigger as-child><Button variant="default" class="cursor-pointer">Create Message</Button></DialogTrigger>
            <DialogContent>
                <CreateMessageForm :team="team" @close="isCreateMessageOpen = false" />
            </DialogContent>
        </Dialog>
    </div>
</template>
