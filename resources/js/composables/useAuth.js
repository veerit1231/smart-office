import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export default function useAuth() {
  const page = usePage()

  const user = computed(() => page.props.auth?.user ?? null)

  const isAdmin = computed(() => {
    console.log('AUTH USER:', user.value)
    return user.value?.role === 'admin'
  })

  return { user, isAdmin }
}
