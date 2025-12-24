<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: String,
})

const statusConfig = {
  draft: {
    label: 'ร่าง',
    class: 'bg-gray-100 text-gray-700',
  },
  received: {
    label: 'รับแล้ว',
    class: 'bg-blue-100 text-blue-700',
  },
  waiting_director: {
    label: 'รอผู้บริหาร',
    class: 'bg-yellow-100 text-yellow-800',
  },
  approved: {
    label: 'อนุมัติ',
    class: 'bg-green-100 text-green-700',
  },
  rejected: {
    label: 'ตีกลับ',
    class: 'bg-orange-100 text-orange-700',
  },

  // ⭐ ตัวเอก
  cancelled: {
    label: 'ยกเลิก',
    class: 'bg-red-100 text-red-700 border border-red-300',
  },
}

const badge = computed(() => {
  return statusConfig[props.status] ?? {
    label: props.status,
    class: 'bg-gray-100 text-gray-700',
  }
})
</script>


<template>
  <span
    class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold"
    :class="badge.class"
  >
    <span v-if="props.status === 'cancelled'">❌</span>
    {{ badge.label }}
  </span>
</template>

