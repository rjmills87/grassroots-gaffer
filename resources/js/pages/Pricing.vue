<script setup lang="ts">
import PricingTable from '@/components/PricingTable.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';

const freeTier = [
    {
        feature: 'User Experience',
        description: 'Completely Ad-Free UI',
    },
    {
        feature: 'Team Size',
        description: 'Unlimited Players & Parents/Guardians',
    },
    {
        feature: 'Scheduling',
        description: 'Unlimited Events (Matches, Training, etc.)',
    },
    {
        feature: 'Attendance',
        description: 'Simple Yes/No Availability Tracking',
    },
    {
        feature: 'Communication',
        description: 'Coach to Team Announcements & Notifications only.',
    },
    {
        feature: 'Support',
        description: 'Standard Support via Email',
    },
    {
        feature: 'Pro Features',
        description: 'None',
    },
];

const proTier = [
    {
        feature: 'Attendance',
        description: 'Full Attendance Reporting & Statistics (e.g., Attendance %, Absentees, etc.)',
    },
    {
        feature: 'Development',
        description: 'Session Planner & Training Drills Storage (Create, save and categorise training plans)',
    },
    {
        feature: 'Logistics',
        description: 'Parent-to-Parent Contact Sharing (Allows carpool organising without the coach as the middleman)',
    },
    {
        feature: 'Player Data',
        description: 'Basic Player Stats Tracker (Minutes played, post-game notes)',
    },
    {
        feature: 'Support',
        description: 'Priority Support via Email',
    },
];

const tiers = [
    {
        name: 'The Free Tier',
        price: 'Free',
        description: 'Free Forever (No ads, no hidden fees)',
        features: freeTier,
        idealFor: 'New coaches, individual parents, or teams just needing basic scheduling',
    },
    {
        name: 'The Gaffer Pro Tier',
        price: '£6.99 per month or £49.99 per year',
        description: 'Everything in Free, PLUS...',
        features: proTier,
        idealFor: 'Dedicated coaches, managers, and administrators running multiple teams or needing detailed player data.',
    },
];

const props = defineProps({
    tiers: {
        type: Array as () => { name: string; price: string; description: string; features: { feature: string; description: string }[] }[],
        required: true,
    },
});
</script>
<template>
    <PublicLayout>
        <Head title="Pricing" />
        <main class="dark:bg-[#0a0a0a]">
            <div class="flex w-full flex-col items-center px-6 py-24 sm:py-32">
                <div class="mb-8 w-full max-w-4xl text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">Pricing</h1>
                    <p class="mt-6 text-lg text-gray-600 dark:text-gray-400">
                        We offer a simple, transparent pricing model with no hidden fees. You can cancel at any time.
                    </p>
                </div>
                <PricingTable :tiers="tiers" />
            </div>
        </main>
    </PublicLayout>
</template>
