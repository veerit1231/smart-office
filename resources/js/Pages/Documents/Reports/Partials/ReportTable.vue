<script setup>
defineProps({
  documents: Array,
})
</script>

<template>
  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 text-left">เลขที่</th>
          <th class="p-2 text-left">เรื่อง</th>
          <th class="p-2">ประเภท</th>
          <th class="p-2">กลุ่มงาน</th>
          <th class="p-2">สถานะ</th>
          <th class="p-2">วันที่</th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="doc in documents"
          :key="doc.id"
          class="border-t hover:bg-gray-50"
        >
          <td class="p-2">{{ doc.number }}</td>
          <td class="p-2">{{ doc.title }}</td>
          <td class="p-2 text-center">
            {{ doc.type === 'incoming' ? 'รับนอก' : 'รับใน' }}
          </td>
          <td class="p-2 text-center">
            {{ doc.department?.name || '-' }}
          </td>
          <td class="p-2 text-center">
            {{ doc.status }}
          </td>
          <td class="p-2 text-center">
            {{ doc.created_at }}
          </td>
        </tr>

        <tr v-if="documents.length === 0">
          <td colspan="6" class="p-4 text-center text-gray-400">
            ไม่พบข้อมูล
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
