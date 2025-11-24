<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);

const navigation = [
    { name: 'Home', href: '/', routeName: 'home' },
    { name: 'Features', href: '/features', routeName: 'features' },
    { name: 'Pricing', href: '/pricing', routeName: 'pricing' },
    { name: 'FAQ', href: '/faq', routeName: 'faq' },
];
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-gray-100 bg-white dark:border-gray-800 dark:bg-gray-900">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between p-2">
                <!-- Logo -->
                <div class="flex shrink-0 items-center">
                    <Link :href="route('home')">
                        <AppLogo class="block h-9 w-auto" />
                    </Link>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm leading-5 font-medium text-gray-500 transition duration-150 ease-in-out hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:text-gray-300"
                        :class="{
                            'border-indigo-500 text-gray-900 dark:border-indigo-600 dark:text-gray-100': route().current(item.routeName),
                        }"
                    >
                        {{ item.name }}
                    </Link>
                </div>

                <!-- Right Side Buttons -->
                <div class="hidden gap-4 sm:ml-6 sm:flex sm:items-center">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')">
                            <Button>Dashboard</Button>
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                        >
                            Log in
                        </Link>
                        <Link :href="route('register')">
                            <Button class="cursor-pointer bg-teal-600">Get Started</Button>
                        </Link>
                    </template>
                </div>

                <!-- Hamburger (Mobile) -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div
            :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
            class="border-t border-gray-200 sm:hidden dark:border-gray-800"
        >
            <div class="space-y-1 pt-2 pb-3">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="block border-l-4 border-transparent py-2 pr-4 pl-3 text-base font-medium text-gray-600 transition duration-150 ease-in-out hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                >
                    {{ item.name }}
                </Link>
            </div>

            <!-- Mobile Auth Buttons -->
            <div class="border-t border-gray-200 pt-4 pb-4 dark:border-gray-600">
                <div class="flex flex-col space-y-3 px-4">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="w-full">
                            <Button class="w-full">Dashboard</Button>
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="block w-full py-2 text-center text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                        >
                            Log in
                        </Link>
                        <Link :href="route('register')" class="w-full">
                            <Button class="w-full">Get Started</Button>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>
