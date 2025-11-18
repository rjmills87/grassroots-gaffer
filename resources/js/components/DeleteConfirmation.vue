<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Button from './ui/button/Button.vue';
import Input from './ui/input/Input.vue';

const props = defineProps<{
    itemType: string;
    itemName: string;
    deleteRoute: string;
    itemId: number;
    toastMessage: string;
}>();
const emit = defineEmits<{
    close: [];
}>();

const confirmationInput = ref('');

const comfirmDeletion = () => {
    router.delete(route(props.deleteRoute, props.itemId), {
        onSuccess: () => {
            emit('close');
            toast(props.toastMessage);
        },
    });
};
</script>
<template>
    <div v-if="props.itemType">
        <p class="pb-2 font-bold text-red-600">Danger Zone!</p>
        <p class="pb-2 text-black dark:text-white">Are you sure you want to delete this {{ props.itemType }}?</p>
        <p class="pb-2 font-bold">{{ props.itemName }}</p>
        <p class="pb-2 text-black dark:text-white">This cannot be undone.</p>
        <p class="pb-2">Type the {{ props.itemType }} name to confirm deletion</p>
        <Input v-model="confirmationInput" type="text" />
        <p v-if="confirmationInput !== props.itemName" class="mt-2 text-red-600">{{ props.itemType }} names must match.</p>

        <p v-else class="py-2 font-bold text-green-600">{{ props.itemType }} names match.</p>
        <div v-if="confirmationInput === props.itemName">
            <Button @click="comfirmDeletion" variant="destructive">Confirm Deletion</Button>
        </div>
    </div>
</template>
