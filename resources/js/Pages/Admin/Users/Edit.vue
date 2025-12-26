<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

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
  <Head title="แก้ไขผู้ใช้งาน" />

  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

      <!-- Header (เหมือน Create) -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
        <h2 class="text-lg font-semibold text-white">
          แก้ไขผู้ใช้งาน
        </h2>
        <p class="text-sm text-blue-100 mt-1">
          แก้ไขข้อมูลและสิทธิ์การใช้งานในระบบ
        </p>
      </div>

      <!-- Form -->
      <form
        @submit.prevent="form.put(`/admin/users/${user.id}`)"
        class="p-6 space-y-6 bg-gray-50"
      >

        <!-- Name -->
        <div>
          <label class="form-label">ชื่อ-สกุล</label>
          <input
            v-model="form.name"
            type="text"
            class="form-input"
          />
          <p v-if="form.errors.name" class="form-error">
            {{ form.errors.name }}
          </p>
        </div>

        <!-- Email -->
        <div>
          <label class="form-label">อีเมล</label>
          <input
            v-model="form.email"
            type="email"
            class="form-input"
          />
          <p v-if="form.errors.email" class="form-error">
            {{ form.errors.email }}
          </p>
        </div>

        <!-- Role -->
        <div>
          <label class="form-label">สิทธิ์การใช้งาน</label>
          <select v-model="form.role" class="form-input">
            <option value="user">User</option>
            <option value="clerk">Clerk</option>
            <option value="director">Director</option>
            <option value="admin">Admin</option>
          </select>
          <p v-if="form.errors.role" class="form-error">
            {{ form.errors.role }}
          </p>
        </div>

        <!-- Status -->
        <div>
          <label class="form-label">สถานะผู้ใช้งาน</label>
          <select v-model="form.status" class="form-input">
            <option value="active">ใช้งาน</option>
            <option value="inactive">ปิดการใช้งาน</option>
          </select>
          <p v-if="form.errors.status" class="form-error">
            {{ form.errors.status }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
          <Link
            href="/admin/users"
            class="text-sm text-gray-600 hover:underline"
          >
            ← กลับไปหน้ารายชื่อ
          </Link>

          <button
            type="submit"
            :disabled="form.processing"
            class="px-6 py-2 rounded-md bg-blue-600 text-white
                   hover:bg-blue-700 disabled:opacity-50 shadow"
          >
            บันทึกการแก้ไข
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped>
.form-label {
  @apply block text-sm font-medium text-gray-700 mb-1;
}

.form-input {
  @apply w-full rounded-md border border-gray-300 bg-white px-3 py-2
         focus:border-blue-500 focus:ring focus:ring-blue-200;
}

.form-error {
  @apply text-sm text-red-500 mt-1;
}
</style>
