<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  document: Object,
  users: Array,
})

const selectedUsers = ref([])
const remark = ref('')
const loading = ref(false)

const submit = () => {
  if (selectedUsers.value.length === 0) {
    alert('กรุณาเลือกผู้รับอย่างน้อย 1 คน')
    return
  }

  loading.value = true

  router.post(
    route('documents.distribute', props.document.id),
    {
      user_ids: selectedUsers.value,
      remark: remark.value,
    },
    {
      onFinish: () => (loading.value = false),
    }
  )
}
</script>

<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-lg p-6 space-y-4">

      <h3 class="text-lg font-semibold">
        แจกจ่ายเอกสาร: {{ document.title }}
      </h3>

      <!-- user list -->
      <div class="max-h-60 overflow-y-auto border rounded p-2 space-y-1">
        <label
          v-for="u in users"
          :key="u.id"
          class="flex items-center gap-2 text-sm"
        >
          <input
            type="checkbox"
            :value="u.id"
            v-model="selectedUsers"
          />
          {{ u.name }}
        </label>
      </div>

      <textarea
        v-model="remark"
        placeholder="หมายเหตุ (ถ้ามี)"
        class="w-full border rounded p-2"
      />

      <div class="flex justify-end gap-2">
        <button
          class="px-4 py-2 border rounded"
          @click="$emit('close')"
        >
          ยกเลิก
        </button>

        <button
          class="px-4 py-2 bg-indigo-600 text-white rounded"
          :disabled="loading"
          @click="submit"
        >
          ยืนยันแจกจ่าย
        </button>
      </div>
    </div>
  </div>
</template>
