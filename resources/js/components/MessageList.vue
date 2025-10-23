<script setup lang="ts">
import { formatDate } from '@/helpers';
import { Message } from '@/types/Message';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Button from './ui/button/Button.vue';
import Textarea from './ui/textarea/Textarea.vue';

const editingMessageId = ref<number | null>(null);
const messageContent = ref<string | undefined>(undefined);

const props = defineProps<{
    messages: Message[];
}>();

const deleteMessage = (messageId: number) => {
    router.delete(route('messages.destroy', messageId), {
        onSuccess: () => {
            toast('Your message has been deleted successfully');
        },
    });
};

const editMessage = (messageId: number) => {
    editingMessageId.value = messageId;
    messageContent.value = props.messages.find((message) => message.id === messageId)?.message;
};

const saveMessage = (messageId: number) => {
    router.put(
        route('messages.update', messageId),
        {
            message: messageContent.value,
        },
        {
            onSuccess: () => {
                toast('Your message has been saved successfully');
            },
            preserveScroll: true,
        },
    );
    editingMessageId.value = null;
    messageContent.value = undefined;
};
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold">Messages</h2>
        <div v-if="props.messages && props.messages.length > 0" class="mt-4">
            <ul class="flex flex-col gap-2">
                <li v-for="message in props.messages" :key="message.id" class="flex flex-col gap-2 rounded-lg border border-gray-200 p-4">
                    <span class="font-bold">Message by: {{ message.user.name }}</span>
                    <div v-if="editingMessageId === message.id"><Textarea v-model="messageContent" /></div>
                    <div v-else>
                        <span> {{ message.message }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-semibold">Message Sent: {{ formatDate(message.created_at) }}</span>
                        </div>
                        <div class="flex flex-row gap-4" v-if="$page.props.auth.user.id === message.user.id">
                            <Button v-if="editingMessageId === message.id" @click="saveMessage(message.id)" class="cursor-pointer"
                                >Save Message</Button
                            >
                            <Button v-else @click="editMessage(message.id)" class="cursor-pointer">Edit Message</Button>
                            <Button @click="deleteMessage(message.id)" class="cursor-pointer">Delete Message</Button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div v-else class="mt-4">
            <p>This team has no messages.</p>
        </div>
    </div>
</template>
