<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { ref } from 'vue';
import CreateTeamForm from './CreateTeamForm.vue';

const isCreateTeamOpen = ref(false);
</script>

<template>
    <Dialog v-model:open="isCreateTeamOpen">
        <DialogTrigger as-child>
            <Button>
                <span class="hidden sm:inline">Create New Team</span>
            </Button>
        </DialogTrigger>
        <DialogContent>
            <CreateTeamForm @close="isCreateTeamOpen = false" />
        </DialogContent>
    </Dialog>
</template>
