<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  file: null,
  department_id: '',
  title: '',
  doc_no: '',
  doc_date: '',
  received_date: new Date().toISOString().slice(0, 10),
  purpose: 'inform', // inform | consider | notify
  notify_to: '',
  remark: '',
  attachments: [],
  use_esign: true,
  urgent: false,
})

const submit = () => {
  form.post(route('documents.external.store'))
}
</script>

<template>
  <AuthenticatedLayout>
    <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- LEFT: Main File -->
      <div class="lg:col-span-2 bg-white p-6 rounded shadow">
        <label class="block text-sm font-medium mb-2">
          📎 ไฟล์เอกสาร (PDF เท่านั้น)
        </label>
        <input
          type="file"
          accept="application/pdf"
          @change="e => form.file = e.target.files[0]"
          class="w-full border rounded p-2"
        />
      </div>

      <!-- RIGHT: Detail -->
      <div class="bg-white p-6 rounded shadow space-y-4">

        <select v-model="form.department_id" class="w-full border rounded p-2">
          <option value="">กรุณาเลือกหน่วยงานเจ้าของเรื่อง</option>
        </select>

        <input v-model="form.title" class="w-full border rounded p-2" placeholder="เรื่อง" />
        <input v-model="form.doc_no" class="w-full border rounded p-2" placeholder="เลขหนังสือ" />

        <input type="date" v-model="form.doc_date" class="w-full border rounded p-2" />
        <input type="date" v-model="form.received_date" class="w-full border rounded p-2" />

        <!-- Purpose -->
        <div>
          <label class="font-medium">เรียน</label>
          <div class="space-y-1 mt-1">
            <label><input type="radio" value="inform" v-model="form.purpose" /> เพื่อโปรดทราบ</label><br />
            <label><input type="radio" value="consider" v-model="form.purpose" /> เพื่อโปรดพิจารณา</label><br />
            <label><input type="radio" value="notify" v-model="form.purpose" /> เห็นควรแจ้ง</label>
          </div>
          <input
            v-if="form.purpose === 'notify'"
            v-model="form.notify_to"
            class="w-full border rounded p-2 mt-2"
            placeholder="ระบุหน่วยงาน / บุคคล"
          />
        </div>

        <textarea v-model="form.remark" class="w-full border rounded p-2" placeholder="ความคิดเห็นเพิ่มเติม" />

        <!-- Extra -->
        <div class="flex items-center space-x-4">
          <label><input type="checkbox" v-model="form.use_esign" /> ใช้ลายเซ็นอิเล็กทรอนิกส์</label>
          <label class="text-red-600">
            <input type="checkbox" v-model="form.urgent" /> เวียนด่วน
          </label>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-2 pt-4">
          <button class="px-4 py-2 bg-gray-200 rounded">บันทึก</button>
          <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
            ส่งดำเนินการ
          </button>
        </div>

      </div>
    </form>
  </AuthenticatedLayout>
</template>
