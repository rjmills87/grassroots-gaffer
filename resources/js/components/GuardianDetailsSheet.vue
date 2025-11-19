<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Player } from '@/types/Player';

const props = defineProps<{
    player: Player;
}>();
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
            <SheetDescription class="space-y-2 p-4">
                <div class="flex flex-col gap-2">
                    <Label class="font-bold">Name</Label>
                    <p>{{ props.player.guardian_name }}</p>
                    <Label class="font-bold">Email</Label>
                    <p>{{ props.player.guardian_email }}</p>
                    <Label class="font-bold">Phone</Label>
                    <p>{{ props.player.guardian_phone }}</p>
                </div>
            </SheetDescription>
        </SheetContent>
    </Sheet>
</template>
