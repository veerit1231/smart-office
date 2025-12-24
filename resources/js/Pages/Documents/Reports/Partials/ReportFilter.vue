<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  filters: Object,
  departments: Array,
})

const filter = ref({
  type: props.filters.type || '',
  department: props.filters.department || '',
  status: props.filters.status || '',
  from: props.filters.from || '',
  to: props.filters.to || '',
})

const apply = () => {
  router.get('/reports/documents', filter.value, {
    preserveState: true,
    replace: true,
  })
}

const reset = () => {
  filter.value = {
    type: '',
    department: '',
    status: '',
    from: '',
    to: '',
  }
  apply()
}
</script>

<template>
  <div class="bg-white p-4 rounded shadow grid grid-cols-1 md:grid-cols-5 gap-4">

    <select v-model="filter.type" class="border rounded px-2 py-1">
      <option value="">ทุกประเภท</option>
      <option value="incoming">เอกสารรับนอก</option>
      <option value="internal">เอกสารรับใน</option>
    </select>

    <select v-model="filter.department" class="border rounded px-2 py-1">
      <option value="">ทุกกลุ่มงาน</option>
      <option
        v-for="d in departments"
        :key="d.id"
        :value="d.id"
      >
        {{ d.name }}
      </option>
    </select>

    <select v-model="filter.status" class="border rounded px-2 py-1">
      <option value="">ทุกสถานะ</option>
      <option value="received">รับแล้ว</option>
      <option value="waiting_director">รอผอ.</option>
      <option value="approved">อนุมัติ</option>
      <option value="rejected">ไม่อนุมัติ</option>
      <option value="distributed">แจกจ่ายแล้ว</option>
    </select>

    <input type="date" v-model="filter.from" class="border rounded px-2 py-1" />
    <input type="date" v-model="filter.to" class="border rounded px-2 py-1" />

    <div class="flex gap-2 md:col-span-5">
      <button
        @click="apply"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        🔍 ค้นหา
      </button>

      <button
        @click="reset"
        class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300"
      >
        ♻ ล้างค่า
      </button>
    </div>

  </div>
</template>
