<script setup lang="ts">
import { formatDate } from '@/helpers';
import { Message } from '@/types/Message';

const props = defineProps<{
    messages: Message[];
}>();
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold">Messages</h2>
        <div v-if="props.messages && props.messages.length > 0" class="mt-4">
            <ul class="flex flex-col gap-2">
                <li v-for="message in props.messages" :key="message.id" class="flex flex-col gap-2 rounded-lg border border-gray-200 p-4">
                    <span class="font-bold">Message by: {{ message.user.name }}</span>
                    <span> {{ message.message }}</span>
                    <span class="font-semibold">Message Sent: {{ formatDate(message.created_at) }}</span>
                </li>
            </ul>
        </div>
        <div v-else class="mt-4">
            <p>This team has no messages.</p>
        </div>
    </div>
</template>
