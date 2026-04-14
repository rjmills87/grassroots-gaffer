<script setup lang="ts">
import EmptyState from '@/components/EmptyState.vue';
import { formatDate, formatRelativeTime } from '@/helpers';
import { Message } from '@/types/Message';
import { router } from '@inertiajs/vue3';
import { LoaderCircle, MessageSquareText } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Button from './ui/button/Button.vue';
import Textarea from './ui/textarea/Textarea.vue';

const editingMessageId = ref<number | null>(null);
const messageContent = ref<string | undefined>(undefined);
const processingMessageId = ref<number | null>(null);

const props = defineProps<{
    messages: Message[];
}>();

const deleteMessage = (messageId: number) => {
    processingMessageId.value = messageId;

    router.delete(route('messages.destroy', messageId), {
        onSuccess: () => {
            toast('Your message has been deleted successfully');
        },
        onFinish: () => {
            processingMessageId.value = null;
        },
    });
};

const editMessage = (messageId: number) => {
    editingMessageId.value = messageId;
    messageContent.value = props.messages.find((message) => message.id === messageId)?.message;
};

const saveMessage = (messageId: number) => {
    processingMessageId.value = messageId;

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
            onFinish: () => {
                processingMessageId.value = null;
            },
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
            <ul class="flex flex-col gap-3">
                <li v-for="message in props.messages" :key="message.id" class="flex flex-col gap-3 rounded-lg border bg-card p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                                {{ message.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="font-semibold">{{ message.user.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ formatRelativeTime(message.created_at) }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-muted-foreground">{{ formatDate(message.created_at) }}</span>
                    </div>

                    <div v-if="editingMessageId === message.id"><Textarea v-model="messageContent" /></div>
                    <div v-else>
                        <span class="text-sm">{{ message.message }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-xs text-muted-foreground">Team announcement</div>
                        <div class="flex flex-row gap-4" v-if="$page.props.auth.user.id === message.user.id">
                            <Button
                                v-if="editingMessageId === message.id"
                                @click="saveMessage(message.id)"
                                class="h-10 cursor-pointer px-4"
                                :disabled="processingMessageId === message.id"
                            >
                                <LoaderCircle v-if="processingMessageId === message.id" class="h-4 w-4 animate-spin" />
                                {{ processingMessageId === message.id ? 'Saving...' : 'Save Message' }}
                            </Button>
                            <Button
                                v-else
                                @click="editMessage(message.id)"
                                class="h-10 cursor-pointer px-4"
                                :disabled="processingMessageId === message.id"
                            >
                                Edit Message
                            </Button>
                            <Button @click="deleteMessage(message.id)" class="h-10 cursor-pointer px-4" :disabled="processingMessageId === message.id">
                                <LoaderCircle v-if="processingMessageId === message.id" class="h-4 w-4 animate-spin" />
                                {{ processingMessageId === message.id ? 'Deleting...' : 'Delete Message' }}
                            </Button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div v-else class="mt-4">
            <EmptyState title="No messages yet" description="Send your first announcement to keep the team informed.">
                <template #icon>
                    <MessageSquareText class="h-5 w-5" />
                </template>
            </EmptyState>
        </div>
    </div>
</template>
