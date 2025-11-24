<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';

const generalFAQs = [
    {
        value: 'item-1',
        question: 'What is Grassroots Gaffer?',
        answer: 'Grassroots Gaffer is an ad-free, intuitive mobile-first web application designed specifically for grassroots football coaches to manage their teams, schedules, and communication simply and efficiently.',
    },
    {
        value: 'item-2',
        question: 'Is the app truly 100% ad-free?',
        answer: 'Yes. Unlike many competitors, we believe ads ruin the user experience, especially for parents and children. Our Free Tier has no ads, and our Gaffer Pro Tier has no ads.',
    },
    {
        value: 'item-3',
        question: 'What is the main difference between Grassroots Gaffer and other team management apps?',
        answer: 'We focus on providing a superior, clean, and fast user experience (UI/UX) without ad distractions. Our features are highly focused on the real-world administrative and logistical problems that grassroots football coaches face, with a simple, transparent pricing model.',
    },
    {
        value: 'item-4',
        question: 'Can parents use the app, or is it only for the coach?',
        answer: 'Both. The coach manages the team and events, but parents and guardians are the primary users for responding to availability and receiving announcements. The app is designed to be extremely easy for parents to use.',
    },
];

const pricingFAQs = [
    {
        value: 'item-5',
        question: 'Why is there a paid tier if the app is free to use?',
        answer: 'The Free Tier covers all essential communication and scheduling needs. The Gaffer Pro Tier unlocks powerful tools for the dedicated coach who runs multiple teams or needs advanced features to save administrative time, such as detailed attendance reports and the Session Planner.',
    },
    {
        value: 'item-6',
        question: 'How much does the Gaffer Pro Tier cost?',
        answer: 'It costs £6.99 per month, or you can get the best value with an annual subscription of £49.99 per year, which saves you over 40% compared to monthly billing.',
    },
    {
        value: 'item-7',
        question: 'Who pays for the Pro Tier?',
        answer: "Only the Coach or Manager needs to subscribe to Pro. Once the coach subscribes, all players and parents on that team automatically gain access to the coach's Pro features.",
    },
    {
        value: 'item-8',
        question: 'What is "Multi-Team Management" in the Pro Tier?',
        answer: 'This allows a coach to manage and switch between multiple independent teams (e.g., U10 Boys and U12 Girls) using a single account, keeping all data, rosters, and schedules separate and organised.',
    },
    {
        value: 'item-9',
        question: 'How does the "Parent-to-Parent Contact Sharing" work?',
        answer: 'This Pro feature allows parents who have opted in to share their contact details (e.g., phone number) with other parents on the same team. This is great for organising carpools or shared snack duties without the coach having to manually coordinate.',
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
                        Find answers to common questions about Grassroots Gaffer below. If you have a question that is not answered here, please
                        contact us at
                        <a href="mailto:hello@grassrootsgaffer.com">hello@grassrootsgaffer.com</a>.
                    </p>
                </div>
                <div class="mt-12 w-full max-w-4xl text-center">
                    <h2 class="mt-12 mb-8 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">General FAQs</h2>
                </div>

                <Accordion type="single" collapsible class="w-full max-w-4xl">
                    <AccordionItem v-for="faq in generalFAQs" :key="faq.value" :value="faq.value">
                        <AccordionTrigger>{{ faq.question }}</AccordionTrigger>
                        <AccordionContent>{{ faq.answer }}</AccordionContent>
                    </AccordionItem>
                </Accordion>

                <div class="mt-12 w-full max-w-4xl text-center">
                    <h2 class="mt-12 mb-8 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Pricing FAQs</h2>
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
