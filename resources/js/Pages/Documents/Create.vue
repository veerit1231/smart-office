<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  departments: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  title: '',
  department_id: '',
})

const submit = () => {
  form.post(route('documents.store'))
}
</script>

<template>
  <AppLayout title="สร้างเอกสารใหม่">
    <!-- ===== Top Header (Logo / Banner) ===== -->
    <div class="bg-white border-b">
      <div class="max-w-7xl mx-auto px-6 py-4 flex items-center gap-3">
      </div>
    </div>

    <!-- ===== Page Content ===== -->
    <div class="py-8">
      <div class="max-w-2xl mx-auto px-6">

        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-2xl font-bold text-gray-800">
            ➕ สร้างเอกสารใหม่
          </h1>

          <button
            type="button"
            @click="router.visit(route('documents.index'))"
            class="text-sm text-gray-600 hover:text-gray-900 underline"
          >
            ← กลับหน้าเอกสารทั้งหมด
          </button>
        </div>

        <!-- Card -->
        <div class="bg-white shadow rounded-lg p-6">
          <form @submit.prevent="submit" class="space-y-6">

            <!-- เรื่อง -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                เรื่อง
              </label>
              <input
                v-model="form.title"
                type="text"
                class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:outline-none"
                placeholder="ระบุเรื่องเอกสาร"
              />
              <p v-if="form.errors.title" class="text-red-600 text-sm mt-1">
                {{ form.errors.title }}
              </p>
            </div>

            <!-- หน่วยงาน -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                หน่วยงาน
              </label>
              <select
                v-model="form.department_id"
                class="w-full border rounded px-3 py-2 bg-white focus:ring focus:ring-blue-200 focus:outline-none"
              >
                <option value="">-- เลือกหน่วยงาน --</option>
                <option
                  v-for="dept in departments"
                  :key="dept.id"
                  :value="dept.id"
                >
                  {{ dept.name }}
                </option>
              </select>
              <p v-if="form.errors.department_id" class="text-red-600 text-sm mt-1">
                {{ form.errors.department_id }}
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t">
              <button
                type="button"
                @click="router.visit(route('documents.index'))"
                class="px-4 py-2 rounded border text-gray-700 hover:bg-gray-100"
              >
                ยกเลิก / กลับหน้าแรก
              </button>

              <button
                type="submit"
                :disabled="form.processing"
                class="px-6 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
              >
                บันทึกเอกสาร
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
