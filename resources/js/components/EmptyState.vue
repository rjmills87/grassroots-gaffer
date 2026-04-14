<script setup lang="ts">
defineProps<{
    title: string;
    description: string;
}>();
</script>

<template>
    <div class="rounded-lg border border-dashed p-8 text-center">
        <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-muted text-muted-foreground">
            <slot name="icon" />
        </div>
        <h3 class="text-base font-semibold">{{ title }}</h3>
        <p class="mt-2 text-sm text-muted-foreground">{{ description }}</p>
        <div v-if="$slots.action" class="mt-4 flex justify-center">
            <slot name="action" />
        </div>
    </div>
</template>
