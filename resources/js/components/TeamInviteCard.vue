<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { type TeamInvite } from '@/types/Join';
import { type Team } from '@/types/Team';
import { router } from '@inertiajs/vue3';
import { Check, Copy, Link2, MessageCircle, RefreshCw } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    team: Team;
    invite: TeamInvite;
}>();

const copied = ref<'message' | 'link' | 'code' | null>(null);
const isRotateOpen = ref(false);
const isRotating = ref(false);

const whatsappShareUrl = computed(
    () => `https://wa.me/?text=${encodeURIComponent(props.invite.whatsapp_message)}`,
);

const copyText = async (value: string, kind: 'message' | 'link' | 'code', successMessage: string) => {
    try {
        await navigator.clipboard.writeText(value);
        copied.value = kind;
        toast(successMessage);
        window.setTimeout(() => {
            if (copied.value === kind) {
                copied.value = null;
            }
        }, 2000);
    } catch {
        toast('Could not copy automatically. Long-press the text to copy.');
    }
};

const rotateCode = () => {
    isRotating.value = true;
    router.post(
        route('teams.invite.rotate', props.team.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                isRotateOpen.value = false;
                toast('New join code created. The old WhatsApp link will no longer work.');
            },
            onFinish: () => {
                isRotating.value = false;
            },
        },
    );
};
</script>

<template>
    <Card class="border-teal-200 bg-teal-50/40 dark:border-teal-900 dark:bg-teal-950/20">
        <CardHeader class="gap-2">
            <CardTitle>Parent join link</CardTitle>
            <CardDescription>
                Copy this into the parent WhatsApp group. Parents use their own email and pick their child — including a
                second parent in a split household. If the link doesn't open, they can open Join a team and type this
                code.
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
            <div class="rounded-lg border bg-background px-4 py-5 text-center">
                <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">Join code</p>
                <p class="mt-2 font-mono text-3xl font-bold tracking-[0.35em] sm:text-4xl">{{ invite.code }}</p>
            </div>

            <p class="break-all rounded-md bg-muted px-3 py-2 text-center text-sm text-muted-foreground">{{ invite.url }}</p>

            <div class="grid gap-2">
                <Button class="h-12 w-full cursor-pointer text-base" size="lg" @click="copyText(invite.whatsapp_message, 'message', 'WhatsApp message copied')">
                    <Check v-if="copied === 'message'" class="h-4 w-4" />
                    <Copy v-else class="h-4 w-4" />
                    {{ copied === 'message' ? 'Copied' : 'Copy WhatsApp message' }}
                </Button>
                <a :href="whatsappShareUrl" target="_blank" rel="noopener noreferrer" class="w-full">
                    <Button type="button" variant="outline" class="h-12 w-full cursor-pointer text-base" size="lg">
                        <MessageCircle class="h-4 w-4" />
                        Open WhatsApp
                    </Button>
                </a>
                <div class="grid grid-cols-2 gap-2">
                    <Button variant="secondary" class="h-11 cursor-pointer" @click="copyText(invite.url, 'link', 'Join link copied')">
                        <Check v-if="copied === 'link'" class="h-4 w-4" />
                        <Link2 v-else class="h-4 w-4" />
                        {{ copied === 'link' ? 'Copied' : 'Copy link' }}
                    </Button>
                    <Button variant="secondary" class="h-11 cursor-pointer" @click="copyText(invite.code, 'code', 'Join code copied')">
                        <Check v-if="copied === 'code'" class="h-4 w-4" />
                        <Copy v-else class="h-4 w-4" />
                        {{ copied === 'code' ? 'Copied' : 'Copy code' }}
                    </Button>
                </div>
            </div>

            <button
                type="button"
                class="inline-flex items-center gap-1.5 text-sm text-muted-foreground underline-offset-4 hover:underline"
                @click="isRotateOpen = true"
            >
                <RefreshCw class="h-3.5 w-3.5" />
                Make a new code
            </button>
        </CardContent>
    </Card>

    <Dialog v-model:open="isRotateOpen">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Replace the join code?</DialogTitle>
                <DialogDescription>
                    Parents with the old WhatsApp link or code will not be able to join until you send the new one.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2 sm:justify-end">
                <Button variant="outline" class="cursor-pointer" @click="isRotateOpen = false">Keep current code</Button>
                <Button class="cursor-pointer" :disabled="isRotating" @click="rotateCode">
                    {{ isRotating ? 'Updating...' : 'Create new code' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
