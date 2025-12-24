<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  departments: Array,
})

const showModal = ref(false)
const editing = ref(null)

const form = useForm({
  name: '',
  code: '',
  is_active: true,
})

function openCreate() {
  editing.value = null
  form.reset()
  showModal.value = true
}

function openEdit(dept) {
  editing.value = dept
  form.name = dept.name
  form.code = dept.code
  form.is_active = dept.is_active
  showModal.value = true
}

function submit() {
  if (editing.value) {
    form.put(route('departments.update', editing.value.id), {
      preserveScroll: true,
      onSuccess: () => showModal.value = false,
    })
  } else {
    form.post(route('departments.store'), {
      preserveScroll: true,
      onSuccess: () => showModal.value = false,
    })
  }
}

function destroyDept(dept) {
  if (confirm(`ลบหน่วยงาน "${dept.name}" ?`)) {
    router.delete(route('departments.destroy', dept.id), {
      preserveScroll: true,
    })
  }
}
</script>
<template>
<AppLayout title="หน่วยงาน">
  <div class="max-w-5xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold">หน่วยงาน</h1>
        <p class="text-sm text-gray-500">
          จัดการหน่วยงานภายในองค์กร
        </p>
      </div>

      <button
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        @click="openCreate"
      >
        + เพิ่มหน่วยงาน
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">ชื่อหน่วยงาน</th>
            <th class="px-4 py-3 text-left w-32">รหัส</th>
            <th class="px-4 py-3 text-left w-32">สถานะ</th>
            <th class="px-4 py-3 text-right w-40">จัดการ</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="dept in departments"
            :key="dept.id"
            class="border-t hover:bg-gray-50"
          >
            <td class="px-4 py-3 font-medium">
              {{ dept.name }}
            </td>

            <td class="px-4 py-3 text-gray-600">
              {{ dept.code || '-' }}
            </td>

            <td class="px-4 py-3">
              <span
                class="inline-flex px-2 py-1 text-xs rounded-full"
                :class="dept.is_active
                  ? 'bg-green-100 text-green-700'
                  : 'bg-gray-200 text-gray-600'"
              >
                {{ dept.is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
              </span>
            </td>

            <td class="px-4 py-3 text-right space-x-2">
              <button
                class="text-blue-600 hover:underline"
                @click="openEdit(dept)"
              >
                แก้ไข
              </button>
              <button
                class="text-red-600 hover:underline"
                @click="destroyDept(dept)"
              >
                ลบ
              </button>
            </td>
          </tr>

          <tr v-if="departments.length === 0">
            <td colspan="4" class="px-4 py-6 text-center text-gray-500">
              ยังไม่มีหน่วยงาน
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div
    v-if="showModal"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
  >
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
      <h2 class="text-lg font-bold mb-4">
        {{ editing ? 'แก้ไขหน่วยงาน' : 'เพิ่มหน่วยงาน' }}
      </h2>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">
            ชื่อหน่วยงาน
          </label>
          <input
            v-model="form.name"
            class="w-full border rounded px-3 py-2"
            placeholder="เช่น ฝ่ายเทคโนโลยีสารสนเทศ"
          />
          <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">
            {{ form.errors.name }}
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">
            รหัส
          </label>
          <input
            v-model="form.code"
            class="w-full border rounded px-3 py-2"
            placeholder="เช่น IT"
          />
        </div>

        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" v-model="form.is_active" />
          ใช้งานหน่วยงานนี้
        </label>
      </div>

      <div class="flex justify-end gap-2 mt-6">
        <button
          class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
          @click="showModal = false"
        >
          ยกเลิก
        </button>
        <button
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          :disabled="form.processing"
          @click="submit"
        >
          บันทึก
        </button>
      </div>
    </div>
  </div>
</AppLayout>
</template>
