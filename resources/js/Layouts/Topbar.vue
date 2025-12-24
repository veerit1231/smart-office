<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()

// auth user
const user = computed(() => page.props.auth?.user)

// notifications จาก Inertia::share
const notifications = computed(() => page.props.notifications || [])

// นับเฉพาะ unread
const unreadCount = computed(() =>
  notifications.value.filter(n => !n.read_at).length
)

// dropdown state
const open = ref(false)

function logout() {
  router.post(route('logout'))
}

// ✅ mark as read + redirect
function markAsRead(notification) {
  router.post(
    route('notifications.read', notification.id),
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        open.value = false
        router.visit(notification.data.url)
      }
    }
  )
}
</script>

<template>
  <header class="h-14 bg-white border-b flex items-center justify-between px-6">
    <!-- Left -->
    <h1 class="font-semibold text-gray-700">
      Smart Office
    </h1>

    <!-- Right -->
    <div class="flex items-center gap-6" v-if="user">
      <!-- 🔔 Notification -->
      <div class="relative">
        <button @click="open = !open" class="relative focus:outline-none">
          🔔
          <span v-if="unreadCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">
            {{ unreadCount }}
          </span>
        </button>

        <!-- Dropdown -->
        <div v-if="open" class="absolute right-0 mt-2 w-80 bg-white border rounded shadow z-50">
          <div class="px-3 py-2 font-semibold border-b">
            Notifications
          </div>

          <div v-if="notifications.length">
            <div v-for="n in notifications" :key="n.id" @click="markAsRead(n)" :class="[
              'px-3 py-2 cursor-pointer hover:bg-gray-100',
              !n.read_at ? 'bg-blue-50' : ''
            ]">
              <div class="font-medium text-sm">
                {{ n.title }}
              </div>
              <div class="text-sm text-gray-600">
                {{ n.message }}
              </div>
              <div class="text-xs text-gray-400 mt-1">
                {{ n.created_at }}
              </div>
            </div>
          </div>

          <div v-else class="px-3 py-4 text-sm text-gray-500 text-center">
            ไม่มีการแจ้งเตือน
          </div>
        </div>
      </div>

      <!-- User name -->
      <span class="text-sm text-gray-600">
        {{ user.name }}
      </span>

      <!-- Logout -->
      <button class="text-sm text-red-600 hover:underline" @click="logout">
        Logout
      </button>
    </div>
  </header>
</template>
