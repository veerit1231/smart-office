<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  roles: Array,
})

const roles = computed(() => props.roles || [])

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
})

const submit = () => {
  form.post(route('admin.users.store'))
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="w-full max-w-lg bg-white rounded-lg shadow p-6">

      <h1 class="text-2xl font-bold mb-6">
        Create User
      </h1>

      <form @submit.prevent="submit" class="space-y-5">

        <!-- Name -->
        <div>
          <label class="block text-sm font-medium mb-1">
            Name
          </label>
          <input
            v-model="form.name"
            class="w-full rounded border px-3 py-2 focus:ring focus:ring-blue-200"
          />
          <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
            {{ form.errors.name }}
          </p>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium mb-1">
            Email
          </label>
          <input
            v-model="form.email"
            type="email"
            class="w-full rounded border px-3 py-2 focus:ring focus:ring-blue-200"
          />
          <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
            {{ form.errors.email }}
          </p>
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium mb-1">
            Password
          </label>
          <input
            v-model="form.password"
            type="password"
            class="w-full rounded border px-3 py-2"
          />
        </div>

        <!-- Confirm -->
        <div>
          <label class="block text-sm font-medium mb-1">
            Confirm Password
          </label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="w-full rounded border px-3 py-2"
          />
        </div>

        <!-- Role -->
        <div>
          <label class="block text-sm font-medium mb-1">
            Role
          </label>
          <select
            v-model="form.role"
            class="w-full rounded border px-3 py-2"
          >
            <option value="">-- Select Role --</option>
            <option v-for="role in roles" :key="role" :value="role">
              {{ role }}
            </option>
          </select>
          <p v-if="form.errors.role" class="text-red-500 text-sm mt-1">
            {{ form.errors.role }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2 pt-4">
          <Link
            href="/admin/users"
            class="px-4 py-2 rounded border text-gray-600 hover:bg-gray-100"
          >
            Cancel
          </Link>

          <button
            type="submit"
            class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
            :disabled="form.processing"
          >
            Save
          </button>
        </div>

      </form>
    </div>
  </div>
</template>
