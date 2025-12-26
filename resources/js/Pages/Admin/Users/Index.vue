<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout,
})

defineProps({
  users: Array,
})
</script>

<template>
  <div class="max-w-6xl mx-auto">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-xl font-semibold text-gray-800">
          จัดการผู้ใช้งาน
        </h1>
        <p class="text-sm text-gray-500 mt-1">
          รายชื่อผู้ใช้งานทั้งหมดในระบบ
        </p>
      </div>

      <Link
        href="/admin/users/create"
        class="inline-flex items-center gap-2 px-4 py-2
               bg-blue-600 text-white rounded-md
               hover:bg-blue-700 shadow"
      >
        + เพิ่มผู้ใช้งาน
      </Link>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

      <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="px-4 py-3 text-left font-medium">ชื่อ</th>
            <th class="px-4 py-3 text-left font-medium">อีเมล</th>
            <th class="px-4 py-3 text-left font-medium">สิทธิ์</th>
            <th class="px-4 py-3 text-center font-medium">สถานะ</th>
            <th class="px-4 py-3 text-center font-medium">จัดการ</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
            class="border-t hover:bg-gray-50 transition"
          >
            <td class="px-4 py-3 font-medium text-gray-800">
              {{ user.name }}
            </td>

            <td class="px-4 py-3 text-gray-600">
              {{ user.email }}
            </td>

            <td class="px-4 py-3 capitalize">
              <span
                class="px-2 py-1 rounded-md text-xs font-semibold"
                :class="{
                  'bg-red-100 text-red-700': user.role === 'admin',
                  'bg-blue-100 text-blue-700': user.role === 'clerk',
                  'bg-purple-100 text-purple-700': user.role === 'director',
                  'bg-gray-100 text-gray-700': user.role === 'user',
                }"
              >
                {{ user.role }}
              </span>
            </td>

            <td class="px-4 py-3 text-center">
              <span
                class="px-2 py-1 rounded-full text-xs font-semibold"
                :class="user.status === 'active'
                  ? 'bg-green-100 text-green-700'
                  : 'bg-red-100 text-red-700'"
              >
                {{ user.status === 'active' ? 'ใช้งาน' : 'ปิดใช้งาน' }}
              </span>
            </td>

            <td class="px-4 py-3 text-center space-x-3">
              <Link
                :href="`/admin/users/${user.id}/edit`"
                class="text-blue-600 hover:underline"
              >
                แก้ไข
              </Link>

              <Link
                :href="`/admin/users/${user.id}`"
                class="text-gray-600 hover:underline"
              >
                ดู
              </Link>
            </td>
          </tr>

          <tr v-if="users.length === 0">
            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
              ยังไม่มีผู้ใช้งานในระบบ
            </td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>
</template>
