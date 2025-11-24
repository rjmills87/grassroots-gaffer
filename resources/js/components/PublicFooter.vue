<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogo from './AppLogo.vue';
</script>

<template>
    <footer>
        <div class="mx-auto flex max-w-7xl justify-between border-t border-gray-200 p-6 px-4 pt-8 sm:px-6 lg:px-8 dark:border-gray-700">
            <div>
                <AppLogo />
            </div>
            <div>
                <ul class="flex flex-col gap-2">
                    <li>
                        <Link
                            class="text-gray-600 transition-colors hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-gray-300"
                            href="/"
                            >Home</Link
                        >
                    </li>
                    <li>
                        <Link
                            class="text-gray-600 transition-colors hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-gray-300"
                            href="/features"
                            >Features</Link
                        >
                    </li>
                    <li>
                        <Link
                            class="text-gray-600 transition-colors hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-gray-300"
                            href="/pricing"
                            >Pricing</Link
                        >
                    </li>
                    <li>
                        <Link
                            class="text-gray-600 transition-colors hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-gray-300"
                            href="/faq"
                            >FAQ</Link
                        >
                    </li>
                </ul>
            </div>
            <div>
                <ul class="flex flex-col gap-2">
                    <li>
                        <Link
                            class="text-gray-600 transition-colors hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-gray-300"
                            href="/terms"
                            >Terms & Conditions</Link
                        >
                    </li>
                    <li>
                        <Link
                            class="text-gray-600 transition-colors hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-gray-300"
                            href="/privacy"
                            >Privacy Policy</Link
                        >
                    </li>
                    <li>
                        <Link
                            class="text-gray-600 transition-colors hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-gray-300"
                            href="/cookies"
                            >Cookie Policy</Link
                        >
                    </li>
                </ul>
            </div>
        </div>
        <div class="p-6 text-center text-gray-600 dark:text-gray-400">
            <p>&copy; {{ new Date().getFullYear() }} Grassroots Gaffer. All rights reserved.</p>
        </div>
    </footer>
</template>
