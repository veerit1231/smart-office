<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const showModal = ref(false)

const closeModal = () => {
  showModal.value = false
}

const props = defineProps({
  document: Object,
  canCancel: Boolean,
})


const page = usePage()
const user = page.props.auth.user

const remark = ref('')
const loading = ref(false)

/* =======================
   role helpers
======================= */
const isClerk = computed(() => user.role === 'clerk')
const isDirector = computed(() => user.role === 'director')

/* =======================
   permissions
======================= */
const canSendToDirector = computed(() =>
  props.document.status === 'received' &&
  ['clerk', 'admin'].includes(user.role)
)

const canApprove = computed(() =>
  props.document.status === 'waiting_director' &&
  ['director', 'admin'].includes(user.role)
)

const canReject = computed(() =>
  props.document.status === 'waiting_director' &&
  ['director', 'admin'].includes(user.role)
)

const canDistribute = computed(() =>
  props.document.status === 'approved' &&
  ['clerk', 'admin'].includes(user.role)
)
const confirmCancel = () => {
  if (!remark.value.trim()) {
    alert('กรุณากรอกเหตุผลการยกเลิกเอกสาร')
    return
  }

  confirmAction('cancel')
}
const isCancelled = computed(() => props.document.status === 'cancelled')


/* =======================
   submit
======================= */
const confirmAction = (type) => {
  console.log('ACTION TYPE =', type)
  if (loading.value) return
  loading.value = true

const routes = {
  send: 'documents.submit',
  approve: 'documents.approve',
  reject: 'documents.reject',
  distribute: 'documents.distribute',
  cancel: 'documents.cancel', // ⬅️ เพิ่ม
}

  router.post(
    route(routes[type], props.document.id),
    { remark: remark.value },
    {
      preserveScroll: true,
      onFinish: () => {
        loading.value = false
        remark.value = ''
      },
    }
  )
}


</script>

<template>
  <div
v-if="props.document.status !== 'cancelled' &&
     (canSendToDirector || canApprove || canReject || canDistribute || canCancel)"
    class="bg-white p-4 rounded shadow space-y-3"
  >
    <h3 class="font-semibold text-sm text-gray-700">
      การดำเนินการ
    </h3>

    <textarea
  v-model="remark"
  class="w-full border rounded p-2"
  placeholder="หมายเหตุ / ความเห็น"
/>

    <div class="flex gap-2 flex-wrap">
      <button
  v-if="canSendToDirector"
  class="px-4 py-2 bg-blue-600 text-white rounded"
  :disabled="loading"
  @click="confirmAction('send')"
>
  เสนอผู้บริหาร
</button>

<button
  v-if="canApprove"
  class="px-4 py-2 bg-green-600 text-white rounded"
  :disabled="loading"
  @click="confirmAction('approve')"
>
  อนุมัติ
</button>

<button
  v-if="canReject"
  class="px-4 py-2 bg-red-600 text-white rounded"
  :disabled="!remark.trim() || loading"
  @click="confirmAction('reject')"
>
  ตีกลับ
</button>
<button
  v-if="props.canCancel"
  class="px-4 py-2 bg-gray-700 text-white rounded"
  :disabled="loading"
  @click="confirmCancel"
>
  ยกเลิกเอกสาร
</button>

    </div>
  </div>

  <!-- confirm modal -->
  <div
    v-if="showModal"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg w-full max-w-md p-6 space-y-4">
      <h3 class="text-lg font-semibold">
        ยืนยันการดำเนินการ
      </h3>

      <p class="text-sm text-gray-600">
        ต้องการดำเนินการนี้ใช่หรือไม่
      </p>

      <div class="flex justify-end gap-2">
        <button class="px-4 py-2 border rounded" @click="closeModal">
          ยกเลิก
        </button>

        <button
          class="px-4 py-2 bg-blue-600 text-white rounded"
          @click="confirmAction"
        >
          ยืนยัน
        </button>
      </div>
    </div>
  </div>
  <p
  v-if="props.document.status === 'cancelled'"
  class="text-sm text-red-600"
>
  เอกสารฉบับนี้ถูกยกเลิกแล้ว ไม่สามารถดำเนินการต่อได้
</p>

</template>
