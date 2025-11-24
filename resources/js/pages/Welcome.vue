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
            <div class="mx-auto max-w-7xl px-6 pt-4 pb-24 sm:pb-32 lg:flex lg:px-8 lg:py-24">
                <div class="mx-auto max-w-2xl flex-shrink-0 lg:mx-0 lg:max-w-xl lg:pt-8">
                    <h1 class="mt-10 text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl dark:text-white">
                        Grassroots football management, <span class="text-teal-600 dark:text-teal-400">finally ad-free.</span>
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                        Stop battling with betting ads and clunky interfaces. Grassroots Gaffer is the simple, intuitive way to manage your team's
                        schedule and availability.
                    </p>
                    <div class="mt-10 flex items-center gap-x-6">
                        <Link :href="route('register')">
                            <Button class="cursor-pointer bg-teal-600 hover:bg-teal-500" size="lg">Get Started for Free</Button>
                        </Link>
                        <Link :href="route('features')" class="text-sm leading-6 font-semibold text-gray-900 dark:text-white">
                            Learn more <span aria-hidden="true">→</span>
                        </Link>
                    </div>
                </div>
                <!-- Right side graphic/placeholder -->
                <div class="mx-auto mt-16 flex max-w-2xl sm:mt-24 lg:mt-0 lg:mr-0 lg:ml-10 lg:max-w-none lg:flex-none xl:ml-32 xl:max-w-none">
                    <div class="xl max-w-3xl flex-none sm:max-w-5xl lg:max-w-4xl xl:max-w-6xl">
                        <div
                            class="-m-2 rounded-xl bg-gray-900/5 p-2 ring-1 ring-gray-900/10 ring-inset lg:-m-4 lg:rounded-2xl lg:p-4 dark:bg-white/5 dark:ring-white/10"
                        >
                            <img
                                src="https://placehold.co/2432x1442/1f2937/FFFFFF/png?text=App+Dashboard+Preview"
                                alt="App screenshot"
                                width="1280"
                                height="720"
                                class="w-[76rem] rounded-md shadow-2xl ring-1 ring-gray-900/10 dark:ring-white/10"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Grid Section -->
        <div class="py-24 sm:py-32 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base leading-7 font-semibold text-teal-600 dark:text-teal-400">Deploy faster</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                        Everything you need to manage your squad
                    </p>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                        We focus on the features that actually matter to grassroots coaches, without the bloat.
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
                                Smart Scheduling
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">
                                    Create matches and training sessions in seconds. Locations, times, and details all in one place.
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
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                Instant Availability
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">
                                    Parents can respond with a single click. Coaches get a live dashboard of who is in and who is out.
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
                                Zero Ads. Ever.
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">
                                    We don't sell user data or bombard you with betting adverts. Your team's focus stays on the game.
                                </p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
