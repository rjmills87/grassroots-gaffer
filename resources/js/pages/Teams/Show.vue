<!-- eslint-disable @typescript-eslint/no-unused-vars -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Team } from '@/types/Team';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    team: Team;
}>();
</script>

<template>
    <Head :title="team.name" />
    <AppLayout>
        <div>
            <h1>{{ team.name }}</h1>
        </div>
    </AppLayout>
</template>
