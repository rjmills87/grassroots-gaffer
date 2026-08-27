<script setup lang="ts">
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <PublicLayout>
        <!-- Hero Section -->
        <div class="relative isolate overflow-hidden bg-white dark:bg-gray-900">
            <div class="mx-auto max-w-3xl px-6 pt-16 pb-24 sm:pt-24 sm:pb-32 lg:px-8">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl dark:text-white">Know who's turning up on Saturday.</h1>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Squadra365 is for grassroots football coaches. Put the squad, the matches, the training, and the parent RSVPs in one place — then
                    nudge the people who have not replied. No ads. No charge for the core tools.
                </p>
                <p class="mt-4 text-base leading-7 text-gray-600 dark:text-gray-300">
                    It does not register players with the FA, and it does not replace Matchday or Club Portal. It sits beside them.
                </p>
                <div class="mt-10 flex flex-col items-start gap-4">
                    <div class="flex items-center gap-x-6">
                        <Link :href="route('register')">
                            <Button class="cursor-pointer bg-teal-600 hover:bg-teal-500" size="lg">Get started for free</Button>
                        </Link>
                        <Link :href="route('features')" class="text-sm leading-6 font-semibold text-gray-900 dark:text-white">
                            What it does today <span aria-hidden="true">→</span>
                        </Link>
                    </div>
                    <Link
                        :href="route('join.create')"
                        class="text-sm leading-6 font-semibold text-teal-700 hover:text-teal-600 dark:text-teal-400 dark:hover:text-teal-300"
                    >
                        Joining as a parent? Enter your team code <span aria-hidden="true">→</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Feature Grid Section -->
        <div class="py-24 sm:py-32 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base leading-7 font-semibold text-teal-600 dark:text-teal-400">What it does today</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                        The admin that used to live in the group chat
                    </p>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                        Teams, players, events, RSVPs, reminders, one-way announcements, and WhatsApp join codes. That is the product. Not a session
                        planner, not match stats, not a club system.
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-white">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-600">
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                                        />
                                    </svg>
                                </div>
                                Matches and training
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">Put Tuesday training and Sunday's match on the calendar. Location and times in one place.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-white">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-600">
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                Parent RSVPs
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">
                                    Parents tap in or out. You see who is available, then send a reminder to the ones who have not replied.
                                </p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-white">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-600">
                                    <svg
                                        class="h-6 w-6 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                                        />
                                    </svg>
                                </div>
                                No ads. Ever.
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">We do not put betting ads in front of the kids. The team stays on the football.</p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
