<script setup>
defineProps({
  summary: Object,
})
</script>

<template>
  <div class="grid grid-cols-2 md:grid-cols-6 gap-4">

    <div class="bg-white p-4 rounded shadow text-center">
      <div class="text-xs text-gray-500">ทั้งหมด</div>
      <div class="text-xl font-bold">{{ summary.total }}</div>
    </div>

    <div class="bg-blue-50 p-4 rounded text-center">
      <div class="text-xs">รับนอก</div>
      <div class="text-xl font-bold">{{ summary.incoming }}</div>
    </div>

    <div class="bg-green-50 p-4 rounded text-center">
      <div class="text-xs">รับใน</div>
      <div class="text-xl font-bold">{{ summary.internal }}</div>
    </div>

    <div class="bg-green-100 p-4 rounded text-center">
      <div class="text-xs">อนุมัติ</div>
      <div class="text-xl font-bold">{{ summary.approved }}</div>
    </div>

    <div class="bg-red-100 p-4 rounded text-center">
      <div class="text-xs">ไม่อนุมัติ</div>
      <div class="text-xl font-bold">{{ summary.rejected }}</div>
    </div>

    <div class="bg-yellow-100 p-4 rounded text-center">
      <div class="text-xs">รอดำเนินการ</div>
      <div class="text-xl font-bold">{{ summary.waiting }}</div>
    </div>

  </div>
</template>
