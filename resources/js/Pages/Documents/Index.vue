<script setup>
/* =======================
   Imports
======================= */
import { router, usePage, useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import DistributeModal from '@/Components/DistributeModal.vue'
import debounce from 'lodash/debounce'
import { Link } from '@inertiajs/vue3'
import { reactive } from 'vue'

import dayjs from 'dayjs'
import 'dayjs/locale/th'

dayjs.locale('th')


defineOptions({ layout: AppLayout })

/* =======================
   Props
======================= */
const props = defineProps({
  documents: Object,
  summary: Object,
  users: Array,
  departments: Array,
  statuses: Object,   // 👈 แก้ตรงนี้

  // ✅ ADD
  filters: {
    type: Object,
    default: () => ({}),
  },
})


/* =======================
   Reactive Departments
======================= */
// ❗ clone จาก props เพื่อให้ reactive
const departmentOptions = ref([...props.departments])

// sync เมื่อ Inertia reload เฉพาะ departments
watch(
  () => props.departments,
  (newVal) => {
    departmentOptions.value = [...newVal]
  }
)

/* =======================
   Auth / Role
======================= */
const page = usePage()
const role = computed(() => page.props.auth.user.role)

const filterForm = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
  department: props.filters.department || '',
  mine: props.filters.mine || false,
})

// auto reload เมื่อ filter เปลี่ยน
watch(
  filterForm,
  debounce(() => {
    router.get(
      route('documents.index'),
      filterForm.value,
      {
        preserveState: true,
        replace: true,
      }
    )
  }, 400),
  { deep: true }
)

/* =======================
   Navigation
======================= */
const openDoc = (id) => {
  if (id) router.visit(route('documents.show', id))
}

/* =======================
   Incoming Modal
======================= */
const showIncomingModal = ref(false)

const incomingForm = useForm({
  file: null,
  title: '',
  department_id: null,
  doc_date: '',
  received_date: '',
  purpose: 'inform',
  notify_to: '',
  urgent: false,
  use_esign: true,
})

const submitIncoming = () => {
  incomingForm.post(route('documents.incoming.store'), {
    forceFormData: true,
    onSuccess: () => {
      showIncomingModal.value = false
      incomingForm.reset()
    },
  })
}

/* =======================
   Department Modal
======================= */
const showDepartmentModal = ref(false)

const departmentForm = useForm({
  name: '',
})

const submitDepartment = () => {
  departmentForm.post(route('departments.store'), {
    preserveScroll: true,
    onSuccess: () => {
      showDepartmentModal.value = false
      departmentForm.reset()

      // ⭐ reload เฉพาะ departments
      router.reload({
        only: ['departments'],
      })
    },
  })
}

/* =======================
   Distribute Modal
======================= */
const selectedDocument = ref(null)
const showDistributeModal = ref(false)

const openDistribute = (doc) => {
  selectedDocument.value = doc
  showDistributeModal.value = true
}

const closeDistribute = () => {
  selectedDocument.value = null
  showDistributeModal.value = false
}

</script>

<template>
  <div class="space-y-6">

    <!-- ➕ รับเอกสารภายนอก -->
    <div class="flex justify-end">
      <button v-if="role === 'clerk'" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        @click="showIncomingModal = true">
        ➕ รับเอกสารภายนอก
      </button>
    </div>
    <!-- 🎛 Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border p-5">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

        <div>
          <label class="text-xs text-gray-500 mb-1 block">ค้นหา</label>
          <input v-model="filterForm.search" placeholder="เลขเอกสาร / เรื่อง" class="input" />
        </div>

        <div>
          <label class="text-xs text-gray-500 mb-1 block">สถานะ</label>
          <select v-model="filterForm.status" class="input">
            <option value="">ทุกสถานะ</option>
            <option v-for="(label, key) in statuses" :key="key" :value="key">
              {{ label }}
            </option>
          </select>
        </div>

        <div>
          <label class="text-xs text-gray-500 mb-1 block">หน่วยงาน</label>
          <select v-model="filterForm.department" class="input">
            <option value="">ทุกหน่วยงาน</option>
            <option v-for="d in departments" :key="d.id" :value="d.id">
              {{ d.name }}
            </option>
          </select>
        </div>

        <div class="flex justify-end">
          <button v-if="filterForm.search || filterForm.status || filterForm.department"
            class="text-sm text-gray-500 hover:text-red-600"
            @click="filterForm = { search: '', status: '', department: '', mine: false }">
            ล้างตัวกรอง
          </button>
        </div>

      </div>
    </div>
    <!-- 📄 Table -->
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr class="text-gray-600">
          <th class="px-4 py-3 text-left w-28">เลขที่</th>
          <th class="px-4 py-3 text-left">เรื่อง</th>
          <th class="px-4 py-3 text-left w-48">หน่วยงาน</th>
          <th class="px-3 py-2 text-left w-48">ประเภท</th>
          <th class="px-3 py-2 text-left w-40">วันที่สร้าง</th>
          <th class="px-4 py-3 text-left w-40">สถานะ</th>
          <th class="px-4 py-3 text-right w-32">การดำเนินการ</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="doc in documents.data" :key="doc.id" class="border-b last:border-0 hover:bg-gray-50 transition">
          <td class="px-4 py-3 font-mono text-indigo-700">
            <span v-if="doc.doc_no">
              {{ String(doc.doc_no).padStart(3, '0') }}
            </span>
            <span v-else class="text-gray-400 italic">รอออกเลข</span>
          </td>

          <td class="px-4 py-3">
            <div class="font-medium text-gray-800 max-w-xl line-clamp-2" :title="doc.title">
              {{ doc.title }}
            </div>
          </td>

          <td class="px-4 py-3 text-gray-600">
            {{ doc.department?.name || '-' }}
          </td>
          <td class="px-3 py-2">
            <span class="px-2 py-1 text-xs rounded-full" :class="doc.type === 'incoming'
              ? 'bg-purple-100 text-purple-700'
              : 'bg-blue-100 text-blue-700'">
              {{ doc.type === 'incoming' ? 'รับนอก' : 'รับใน' }}
            </span>
          </td>
          <td class="px-3 py-2 text-sm text-gray-600">
            {{ doc.document_date
              ? dayjs(doc.document_date).format('DD/MM/YYYY')
            : '-' }}
          </td>
          <td class="px-4 py-3">
            <StatusBadge :status="doc.status" />
          </td>

          <td class="px-4 py-3 text-right">
  <Link
    :href="route('documents.show', doc.id)"
    class="inline-flex items-center gap-1 px-3 py-1.5
           rounded-md text-sm font-medium
           bg-indigo-600 text-white
           hover:bg-indigo-700
           focus:outline-none focus:ring-2 focus:ring-indigo-400
           transition"
  >
    🔍 เปิด
  </Link>
</td>

        </tr>
      </tbody>
    </table>
    <!-- 🔢 Pagination (ใส่ตรงนี้) -->
    <div v-if="documents.links" class="flex justify-between items-center mt-4 text-sm text-gray-500">
      <div>
        แสดง {{ documents.from }}–{{ documents.to }}
        จาก {{ documents.total }} รายการ
      </div>

      <div class="flex gap-1">
        <button v-for="link in documents.links" :key="link.label" v-html="link.label" :disabled="!link.url"
          class="px-3 py-1 rounded border text-sm" :class="{
            'bg-indigo-600 text-white': link.active,
            'text-gray-400': !link.url
          }" @click="link.url && router.visit(link.url, { preserveState: true })" />
      </div>
    </div>


    <!-- 📥 Incoming Modal -->
    <div v-if="showIncomingModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white w-full max-w-xl rounded p-6 space-y-4">
        <h2 class="text-lg font-bold">📥 รับเอกสารภายนอก</h2>

        <input type="file" @change="e => incomingForm.file = e.target.files[0]" />

        <input v-model="incomingForm.title" placeholder="เรื่อง" class="input" />

        <div class="space-y-1">
          <div class="flex justify-between items-center">
            <label class="text-sm font-medium">หน่วยงานต้นทาง</label>
            <button type="button" class="text-xs text-blue-600 hover:underline" @click="showDepartmentModal = true">
              ➕ เพิ่มหน่วยงาน
            </button>
          </div>

          <select v-model.number="incomingForm.department_id" class="input">
            <option value="">-- เลือกหน่วยงาน --</option>
            <option v-for="dept in departmentOptions" :key="dept.id" :value="dept.id">
              {{ dept.name }}
            </option>
          </select>
        </div>
        <div class="flex items-center gap-2">
          <input type="checkbox" v-model="filterForm.mine"
            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
          <span class="text-sm text-gray-700">เอกสารของฉัน</span>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <input type="date" v-model="incomingForm.doc_date" class="input" />
          <input type="date" v-model="incomingForm.received_date" class="input" />
        </div>

        <select v-model="incomingForm.purpose" class="input">
          <option value="inform">แจ้งเพื่อทราบ</option>
          <option value="consider">พิจารณา</option>
          <option value="notify">แจ้งดำเนินการ</option>
        </select>

        <input v-model="incomingForm.notify_to" placeholder="แจ้งถึง (ถ้ามี)" class="input" />

        <div class="flex gap-4">
          <label><input type="checkbox" v-model="incomingForm.urgent" /> ด่วน</label>
          <label><input type="checkbox" v-model="incomingForm.use_esign" /> ใช้ e-Sign</label>
        </div>

        <div class="flex justify-end gap-2">
          <button @click="showIncomingModal = false">ยกเลิก</button>
          <button class="px-4 py-2 bg-green-600 text-white rounded" :disabled="incomingForm.processing"
            @click="submitIncoming">
            บันทึก
          </button>
        </div>

        <div v-if="incomingForm.errors" class="text-red-500 text-sm">
          <div v-for="(err, key) in incomingForm.errors" :key="key">
            {{ err }}
          </div>
        </div>
      </div>
    </div>

    <!-- 🏢 Department Modal -->
    <div v-if="showDepartmentModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="bg-white w-full max-w-sm rounded p-6 space-y-4">
        <h2 class="text-lg font-bold">➕ เพิ่มหน่วยงานต้นทาง</h2>

        <input v-model="departmentForm.name" placeholder="ชื่อหน่วยงาน" class="input" />

        <div class="flex justify-end gap-2">
          <button @click="showDepartmentModal = false">ยกเลิก</button>
          <button class="px-4 py-2 bg-blue-600 text-white rounded" :disabled="departmentForm.processing"
            @click="submitDepartment">
            บันทึก
          </button>
        </div>

        <div v-if="departmentForm.errors.name" class="text-red-500 text-sm">
          {{ departmentForm.errors.name }}
        </div>
      </div>
    </div>

    <!-- 📦 Distribute Modal -->
    <DistributeModal v-if="showDistributeModal && selectedDocument" :document="selectedDocument" :users="users"
      @close="closeDistribute" />
  </div>
</template>

<style scoped>
.input {
  @apply w-full border rounded px-3 py-2;
}
</style>
