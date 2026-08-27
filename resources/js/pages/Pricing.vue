<script setup lang="ts">
import PricingTable from '@/components/PricingTable.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';

const freeTier = [
    {
        feature: 'Teams and squad',
        description: 'Players and guardian contact details',
    },
    {
        feature: 'Join codes',
        description: 'WhatsApp join codes so parents add themselves',
    },
    {
        feature: 'Matches and training',
        description: 'Unlimited events with location and times',
    },
    {
        feature: 'RSVPs',
        description: 'Parents tap in or out for each event',
    },
    {
        feature: 'Reminders',
        description: 'Nudge guardians who have not replied — included, not paid',
    },
    {
        feature: 'Announcements',
        description: 'One-way updates from the coach to the team',
    },
    {
        feature: 'No ads',
        description: 'No betting ads, no trial that runs out',
    },
];

const tiers = [
    {
        name: 'For coaches, free',
        price: 'Free',
        description: 'The core tools are free. There is no paid plan to buy today.',
        features: freeTier,
        idealFor: 'Grassroots coaches running a team from the touchline',
    },
];
</script>
<template>
    <PublicLayout>
        <Head title="Pricing" />
        <main class="dark:bg-[#0a0a0a]">
            <div class="flex w-full flex-col items-center px-6 py-24 sm:py-32">
                <div class="mb-8 w-full max-w-4xl text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">Pricing</h1>
                    <p class="mt-6 text-lg text-gray-600 dark:text-gray-400">Nothing to buy. Coaches and parents use the core tools for free.</p>
                </div>
                <PricingTable :tiers="tiers" />
                <div class="mt-12 w-full max-w-4xl text-center">
                    <h2 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">Later, not live</h2>
                    <p class="mt-4 text-base leading-7 text-gray-600 dark:text-gray-400">
                        We may later charge for things that cost money to send, such as SMS frost or venue-change alert packs, and later still for
                        subs collection. None of that is for sale now, and there is no price on it. Recurring training and a one-tap call-off or venue
                        change that notifies parents are also on the list — still not a session planner or stats pack.
                    </p>
                </div>
            </div>
        </main>
    </PublicLayout>
</template>
