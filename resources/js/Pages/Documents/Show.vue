<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import StatusBadge from '@/Components/StatusBadge.vue'
import WorkflowActions from '@/Components/WorkflowActions.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  document: Object,
  canCancel: Boolean,
})


const page = usePage()
const user = page.props.auth.user

/* =======================
   permission
======================= */
const canSubmitDraft = computed(() => {
  if (props.document.status !== 'draft') return false
  if (user.role === 'admin') return true
  return props.document.created_by === user.id
})



/* =======================
   actions
======================= */
const submitDraft = () => {
  if (!confirm('เมื่อส่งเอกสารแล้วจะไม่สามารถแก้ไขได้ ต้องการส่งหรือไม่?')) {
    return
  }

router.post(route('documents.submit-user', props.document.id), {}, {
  preserveScroll: true,
})
}
const actionLabelMap = {
  created: 'สร้างเอกสาร',
  submit_user: 'ผู้ใช้ส่งเอกสาร',
  submit_clerk: 'เสนอผู้บริหาร',
  approve: 'อนุมัติ',
  reject: 'ตีกลับ',
  cancel: 'ยกเลิกเอกสาร',
  distribute: 'แจกจ่าย',
}

</script>

<template>
  <AppLayout title="รายละเอียดเอกสาร">
    <div class="p-6 max-w-5xl mx-auto space-y-6">

      <!-- ========================= -->
      <!-- Header -->
      <!-- ========================= -->
      <div class="bg-white rounded shadow p-4 space-y-2">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h1 class="text-xl font-bold flex items-center gap-3">
              📄 {{ document.title }}
              <StatusBadge :status="document.status" />
            </h1>

            <div class="text-sm text-gray-600 mt-1">
              เลขเอกสาร: {{ document.doc_no }}
            </div>
          </div>

          <div class="text-right text-sm text-gray-500">
            <div>ประเภท: {{ document.type }}</div>
            <div>
              สร้างเมื่อ:
              {{ new Date(document.created_at).toLocaleDateString('th-TH') }}
            </div>
          </div>
        </div>
      </div>

      <!-- ========================= -->
      <!-- Submit Draft (OWNER ONLY) -->
      <!-- ========================= -->
      <div
        v-if="canSubmitDraft"
        class="bg-white p-4 rounded shadow flex justify-end"
      >
        <button
          class="px-4 py-2 bg-blue-600 text-white rounded"
          @click="submitDraft"
        >
          ส่งเอกสาร
        </button>
      </div>

      <!-- ========================= -->
      <!-- Workflow Actions -->
      <!-- ========================= -->
      <WorkflowActions
  v-if="!['draft', 'cancelled'].includes(document.status)"
  :document="document"
  :can-cancel="canCancel"
/>
<div
  v-if="document.status === 'cancelled'"
  class="bg-red-50 border border-red-200 text-red-700 p-4 rounded"
>
  เอกสารฉบับนี้ถูกยกเลิกแล้ว ไม่สามารถดำเนินการต่อได้
</div>

      <!-- ========================= -->
      <!-- Timeline -->
      <!-- ========================= -->
      <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-4">ประวัติการดำเนินการ</h2>

        <ul class="relative space-y-6">
          <li
            v-for="(log, index) in document.logs"
            :key="log.id"
            class="relative pl-8"
          >
            <span
              class="absolute left-0 top-1 w-3 h-3 rounded-full bg-gray-400"
            />

            <span
              v-if="index !== document.logs.length - 1"
              class="absolute left-1.5 top-4 h-full w-px bg-gray-300"
            />

            <div class="bg-gray-50 rounded p-3">
              <div class="text-sm font-medium">
                {{ log.action }}
              </div>

              <div class="text-xs text-gray-500">
                โดย {{ log.user?.name ?? 'ระบบ' }}
                · {{ new Date(log.created_at).toLocaleString('th-TH') }}
              </div>

              <div
                v-if="log.remark"
                class="mt-1 text-sm text-gray-700 italic"
              >
                “{{ log.remark }}”
              </div>
            </div>
          </li>

          <li
            v-if="!document.logs || document.logs.length === 0"
            class="text-sm text-gray-500"
          >
            ยังไม่มีประวัติการดำเนินการ
          </li>
        </ul>
      </div>

    </div>
  </AppLayout>
</template>

