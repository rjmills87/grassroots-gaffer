<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { type PendingJoin } from '@/types/Join';
import { router } from '@inertiajs/vue3';
import { Inbox } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import EmptyState from './EmptyState.vue';

const props = defineProps<{
    pendingJoins: PendingJoin[];
}>();

const processingId = ref<number | null>(null);

const respond = (join: PendingJoin, action: 'approve' | 'reject') => {
    processingId.value = join.id;
    const routeName = action === 'approve' ? 'team-join-requests.approve' : 'team-join-requests.reject';

    router.post(
        route(routeName, join.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast(
                    action === 'approve'
                        ? `${join.guardian_name} can now join as a parent of ${join.player_name}.`
                        : `Declined ${join.guardian_name}'s request for ${join.player_name}.`,
                );
            },
            onFinish: () => {
                processingId.value = null;
            },
        },
    );
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center justify-between gap-3">
                <span>Parent join requests</span>
                <span
                    v-if="pendingJoins.length > 0"
                    class="rounded-full bg-teal-600 px-2.5 py-0.5 text-xs font-semibold text-white"
                >
                    {{ pendingJoins.length }}
                </span>
            </CardTitle>
            <CardDescription>
                Approve a parent before they can see the team. A second parent can attach to a child who is already on
                the squad.
            </CardDescription>
        </CardHeader>
        <CardContent>
            <div v-if="pendingJoins.length === 0">
                <EmptyState title="No pending requests" description="When a parent uses your WhatsApp link, their request will show up here.">
                    <template #icon>
                        <Inbox class="h-5 w-5" />
                    </template>
                </EmptyState>
            </div>
            <ul v-else class="space-y-3">
                <li v-for="join in pendingJoins" :key="join.id" class="rounded-lg border bg-card p-4 shadow-sm">
                    <p class="font-semibold">{{ join.guardian_name }}</p>
                    <p class="text-sm text-muted-foreground">{{ join.guardian_email }}</p>
                    <p class="mt-2 text-sm">
                        <span v-if="join.is_new_player">
                            Wants to add <span class="font-medium">{{ join.player_name }}</span> to the squad
                        </span>
                        <span v-else>
                            Parent of <span class="font-medium">{{ join.player_name }}</span>
                        </span>
                    </p>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <Button
                            class="h-11 cursor-pointer"
                            :disabled="processingId === join.id"
                            @click="respond(join, 'approve')"
                        >
                            Approve
                        </Button>
                        <Button
                            variant="outline"
                            class="h-11 cursor-pointer"
                            :disabled="processingId === join.id"
                            @click="respond(join, 'reject')"
                        >
                            Decline
                        </Button>
                    </div>
                </li>
            </ul>
        </CardContent>
    </Card>
</template>
