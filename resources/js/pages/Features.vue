<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';

const features = [
    {
        name: 'Squad and guardians',
        description:
            'Keep players and parent details in one place. Share a WhatsApp join code so parents add themselves, or add players yourself. A second parent can join the same child.',
        icon: 'UsersIcon',
    },
    {
        name: 'Matches and training',
        description: 'Create matches, training, and other team events. Set the location and times so everyone knows where to be.',
        icon: 'CalendarIcon',
    },
    {
        name: 'Parent RSVPs',
        description: 'Stop chasing the group chat. Parents respond with one tap, and you get a clear list of who is available.',
        icon: 'CheckCircleIcon',
    },
    {
        name: 'Reminders',
        description: 'Send a nudge to guardians who have not replied yet. Reminders are part of the free core, not a paid extra.',
        icon: 'BellIcon',
    },
    {
        name: 'One-way announcements',
        description: 'Post updates to the whole squad. Pitch notes, kit colour, meet time — the coach writes, the parents read.',
        icon: 'MegaphoneIcon',
    },
    {
        name: 'Ad-free and private',
        description: 'No betting ads. Team data stays with you. Built for youth football, not for selling eyeballs.',
        icon: 'ShieldCheckIcon',
    },
];
</script>

<template>
    <PublicLayout>
        <Head title="Features" />
        <main class="dark:bg-[#0a0a0a]">
            <div class="flex w-full flex-col items-center px-6 py-24 sm:py-32">
                <div class="mx-auto max-w-7xl">
                    <div class="mx-auto max-w-2xl lg:text-center">
                        <h2 class="text-base leading-7 font-semibold text-teal-600 dark:text-teal-400">Built for coaches</h2>
                        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">What you can do today</p>
                        <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                            The coach tools are free: teams, players and guardians, events, RSVPs, reminders, one-way announcements, and join codes.
                            Squadra365 sits beside Matchday and Club Portal. It does not register anyone with the FA.
                        </p>
                    </div>
                    <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                        <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                            <div v-for="feature in features" :key="feature.name" class="flex flex-col">
                                <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-white">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-600">
                                        <svg
                                            v-if="feature.icon === 'UsersIcon'"
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
                                            />
                                        </svg>
                                        <svg
                                            v-else-if="feature.icon === 'CalendarIcon'"
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                                            />
                                        </svg>
                                        <svg
                                            v-else-if="feature.icon === 'CheckCircleIcon'"
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <svg
                                            v-else-if="feature.icon === 'BellIcon'"
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
                                            />
                                        </svg>
                                        <svg
                                            v-else-if="feature.icon === 'MegaphoneIcon'"
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.43.816 1.035.816 1.73 0 .695-.32 1.3-.816 1.73"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            class="h-6 w-6 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"
                                            />
                                        </svg>
                                    </div>
                                    {{ feature.name }}
                                </dt>
                                <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                    <p class="flex-auto">{{ feature.description }}</p>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <p class="mx-auto mt-16 max-w-2xl text-center text-base leading-7 text-gray-600 dark:text-gray-300">
                        Coming later, if we need it: recurring training, and a one-tap call-off or venue change that tells the parents. Not a session
                        planner, lineup tool, or match stats pack.
                    </p>
                </div>
            </div>
        </main>
    </PublicLayout>
</template>
