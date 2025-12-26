<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link, Head } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: AdminLayout })

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
  form.post(route('admin.users.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="เพิ่มผู้ใช้งาน" />

  <div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
        <h2 class="text-lg font-semibold text-white">
          เพิ่มผู้ใช้งาน
        </h2>
        <p class="text-sm text-blue-100 mt-1">
          กำหนดข้อมูลผู้ใช้และสิทธิ์การใช้งาน
        </p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="p-6 space-y-6 bg-gray-50">

        <!-- Name -->
        <div>
          <label class="form-label">ชื่อ-สกุล</label>
          <input
            v-model="form.name"
            class="form-input"
            placeholder="เช่น นายสมชาย ใจดี"
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
            placeholder="example@company.com"
          />
          <p v-if="form.errors.email" class="form-error">
            {{ form.errors.email }}
          </p>
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="form-label">รหัสผ่าน</label>
            <input
              v-model="form.password"
              type="password"
              class="form-input"
            />
            <p v-if="form.errors.password" class="form-error">
              {{ form.errors.password }}
            </p>
          </div>

          <div>
            <label class="form-label">ยืนยันรหัสผ่าน</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              class="form-input"
            />
            <p v-if="form.errors.password_confirmation" class="form-error">
              {{ form.errors.password_confirmation }}
            </p>
          </div>
        </div>

        <!-- Role -->
        <div>
          <label class="form-label">สิทธิ์การใช้งาน</label>
          <select v-model="form.role" class="form-input">
            <option value="">-- เลือกสิทธิ์ --</option>
            <option v-for="role in roles" :key="role" :value="role">
              {{ role }}
            </option>
          </select>
          <p v-if="form.errors.role" class="form-error">
            {{ form.errors.role }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
          <Link
            href="/admin/users"
            class="px-4 py-2 rounded-md border bg-white text-gray-600 hover:bg-gray-100"
          >
            ยกเลิก
          </Link>

          <button
            type="submit"
            class="px-6 py-2 rounded-md bg-blue-600 text-white
                   hover:bg-blue-700 disabled:opacity-50 shadow"
            :disabled="form.processing"
          >
            บันทึก
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
