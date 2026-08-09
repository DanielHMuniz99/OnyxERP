<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import ContentView from './dashboard/ContentView.vue'
import CustomersView from './dashboard/customers/CustomersView.vue'
import FooterView from './dashboard/FooterView.vue'
import HeaderView from './dashboard/HeaderView.vue'
import InventoryView from './dashboard/inventory/InventoryView.vue'
import SidebarView from './dashboard/SidebarView.vue'
import type { CustomerRouteMode } from './dashboard/customers/types'
import type { InventoryRouteMode } from './dashboard/inventory/types'

type DashboardSection = 'overview' | 'customers' | 'inventory'
type DashboardRoute = {
  section: DashboardSection
  customerMode?: CustomerRouteMode
  customerId?: number | null
  inventoryMode?: InventoryRouteMode
  inventoryId?: number | null
}

defineProps<{
  user: {
    name: string
    email: string
    role?: string | null
  }
  token: string
}>()

const emit = defineEmits<{
  logout: []
}>()

function parseRoute(pathname: string): DashboardRoute {
  if (pathname === '/clientes') {
    return { section: 'customers', customerMode: 'list', customerId: null }
  }

  if (pathname === '/clientes/novo') {
    return { section: 'customers', customerMode: 'create', customerId: null }
  }

  const editMatch = pathname.match(/^\/clientes\/(\d+)\/editar$/)
  if (editMatch) {
    return {
      section: 'customers',
      customerMode: 'edit',
      customerId: Number(editMatch[1]),
    }
  }

  if (pathname === '/estoque') {
    return { section: 'inventory', inventoryMode: 'list', inventoryId: null }
  }

  if (pathname === '/estoque/novo') {
    return { section: 'inventory', inventoryMode: 'create', inventoryId: null }
  }

  const inventoryEditMatch = pathname.match(/^\/estoque\/(\d+)\/editar$/)
  if (inventoryEditMatch) {
    return {
      section: 'inventory',
      inventoryMode: 'edit',
      inventoryId: Number(inventoryEditMatch[1]),
    }
  }

  return { section: 'overview' }
}

const currentRoute = ref<DashboardRoute>(parseRoute(window.location.pathname))

function navigateTo(pathname: string): void {
  if (window.location.pathname === pathname) {
    currentRoute.value = parseRoute(pathname)
    return
  }

  window.history.pushState({}, '', pathname)
  currentRoute.value = parseRoute(pathname)
}

function handlePopState(): void {
  currentRoute.value = parseRoute(window.location.pathname)
}

function handleSidebarNavigate(section: DashboardSection): void {
  if (section === 'customers') {
    navigateTo('/clientes')
    return
  }

  if (section === 'inventory') {
    navigateTo('/estoque')
    return
  }

  navigateTo('/')
}

onMounted(() => {
  window.addEventListener('popstate', handlePopState)
})

onBeforeUnmount(() => {
  window.removeEventListener('popstate', handlePopState)
})

const sectionMeta = computed(() => {
  if (currentRoute.value.section === 'customers') {
    if (currentRoute.value.customerMode === 'create') {
      return {
        label: 'Clientes',
        title: 'Novo cliente',
      }
    }

    if (currentRoute.value.customerMode === 'edit') {
      return {
        label: 'Clientes',
        title: 'Editar cliente',
      }
    }

    return {
      label: 'Clientes',
      title: 'Relacionamento comercial',
    }
  }

  if (currentRoute.value.section === 'inventory') {
    if (currentRoute.value.inventoryMode === 'create') {
      return {
        label: 'Estoque',
        title: 'Novo item',
      }
    }

    if (currentRoute.value.inventoryMode === 'edit') {
      return {
        label: 'Estoque',
        title: 'Editar item',
      }
    }

    return {
      label: 'Estoque',
      title: 'Gestao de estoque',
    }
  }

  return {
    label: 'Painel inicial',
    title: 'Bem-vindo',
  }
})
</script>

<template>
  <main class="dashboard-layout">
    <SidebarView :active-section="currentRoute.section" @navigate="handleSidebarNavigate" />

    <section class="dashboard-shell">
      <HeaderView
        :user-name="user.name"
        :section-label="sectionMeta.label"
        :section-title="sectionMeta.title"
        @logout="emit('logout')"
      />
      <CustomersView
        v-if="currentRoute.section === 'customers'"
        :token="token"
        :mode="currentRoute.customerMode || 'list'"
        :customer-id="currentRoute.customerId || null"
        @navigate="navigateTo"
      />
      <InventoryView
        v-else-if="currentRoute.section === 'inventory'"
        :token="token"
        :mode="currentRoute.inventoryMode || 'list'"
        :inventory-id="currentRoute.inventoryId || null"
        @navigate="navigateTo"
      />
      <ContentView
        v-else
        :user-name="user.name"
        :user-email="user.email"
        :user-role="user.role || 'operator'"
      />
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
