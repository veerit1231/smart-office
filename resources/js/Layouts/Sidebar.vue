<script setup>
import { Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import useAuth from '@/composables/useAuth'
import { router } from '@inertiajs/vue3'

const { user, isAdmin } = useAuth()

/**
 * Role helpers
 * admin เห็นทุกเมนู
 */
const isUser = computed(() =>
  user.value?.role === 'user' || isAdmin.value
)

const isClerk = computed(() =>
  user.value?.role === 'clerk' || isAdmin.value
)

const isDirector = computed(() =>
  user.value?.role === 'director' || isAdmin.value
)

const openReport = ref(false)

/**
 * TODO: ต่อ backend จริงภายหลัง
 */
const pendingCount = 0
const notifyCount = 1204
</script>

<template>
  <aside class="w-64 bg-gray-900 text-gray-200 min-h-screen p-4 text-sm">

    <!-- ================================================= -->
    <!-- Header -->
    <!-- ================================================= -->

    <div class="mb-6">
      <button type="button" @click="router.visit(route('documents.index'))"
        class="flex items-center gap-3 text-left hover:opacity-80 focus:outline-none">
        <div class="font-bold text-lg text-white">
          Smart Office
        </div>
        <div class="text-xs text-gray-400">
          @StsTak
        </div>
      </button>
    </div>

    <!-- ================================================= -->
    <!-- 📥 Incoming (Clerk / Admin) -->
    <!-- ================================================= -->
    <li v-if="isClerk">
      <Link href="/documents/incoming/create" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-800">
        📄 บันทึกเอกสารรับนอก
      </Link>
    </li>

    <!-- ================================================= -->
    <!-- 📄 Internal (User / Admin) -->
    <!-- ================================================= -->
    <li v-if="isUser">
      <Link href="/documents/create" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-800">
        📄 บันทึกเอกสารรับใน
      </Link>
    </li>

    <!-- ================================================= -->
    <!-- 🔍 Search -->
    <!-- ================================================= -->
    <li>
      <Link href="/documents" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-800">
        🔍 ค้นหา
      </Link>
    </li>

    <!-- ================================================= -->
    <!-- 📌 Pending -->
    <!-- ================================================= -->
    <li>
      <Link href="/documents/index" class="flex items-center justify-between px-3 py-2 rounded hover:bg-gray-800">
        <span>⏳ รอดำเนินการ</span>
        <span class="text-red-400">
          ({{ pendingCount }})
        </span>
      </Link>
    </li>

    <!-- ================================================= -->
    <!-- 📝 Orders (Director / Admin) -->
    <!-- ================================================= -->
    <li v-if="isDirector">
      <span class="flex items-center gap-2 px-3 py-2 text-gray-400">
        📝 คำสั่งเพิ่มเติม
      </span>
    </li>

    <!-- ================================================= -->
    <!-- 👥 Personnel (Admin ONLY) -->
    <!-- ================================================= -->
    <li v-if="isAdmin">
      <Link href="/admin/users" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-800">
        👥 บุคลากร
      </Link>
    </li>

    <!-- ================================================= -->
    <!-- 🏢 Departments (Admin ONLY) -->
    <!-- ================================================= -->
    <li v-if="isAdmin">
  <Link href="/departments" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-800">
    🏢 หน่วยงาน
  </Link>
</li>

    <!-- ================================================= -->
    <!-- 📊 Reports -->
    <!-- ================================================= -->
    <li>
      <button @click="openReport = !openReport"
        class="w-full flex items-center justify-between px-3 py-2 rounded hover:bg-gray-800">
        <span>📊 รายงาน</span>
        <span>{{ openReport ? '▾' : '▸' }}</span>
      </button>

      <ul v-if="openReport" class="ml-4 mt-1 space-y-1 text-gray-400">
        <li>
          <Link href="/reports/documents" class="block px-3 py-1 hover:text-white">
            รายงานเอกสาร
          </Link>
        </li>
      </ul>
    </li>

    <!-- ================================================= -->
    <!-- 🔔 Notifications -->
    <!-- ================================================= -->
    <li>
      <Link href="/notifications" class="flex items-center justify-between px-3 py-2 rounded hover:bg-gray-800">
        <span>🔔 แจ้งเตือน</span>
        <span class="text-red-500">
          ({{ notifyCount }})
        </span>
      </Link>
    </li>


  </aside>
</template>
