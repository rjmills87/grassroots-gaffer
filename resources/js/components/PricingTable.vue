<script setup lang="ts">
import { defineProps } from 'vue';
import { Button } from './ui/button';

const props = defineProps({
    tiers: {
        type: Array as () => {
            name: string;
            price: string;
            description: string;
            idealFor: string;
            features: { feature: string; description: string }[];
        }[],
        required: true,
    },
});
</script>
<template>
    <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
        <div
            v-for="tier in props.tiers"
            :key="tier.name"
            class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
        >
            <div class="grid grid-cols-1 gap-8 p-8 lg:grid-cols-3 lg:gap-12 lg:p-12">
                <!-- Left Column: Info -->
                <div class="flex flex-col justify-between lg:col-span-1">
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl dark:text-white">{{ tier.name }}</h3>
                        <p class="mt-4 text-lg leading-6 text-gray-600 dark:text-gray-300">
                            {{ tier.description }}
                        </p>
                        <h4 class="mt-4 text-lg font-semibold text-gray-600 dark:text-gray-300">Ideal for:</h4>
                        <p class="mt-2 text-lg leading-6 text-gray-600 dark:text-gray-300">
                            {{ tier.idealFor }}
                        </p>
                    </div>
                    <div class="mt-8">
                        <Button class="cursor-pointer bg-teal-600" variant="default">Get Started</Button>
                    </div>
                </div>

                <!-- Right Column: Features & Price -->
                <div
                    class="flex flex-col justify-between border-t border-gray-200 pt-8 lg:col-span-2 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-12 dark:border-gray-700"
                >
                    <div>
                        <h4 class="text-sm font-semibold tracking-wide text-teal-600 uppercase dark:text-indigo-400">Key Features</h4>
                        <ul role="list" class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                            <li v-for="feature in tier.features" :key="feature.feature" class="flex gap-x-3">
                                <div class="mt-1 flex-none">
                                    <svg
                                        class="h-5 w-5 text-teal-600 dark:text-indigo-400"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ feature.feature }}</span>
                                    <span class="mt-0.5 block text-sm text-gray-500 dark:text-gray-400">{{ feature.description }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8 flex items-baseline gap-x-2 border-t border-gray-200 pt-8 dark:border-gray-700">
                        <span class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">{{ tier.price }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
