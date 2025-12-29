<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

/**
 * ✅ DEFINE PROPS ให้ Vue รู้จักก่อนใช้งาน
 */
const props = defineProps({
  latestBackup: {
    type: Object,
    default: null,
  },
})

/**
 * (optional) ใช้ computed เพื่อความชัวร์
 */
const latest = computed(() => props.latestBackup)

/**
 * Run backup
 */
const runBackup = () => {
  if (!confirm('Run backup now?')) return

  router.post(route('admin.backup.run'), {}, {
    preserveState: false, // ⭐ บังคับ reset state
  })
}

</script>

<template>
  <AdminLayout title="System Backup">
    <h1 class="text-xl font-bold mb-4">System Backup</h1>

    <div class="bg-white p-4 rounded shadow space-y-3">
      <!-- ✅ มี backup -->
      <div v-if="latest">
        <p><b>Latest backup:</b> {{ latest.name }}</p>
        <p>Size: {{ latest.size }} MB</p>
        <p>Created: {{ latest.created_at }}</p>

        <a
          :href="route('admin.backup.download')"
          class="text-blue-600 underline"
        >
          Download Backup
        </a>
      </div>

      <!-- ❌ ยังไม่มี backup -->
      <div v-else class="text-gray-500">
        No backup found
      </div>

      <button
        @click="runBackup"
        class="px-4 py-2 bg-green-600 text-white rounded"
      >
        Backup Now
      </button>
    </div>
  </AdminLayout>
</template>
