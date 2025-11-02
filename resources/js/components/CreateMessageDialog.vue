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
    <div v-if="$page.props.auth.user.role === 'coach'" class="mt-8 mb-4 flex justify-end">
        <Dialog v-model:open="isCreateMessageOpen">
            <DialogTrigger as-child><Button>Create Message</Button></DialogTrigger>
            <DialogContent>
                <CreateMessageForm :team="team" @close="isCreateMessageOpen = false" />
            </DialogContent>
        </Dialog>
    </div>
</template>
