<script setup lang="ts">
import PublicHeader from '@/components/PublicHeader.vue';
import PublicFooter from '@/components/PublicFooter.vue';
</script>

<template>
    <PublicHeader />
    <slot />
    <PublicFooter />
</template>
