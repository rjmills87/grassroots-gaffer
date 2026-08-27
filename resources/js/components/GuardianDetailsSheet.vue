<script setup lang="ts">
import { Button } from '@/components/ui/button';
import Label from '@/components/ui/label/Label.vue';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Player } from '@/types/Player';
import { computed } from 'vue';

const props = defineProps<{
    player: Player;
}>();

const guardians = computed(() => {
    if (props.player.guardians && props.player.guardians.length > 0) {
        return props.player.guardians.map((guardian) => ({
            name: guardian.name,
            email: guardian.email,
            phone: guardian.id === undefined ? props.player.guardian_phone : guardian.email === props.player.guardian_email ? props.player.guardian_phone : null,
        }));
    }

    if (props.player.guardian_name || props.player.guardian_email) {
        return [
            {
                name: props.player.guardian_name,
                email: props.player.guardian_email,
                phone: props.player.guardian_phone,
            },
        ];
    }

    return [];
});
</script>

<template>
    <Sheet>
        <SheetTrigger asChild>
            <Button variant="default">View Details</Button>
        </SheetTrigger>
        <SheetContent class="max-w-[400px]">
            <SheetHeader class="p-4">
                <SheetTitle class="flex flex-col">
                    <span class="font-bold">Guardian Details</span>
                    <span class="text-xl font-semibold">{{ props.player.name }}</span>
                </SheetTitle>
            </SheetHeader>
            <SheetDescription class="space-y-4 p-4">
                <div v-for="(guardian, index) in guardians" :key="`${guardian.email}-${index}`" class="flex flex-col gap-2">
                    <p v-if="guardians.length > 1" class="text-xs font-semibold tracking-wide text-muted-foreground uppercase">
                        Parent {{ index + 1 }}
                    </p>
                    <Label class="font-bold">Name</Label>
                    <p>{{ guardian.name }}</p>
                    <Label class="font-bold">Email</Label>
                    <p>{{ guardian.email }}</p>
                    <div v-if="guardian.phone">
                        <Label class="font-bold">Phone</Label>
                        <p>{{ guardian.phone }}</p>
                    </div>
                </div>
                <p v-if="guardians.length === 0" class="text-sm text-muted-foreground">No parent linked yet.</p>
            </SheetDescription>
        </SheetContent>
    </Sheet>
</template>
