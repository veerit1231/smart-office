<script setup>
import { ref } from 'vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import { Link } from '@inertiajs/vue3'

const showingNavigationDropdown = ref(false)
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Top Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left -->
                    <div class="flex items-center space-x-6">
                        <Link
                            :href="route('documents.index')"
                            class="font-semibold text-lg text-gray-800"
                        >
                            Smart Office
                        </Link>

                        <Link
                            :href="route('documents.index')"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            Documents
                        </Link>
                    </div>

                    <!-- Right -->
                    <div class="hidden sm:flex sm:items-center">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
                                >
                                    {{ $page.props.auth.user.name }}
                                    <svg
                                        class="ms-2 h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <!-- ❌ ตัด Profile ออก -->
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>

                    <!-- Mobile -->
                    <div class="flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="p-2 text-gray-500"
                        >
                            ☰
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div
                v-show="showingNavigationDropdown"
                class="sm:hidden border-t border-gray-200"
            >
                <div class="p-4 space-y-2">
                    <ResponsiveNavLink :href="route('documents.index')">
                        Documents
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                    >
                        Log Out
                    </ResponsiveNavLink>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="p-6">
            <slot />
        </main>
    </div>
</template>
