<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
    user: Object,
})

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    status: props.user.status,
})
</script>

<template>
    <div class="p-6 max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Edit User</h1>

        <form @submit.prevent="form.put(`/admin/users/${user.id}`)" class="space-y-6">

            <!-- Name -->
            <div>
                <label class="block font-medium mb-1">Name</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="w-full border rounded px-3 py-2"
                />
                <div v-if="form.errors.name" class="text-red-600 text-sm">
                    {{ form.errors.name }}
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block font-medium mb-1">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="w-full border rounded px-3 py-2"
                />
                <div v-if="form.errors.email" class="text-red-600 text-sm">
                    {{ form.errors.email }}
                </div>
            </div>

            <!-- Role -->
            <div>
                <label class="block font-medium mb-1">Role</label>
                <select
                    v-model="form.role"
                    class="w-full border rounded px-3 py-2"
                >
                    <option value="user">User</option>
                    <option value="clerk">Clerk</option>
                    <option value="director">Director</option>
                    <option value="admin">Admin</option>
                </select>
                <div v-if="form.errors.role" class="text-red-600 text-sm">
                    {{ form.errors.role }}
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block font-medium mb-1">Status</label>
                <select
                    v-model="form.status"
                    class="w-full border rounded px-3 py-2"
                >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <div v-if="form.errors.status" class="text-red-600 text-sm">
                    {{ form.errors.status }}
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between">
                <Link
                    href="/admin/users"
                    class="text-gray-600 hover:underline"
                >
                    ← Back
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</template>
