<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user
</script>

<template>
  <div class="min-h-screen bg-slate-100 flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-gradient-to-b from-blue-700 to-blue-800 text-white hidden md:flex flex-col shadow-lg">

      <!-- Logo -->
      <div class="h-16 flex items-center px-6 border-b border-blue-600 font-bold text-lg tracking-wide">
        Smart Office
      </div>

      <!-- Menu -->
      <nav class="flex-1 px-3 py-4 space-y-1">
        <Link href="/documents" class="sidebar-link">
          📄 หน้าแรก
        </Link>
        <Link href="/admin/users" class="sidebar-link">
          👤 จัดการผู้ใช้งาน
        </Link>
      </nav>

      <!-- Footer -->
      <div class="px-4 py-3 text-xs text-blue-200 border-t border-blue-600">
        v1.0 Smart Office
      </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col">

      <!-- Topbar -->
      <header class="h-16 bg-white/90 backdrop-blur border-b flex items-center justify-between px-6 shadow-sm">
        <h1 class="text-lg font-semibold text-gray-800">
          <slot name="title" />
        </h1>

        <div class="flex items-center gap-4 text-sm text-gray-600">
          <span class="font-medium">{{ user.name }}</span>
          <Link
            href="/logout"
            method="post"
            as="button"
            class="text-red-500 hover:underline"
          >
            Logout
          </Link>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>

    </div>
  </div>
</template>

<style scoped>
.sidebar-link {
  @apply flex items-center gap-2 px-3 py-2 rounded-md
         text-blue-100 hover:bg-blue-600 hover:text-white
         transition;
}
</style>
