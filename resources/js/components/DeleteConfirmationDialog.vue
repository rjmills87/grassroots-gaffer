<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import { ref } from 'vue';
import DeleteConfirmation from './DeleteConfirmation.vue';

const props = defineProps<{
    itemType: string;
    itemName: string;
    deleteRoute: string;
    itemId: number;
    toastMessage: string;
}>();

const isDialogOpen = ref(false);
</script>
<template>
    <div v-if="$page.props.auth.user.role === 'coach'">
        <Dialog v-model:open="isDialogOpen">
            <DialogTrigger as-child>
                <Button class="cursor-pointer" variant="destructive">Delete {{ itemType }}</Button>
            </DialogTrigger>
            <DialogContent>
                <DeleteConfirmation
                    :itemType="itemType"
                    :itemName="itemName"
                    :deleteRoute="deleteRoute"
                    :itemId="itemId"
                    :toastMessage="toastMessage"
                    @close="isDialogOpen = false"
                />
            </DialogContent>
        </Dialog>
    </div>
</template>
