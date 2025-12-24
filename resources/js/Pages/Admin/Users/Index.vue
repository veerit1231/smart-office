<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
    users: Array,
})
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">User Management</h1>

        <div class="mb-4">
            <a href="/admin/users/create" class="inline-block px-4 py-2 bg-blue-600 text-white rounded">
                + Create User
            </a>
        </div>

        <table class="min-w-full border text-sm">
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td class="border px-3 py-2">{{ user.name }}</td>

                    <td class="border px-3 py-2">{{ user.email }}</td>

                    <td class="border px-3 py-2 capitalize">
                        {{ user.role }}
                    </td>

                    <!-- ✅ Status -->
                    <td class="border px-3 py-2 text-center">
                        <span class="px-2 py-1 rounded text-xs font-semibold" :class="user.status === 'active'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-red-100 text-red-700'">
                            {{ user.status }}
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="border px-3 py-2 text-center">
                        <a :href="`/admin/users/${user.id}/edit`" class="text-blue-600 hover:underline mr-2">
                            Edit
                        </a>

                        <a :href="`/admin/users/${user.id}`" class="text-gray-600 hover:underline">
                            View
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
