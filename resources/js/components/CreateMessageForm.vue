<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Team } from '@/types/Team';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import InputError from './InputError.vue';
import Input from './ui/input/Input.vue';
import Label from './ui/label/Label.vue';

const props = defineProps<{
    team: Team;
}>();

const emit = defineEmits<{
    close: [];
}>();

const form = useForm({
    message: '',
    attachments: [] as File[],
});

const onAttachmentChange = (inputEvent: globalThis.Event) => {
    const files = (inputEvent.target as HTMLInputElement).files;
    form.attachments = files ? Array.from(files).slice(0, 3) : [];
};

const submit = () => {
    form.post(route('teams.messages.store', props.team.id), {
        preserveScroll: true,
        forceFormData: form.attachments.length > 0,
        onSuccess: () => {
            form.reset();
            form.attachments = [];
            emit('close');
            toast('Your message has been created successfully');
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="mt-4 space-y-4">
        <div class="grid gap-2">
            <h3 class="text-lg font-semibold">Send an Announcement</h3>
            <p class="text-sm text-gray-500">Your announcement will be sent to all players and guardians on the team.</p>
        </div>
        <div class="grid gap-2">
            <Label for="create-message-body">Message</Label>
            <Textarea id="create-message-body" v-model="form.message" placeholder="Type your announcement here..." />
            <InputError :message="form.errors.message" />
        </div>
        <div class="grid gap-2">
            <Label for="create-message-attachments">Attachments (optional)</Label>
            <Input
                id="create-message-attachments"
                type="file"
                multiple
                accept=".pdf,.jpg,.jpeg,.png"
                @change="onAttachmentChange"
            />
            <p class="text-xs text-muted-foreground">Up to 3 files. PDF, JPG, JPEG, PNG only. 10MB max per file.</p>
            <p v-if="form.attachments.length > 0" class="text-xs text-muted-foreground">{{ form.attachments.length }} file(s) selected</p>
            <InputError :message="form.errors.attachments" />
            <InputError :message="form.errors['attachments.0']" />
        </div>
        <Button type="submit" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
            {{ form.processing ? 'Sending...' : 'Send Announcement' }}
        </Button>
    </form>
</template>
