<script setup lang="ts">
import ContentView from './dashboard/ContentView.vue'
import FooterView from './dashboard/FooterView.vue'
import HeaderView from './dashboard/HeaderView.vue'
import SidebarView from './dashboard/SidebarView.vue'

defineProps<{
  user: {
    name: string
    email: string
    role?: string | null
  }
}>()

const emit = defineEmits<{
  logout: []
}>()
</script>

<template>
  <main class="dashboard-layout">
    <SidebarView />

    <section class="dashboard-shell">
      <HeaderView :user-name="user.name" @logout="emit('logout')" />
      <ContentView :user-name="user.name" :user-email="user.email" :user-role="user.role || 'operator'" />
      <FooterView />
    </section>
  </main>
</template>

<style scoped>
.dashboard-layout {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 280px 1fr;
  background: #eef3f8;
}

.dashboard-shell {
  display: grid;
  grid-template-rows: auto 1fr auto;
}

@media (max-width: 980px) {
  .dashboard-layout {
    grid-template-columns: 1fr;
  }
}
</style>
