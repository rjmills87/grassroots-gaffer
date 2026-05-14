<script setup lang="ts">
import EmptyState from '@/components/EmptyState.vue';
import { formatDate, formatRelativeTime } from '@/helpers';
import { Message, type MessageAttachment } from '@/types/Message';
import { router, useForm } from '@inertiajs/vue3';
import { File as FileIcon, FileText, Image, LoaderCircle, MessageSquareText } from 'lucide-vue-next';
import { computed, ref, type Component } from 'vue';
import { toast } from 'vue-sonner';
import Button from './ui/button/Button.vue';
import { Dialog, DialogContent } from './ui/dialog';
import Input from './ui/input/Input.vue';
import InputError from './InputError.vue';
import Label from './ui/label/Label.vue';
import Textarea from './ui/textarea/Textarea.vue';

const editingMessageId = ref<number | null>(null);
const processingMessageId = ref<number | null>(null);
const selectedAttachment = ref<MessageAttachment | null>(null);
const isAttachmentPreviewOpen = ref(false);

const props = defineProps<{
    messages: Message[];
}>();

const editForm = useForm({
    message: '',
    attachments: [] as File[],
    removed_attachment_ids: [] as number[],
    _method: 'put',
});

const editingMessage = computed(() => props.messages.find((m) => m.id === editingMessageId.value) ?? null);

const remainingAttachmentSlots = computed(() => {
    const m = editingMessage.value;
    if (!m?.attachments?.length) {
        return 3;
    }

    const kept = m.attachments.filter((a) => !editForm.removed_attachment_ids.includes(a.id)).length;

    return Math.max(0, 3 - kept);
});

const formatAttachmentSize = (size: number) => {
    if (size < 1024) {
        return `${size} B`;
    }

    if (size < 1024 * 1024) {
        return `${(size / 1024).toFixed(1)} KB`;
    }

    return `${(size / (1024 * 1024)).toFixed(1)} MB`;
};

const isPreviewableImage = (attachment: MessageAttachment) => attachment.mime_type.startsWith('image/');
const isPreviewablePdf = (attachment: MessageAttachment) => attachment.mime_type === 'application/pdf';

const attachmentListIcon = (attachment: MessageAttachment): Component => {
    if (attachment.mime_type === 'application/pdf') {
        return FileText;
    }

    if (attachment.mime_type.startsWith('image/')) {
        return Image;
    }

    return FileIcon;
};

const attachmentIconClass = (attachment: MessageAttachment): string => {
    if (attachment.mime_type === 'application/pdf') {
        return 'mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-400';
    }

    if (attachment.mime_type.startsWith('image/')) {
        return 'mt-0.5 h-5 w-5 shrink-0 text-sky-600 dark:text-sky-400';
    }

    return 'mt-0.5 h-5 w-5 shrink-0 text-muted-foreground';
};

const openAttachmentPreview = (attachment: MessageAttachment) => {
    selectedAttachment.value = attachment;
    isAttachmentPreviewOpen.value = true;
};

const toggleAttachmentRemoval = (attachmentId: number) => {
    if (editForm.removed_attachment_ids.includes(attachmentId)) {
        editForm.removed_attachment_ids = editForm.removed_attachment_ids.filter((id) => id !== attachmentId);

        return;
    }

    editForm.removed_attachment_ids = [...editForm.removed_attachment_ids, attachmentId];
};

const onEditAttachmentChange = (inputEvent: globalThis.Event) => {
    const files = (inputEvent.target as HTMLInputElement).files;
    const list = files ? Array.from(files) : [];
    editForm.attachments = list.slice(0, remainingAttachmentSlots.value);
};

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
    const found = props.messages.find((message) => message.id === messageId);
    if (!found) {
        return;
    }

    editingMessageId.value = messageId;
    editForm.message = found.message;
    editForm.attachments = [];
    editForm.removed_attachment_ids = [];
    editForm.clearErrors();
};

const saveMessage = () => {
    if (!editingMessageId.value) {
        return;
    }

    const messageId = editingMessageId.value;
    processingMessageId.value = messageId;

    editForm.post(route('messages.update', messageId), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            toast('Your message has been saved successfully');
            editingMessageId.value = null;
            editForm.reset();
            editForm.attachments = [];
            editForm.removed_attachment_ids = [];
        },
        onFinish: () => {
            processingMessageId.value = null;
        },
    });
};

const cancelEdit = () => {
    editingMessageId.value = null;
    editForm.reset();
    editForm.attachments = [];
    editForm.removed_attachment_ids = [];
};
</script>

<template>
    <div>
        <h2 class="text-xl font-semibold">Announcements</h2>
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

                    <template v-if="editingMessageId === message.id">
                        <div class="grid gap-2">
                            <Label :for="`edit-message-${message.id}`">Message</Label>
                            <Textarea :id="`edit-message-${message.id}`" v-model="editForm.message" />
                            <InputError :message="editForm.errors.message" />
                        </div>
                        <div class="grid gap-2">
                            <Label :for="`edit-attachments-${message.id}`">Add attachments</Label>
                            <Input
                                :id="`edit-attachments-${message.id}`"
                                type="file"
                                multiple
                                accept=".pdf,.jpg,.jpeg,.png"
                                @change="onEditAttachmentChange"
                            />
                            <p class="text-xs text-muted-foreground">
                                Up to 3 files total. PDF, JPG, JPEG, PNG only. 10MB each max. {{ remainingAttachmentSlots }} slot(s) for new files.
                            </p>
                            <p v-if="editForm.attachments.length > 0" class="text-xs text-muted-foreground">
                                {{ editForm.attachments.length }} new file(s) selected
                            </p>
                            <InputError :message="editForm.errors.attachments" />
                            <InputError :message="editForm.errors['attachments.0']" />
                        </div>
                        <div v-if="message.attachments?.length" class="grid gap-2">
                            <Label>Existing attachments</Label>
                            <div class="space-y-2">
                                <div
                                    v-for="attachment in message.attachments"
                                    :key="`edit-att-${attachment.id}`"
                                    class="flex items-center justify-between rounded-lg border p-2"
                                >
                                    <div class="flex min-w-0 flex-1 items-start gap-2">
                                        <component :is="attachmentListIcon(attachment)" :class="attachmentIconClass(attachment)" aria-hidden="true" />
                                        <div class="min-w-0 flex-1">
                                            <p
                                                class="truncate text-sm font-medium"
                                                :class="{ 'text-muted-foreground line-through': editForm.removed_attachment_ids.includes(attachment.id) }"
                                            >
                                                {{ attachment.original_name }}
                                            </p>
                                            <p class="text-xs text-muted-foreground">{{ formatAttachmentSize(attachment.size) }}</p>
                                        </div>
                                    </div>
                                    <Button
                                        class="cursor-pointer"
                                        :variant="editForm.removed_attachment_ids.includes(attachment.id) ? 'default' : 'outline'"
                                        size="sm"
                                        type="button"
                                        @click.prevent="toggleAttachmentRemoval(attachment.id)"
                                    >
                                        {{ editForm.removed_attachment_ids.includes(attachment.id) ? 'Undo remove' : 'Remove' }}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div>
                            <span class="text-sm">{{ message.message }}</span>
                        </div>
                        <div v-if="message.attachments?.length" class="space-y-2 border-t pt-3">
                            <p class="text-xs font-medium text-muted-foreground">Attachments</p>
                            <div
                                v-for="attachment in message.attachments"
                                :key="attachment.id"
                                class="flex flex-wrap items-center justify-between gap-2 rounded-lg border p-3"
                            >
                                <div class="flex min-w-0 flex-1 items-start gap-3">
                                    <component :is="attachmentListIcon(attachment)" :class="attachmentIconClass(attachment)" aria-hidden="true" />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium">{{ attachment.original_name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ formatAttachmentSize(attachment.size) }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <Button class="cursor-pointer" variant="outline" size="sm" type="button" @click="openAttachmentPreview(attachment)">
                                        View
                                    </Button>
                                    <Button class="cursor-pointer" variant="outline" size="sm" as-child>
                                        <a :href="route('messages.attachments.download', attachment.id)">Download</a>
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-between">
                        <div class="text-xs text-muted-foreground">Team announcement</div>
                        <div v-if="$page.props.auth.user.id === message.user.id" class="flex flex-row gap-4">
                            <template v-if="editingMessageId === message.id">
                                <Button
                                    class="h-10 cursor-pointer px-4"
                                    type="button"
                                    :disabled="editForm.processing"
                                    @click="saveMessage"
                                >
                                    <LoaderCircle v-if="editForm.processing" class="h-4 w-4 animate-spin" />
                                    {{ editForm.processing ? 'Saving...' : 'Save announcement' }}
                                </Button>
                                <Button
                                    class="h-10 cursor-pointer px-4"
                                    variant="outline"
                                    type="button"
                                    :disabled="editForm.processing"
                                    @click="cancelEdit"
                                >
                                    Cancel
                                </Button>
                            </template>
                            <template v-else>
                                <Button
                                    class="h-10 cursor-pointer px-4"
                                    type="button"
                                    :disabled="processingMessageId === message.id"
                                    @click="editMessage(message.id)"
                                >
                                    Edit announcement
                                </Button>
                                <Button class="h-10 cursor-pointer px-4" type="button" :disabled="processingMessageId === message.id" @click="deleteMessage(message.id)">
                                    <LoaderCircle v-if="processingMessageId === message.id" class="h-4 w-4 animate-spin" />
                                    {{ processingMessageId === message.id ? 'Deleting...' : 'Delete announcement' }}
                                </Button>
                            </template>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div v-else class="mt-4">
            <EmptyState title="No announcements yet" description="Send your first announcement to keep the team informed.">
                <template #icon>
                    <MessageSquareText class="h-5 w-5" />
                </template>
            </EmptyState>
        </div>
    </div>

    <Dialog v-model:open="isAttachmentPreviewOpen">
        <DialogContent>
            <div v-if="selectedAttachment" class="space-y-4">
                <h2 class="text-lg font-semibold">{{ selectedAttachment.original_name }}</h2>
                <div class="max-h-[70vh] overflow-auto rounded-md border p-2">
                    <img
                        v-if="isPreviewableImage(selectedAttachment)"
                        :src="route('messages.attachments.preview', selectedAttachment.id)"
                        :alt="selectedAttachment.original_name"
                        class="mx-auto max-h-[60vh] rounded-md object-contain"
                    />
                    <iframe
                        v-else-if="isPreviewablePdf(selectedAttachment)"
                        :src="route('messages.attachments.preview', selectedAttachment.id)"
                        class="h-[60vh] w-full rounded-md"
                        title="Attachment preview"
                    />
                    <p v-else class="text-sm text-muted-foreground">Preview not available for this file type. Please download to view.</p>
                </div>
                <div class="flex justify-end">
                    <Button class="cursor-pointer" as-child>
                        <a :href="route('messages.attachments.download', selectedAttachment.id)">Download</a>
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
