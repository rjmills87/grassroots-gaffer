<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

interface JoinPlayer {
    id: number;
    name: string;
    squad_number: number | null;
}

interface JoinTeam {
    name: string;
    age_group: string;
    coach_first_name: string | null;
}

const props = defineProps<{
    valid: boolean | null;
    reason: string | null;
    code: string | null;
    team: JoinTeam | null;
    players: JoinPlayer[];
}>();

const lookupForm = useForm({
    code: props.code ?? '',
});

const joinForm = useForm({
    guardian_name: '',
    guardian_email: '',
    guardian_phone: '',
    child_mode: props.players.length > 0 ? 'existing' : 'new',
    player_id: null as number | null,
    requested_player_name: '',
});

const selectedChildName = computed(() => {
    if (joinForm.child_mode === 'new') {
        return joinForm.requested_player_name || 'your child';
    }

    return props.players.find((player) => player.id === joinForm.player_id)?.name ?? 'your child';
});

const submitLookup = () => {
    lookupForm.post(route('join.lookup'));
};

const submitJoin = () => {
    if (!props.code) {
        return;
    }

    joinForm.post(route('join.store', props.code));
};

const selectExisting = (playerId: number) => {
    joinForm.child_mode = 'existing';
    joinForm.player_id = playerId;
    joinForm.requested_player_name = '';
};

const selectNewChild = () => {
    joinForm.child_mode = 'new';
    joinForm.player_id = null;
};
</script>

<template>
    <Head :title="team ? `Join ${team.name}` : 'Join a team'" />

    <PublicLayout>
        <div class="mx-auto max-w-lg px-4 py-10 sm:px-6 sm:py-16">
            <div v-if="valid === null" class="rounded-2xl border bg-white p-6 shadow-sm dark:bg-gray-900">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Join your child's team</h1>
                <p class="mt-2 text-sm leading-6 text-muted-foreground">
                    Enter the code from your coach's WhatsApp message. You'll join as a parent — not as a player — using
                    your own email.
                </p>
                <form class="mt-6 space-y-4" @submit.prevent="submitLookup">
                    <div class="grid gap-2">
                        <Label for="code">Join code</Label>
                        <Input
                            id="code"
                            v-model="lookupForm.code"
                            class="h-12 text-center font-mono text-lg tracking-[0.3em] uppercase"
                            maxlength="12"
                            autocomplete="off"
                            placeholder="ABCD1234"
                        />
                        <InputError :message="lookupForm.errors.code" />
                    </div>
                    <Button type="submit" class="h-12 w-full cursor-pointer bg-teal-600 text-base hover:bg-teal-500" :disabled="lookupForm.processing">
                        <LoaderCircle v-if="lookupForm.processing" class="h-4 w-4 animate-spin" />
                        Continue
                    </Button>
                </form>
            </div>

            <div v-else-if="!valid" class="rounded-2xl border bg-white p-6 text-center shadow-sm dark:bg-gray-900">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">This join link isn't valid</h1>
                <p class="mt-3 text-sm leading-6 text-muted-foreground">
                    {{
                        reason === 'expired'
                            ? team
                                ? `The join link for ${team.name} has expired.`
                                : 'This join link has expired.'
                            : 'This code or link is wrong, or your coach has created a new one.'
                    }}
                    Ask your coach to send the latest WhatsApp join message.
                </p>
                <form class="mt-6 space-y-4 text-left" @submit.prevent="submitLookup">
                    <div class="grid gap-2">
                        <Label for="retry-code">Try another code</Label>
                        <Input
                            id="retry-code"
                            v-model="lookupForm.code"
                            class="h-12 text-center font-mono text-lg tracking-[0.3em] uppercase"
                            maxlength="12"
                            autocomplete="off"
                        />
                    </div>
                    <Button type="submit" class="h-12 w-full cursor-pointer bg-teal-600 text-base hover:bg-teal-500" :disabled="lookupForm.processing">
                        Continue
                    </Button>
                </form>
            </div>

            <div v-else class="space-y-6">
                <div>
                    <p class="text-sm font-semibold text-teal-600 dark:text-teal-400">Parent join</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Join {{ team?.name }}</h1>
                    <p class="mt-2 text-sm leading-6 text-muted-foreground">
                        You're joining as a parent or guardian, not as a player.
                        <span v-if="team?.coach_first_name"> {{ team.coach_first_name }} will confirm before you're added.</span>
                    </p>
                </div>

                <form class="space-y-5 rounded-2xl border bg-white p-6 shadow-sm dark:bg-gray-900" @submit.prevent="submitJoin">
                    <div class="grid gap-2">
                        <Label for="guardian_name">Your name</Label>
                        <Input id="guardian_name" v-model="joinForm.guardian_name" class="h-11" autocomplete="name" />
                        <InputError :message="joinForm.errors.guardian_name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="guardian_email">Your email</Label>
                        <Input id="guardian_email" v-model="joinForm.guardian_email" type="email" class="h-11" autocomplete="email" />
                        <p class="text-xs text-muted-foreground">Use your own email, not your child's.</p>
                        <InputError :message="joinForm.errors.guardian_email || joinForm.errors.code" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="guardian_phone">Your phone number</Label>
                        <Input id="guardian_phone" v-model="joinForm.guardian_phone" class="h-11" autocomplete="tel" />
                        <InputError :message="joinForm.errors.guardian_phone" />
                    </div>

                    <fieldset class="space-y-3">
                        <legend class="text-sm font-medium">Which child are you the parent of?</legend>
                        <button
                            v-for="player in players"
                            :key="player.id"
                            type="button"
                            class="flex w-full items-center justify-between rounded-lg border px-4 py-3 text-left"
                            :class="
                                joinForm.child_mode === 'existing' && joinForm.player_id === player.id
                                    ? 'border-teal-600 bg-teal-50 dark:bg-teal-950/40'
                                    : 'bg-background'
                            "
                            @click="selectExisting(player.id)"
                        >
                            <span class="font-medium">{{ player.name }}</span>
                            <span v-if="player.squad_number" class="text-sm text-muted-foreground">#{{ player.squad_number }}</span>
                        </button>
                        <button
                            type="button"
                            class="w-full rounded-lg border px-4 py-3 text-left"
                            :class="joinForm.child_mode === 'new' ? 'border-teal-600 bg-teal-50 dark:bg-teal-950/40' : 'bg-background'"
                            @click="selectNewChild"
                        >
                            <span class="font-medium">My child isn't listed</span>
                            <span class="mt-1 block text-xs text-muted-foreground">Ask the coach to add them to the squad</span>
                        </button>
                        <InputError :message="joinForm.errors.player_id || joinForm.errors.child_mode" />
                    </fieldset>

                    <div v-if="joinForm.child_mode === 'new'" class="grid gap-2">
                        <Label for="requested_player_name">Child's name</Label>
                        <Input id="requested_player_name" v-model="joinForm.requested_player_name" class="h-11" />
                        <InputError :message="joinForm.errors.requested_player_name" />
                    </div>

                    <Button type="submit" class="h-12 w-full cursor-pointer bg-teal-600 text-base hover:bg-teal-500" :disabled="joinForm.processing">
                        <LoaderCircle v-if="joinForm.processing" class="h-4 w-4 animate-spin" />
                        Ask to join as parent of {{ selectedChildName }}
                    </Button>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
