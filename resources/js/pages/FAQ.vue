<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';

const generalFAQs = [
    {
        value: 'item-1',
        question: 'What is Squadra365?',
        answer: 'Squadra365 is an ad-free web app for grassroots football coaches. You can run the squad, put matches and training on the calendar, collect parent RSVPs, send reminders, and post one-way announcements. Parents join with a WhatsApp code. It does not register players with the FA, and it does not replace Matchday or Club Portal — it sits beside them.',
    },
    {
        value: 'item-2',
        question: 'Is it ad-free?',
        answer: 'Yes. No betting ads, and no ads in front of the kids.',
    },
    {
        value: 'item-3',
        question: 'How is this different from other team apps?',
        answer: 'It is for the WhatsApp-admin problem: who is in on Saturday, where is training, and can you tell the parents without a group-chat pile-on. It is not a club system, not an FA registration tool, and not a session planner or match-stats pack.',
    },
    {
        value: 'item-4',
        question: 'Can parents use it, or is it only for the coach?',
        answer: 'Both. The coach runs the team and events. Parents and guardians RSVP and receive announcements. A second parent can join the same child.',
    },
    {
        value: 'item-4b',
        question: "How do I join my child's team?",
        answer: "Tap the join link in your coach's WhatsApp message. If the link doesn't open, go to Join a team in the menu (or /join), type the 8-character code from the message, and continue. Use your own email, not your child's. A second parent can join the same child too.",
    },
];

const pricingFAQs = [
    {
        value: 'item-5',
        question: 'Is there a paid plan?',
        answer: 'Not today. The coach core is free: teams, players and guardians, events, RSVPs, reminders, one-way announcements, and join codes. There is no subscription to buy.',
    },
    {
        value: 'item-6',
        question: 'Will you charge later?',
        answer: 'Possibly, for things that cost money to send — SMS frost or venue-change alert packs — and later still for subs collection. None of that is live. Reminders stay in the free core.',
    },
    {
        value: 'item-7',
        question: 'Who pays?',
        answer: 'Nobody, today. Coaches use the core tools for free. Parents join and RSVP for free.',
    },
    {
        value: 'item-8',
        question: 'Does Squadra365 register players with the FA?',
        answer: 'No. It does not register anyone with the FA, and it does not replace Matchday or Club Portal. Use those for affiliation and fixtures. Use Squadra365 for the squad, RSVPs, reminders, and parent comms.',
    },
    {
        value: 'item-9',
        question: 'Can I run more than one team?',
        answer: 'Yes. A coach can already have more than one team on a free account, with separate squads and events.',
    },
];
</script>
<template>
    <PublicLayout>
        <Head title="FAQ" />
        <main class="dark:bg-[#0a0a0a]">
            <div class="flex w-full flex-col items-center px-6 py-24 sm:py-32">
                <div class="w-full max-w-4xl text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">Frequently Asked Questions</h1>
                    <p class="mt-6 text-lg text-gray-600 dark:text-gray-400">
                        Find answers to common questions about Squadra365 below. If you have a question that is not answered here, please contact us
                        at
                        <a href="mailto:hello@squadra365.com">hello@squadra365.com</a>.
                    </p>
                </div>
                <div class="mt-12 w-full max-w-4xl text-center">
                    <h2 class="mt-12 mb-8 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">General</h2>
                </div>

                <Accordion type="single" collapsible class="w-full max-w-4xl">
                    <AccordionItem v-for="faq in generalFAQs" :key="faq.value" :value="faq.value">
                        <AccordionTrigger>{{ faq.question }}</AccordionTrigger>
                        <AccordionContent>{{ faq.answer }}</AccordionContent>
                    </AccordionItem>
                </Accordion>

                <div class="mt-12 w-full max-w-4xl text-center">
                    <h2 class="mt-12 mb-8 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Cost</h2>
                </div>

                <Accordion type="single" collapsible class="w-full max-w-4xl">
                    <AccordionItem v-for="faq in pricingFAQs" :key="faq.value" :value="faq.value">
                        <AccordionTrigger>{{ faq.question }}</AccordionTrigger>
                        <AccordionContent>{{ faq.answer }}</AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </main>
    </PublicLayout>
</template>
