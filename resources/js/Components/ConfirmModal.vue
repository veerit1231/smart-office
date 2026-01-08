<script setup>
defineProps({
  show: Boolean,
  title: String,
  message: String,
})

defineEmits(['confirm', 'cancel'])
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm space-y-4">
      <h2 class="font-semibold text-lg">{{ title }}</h2>
      <p class="text-gray-600">{{ message }}</p>

      <div class="flex justify-end gap-2">
        <button
          class="px-4 py-2 rounded bg-gray-200"
          @click="$emit('cancel')"
        >
          ยกเลิก
        </button>
        <button
          class="px-4 py-2 rounded bg-red-600 text-white"
          @click="$emit('confirm')"
        >
          ยืนยัน
        </button>
      </div>
    </div>
  </div>
</template>
