<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { apiUrl } from '@/config/api'
import InventoryFormView from './InventoryFormView.vue'
import InventoryListView from './InventoryListView.vue'
import type {
  ApiError,
  Inventory,
  InventoryFormData,
  InventoryListResponse,
  InventoryMutationResponse,
  InventoryRouteMode,
  InventoryShowResponse,
} from './types'

const props = defineProps<{
  token: string
  mode: InventoryRouteMode
  inventoryId: number | null
}>()

const emit = defineEmits<{
  navigate: [path: string]
}>()

const inventories = ref<Inventory[]>([])
const selectedInventory = ref<Inventory | null>(null)
const isLoading = ref(false)
const isSubmitting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const formTitle = computed(() => {
  return selectedInventory.value
    ? 'Atualize os dados do item selecionado na tabela.'
    : 'Cadastre itens e acompanhe os niveis de estoque do sistema.'
})

const isFormMode = computed(() => props.mode === 'create' || props.mode === 'edit')

function getAuthHeaders(token: string): HeadersInit {
  return {
    Accept: 'application/json',
    Authorization: `Bearer ${token}`,
    'Content-Type': 'application/json',
  }
}

function getErrorMessage(payload: ApiError, fallback: string): string {
  const firstErrorList = payload.errors ? Object.values(payload.errors)[0] : null
  const firstError = firstErrorList && firstErrorList.length > 0 ? firstErrorList[0] : null

  return firstError || payload.message || fallback
}

async function fetchInventories(): Promise<void> {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(apiUrl('/inventories'), {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${props.token}`,
      },
    })

    if (!response.ok) {
      throw new Error('Nao foi possivel carregar os itens de estoque.')
    }

    const payload = (await response.json()) as InventoryListResponse
    inventories.value = payload.inventories
  } catch (error) {
    errorMessage.value = error instanceof Error
      ? error.message
      : 'Erro inesperado ao carregar estoque.'
  } finally {
    isLoading.value = false
  }
}

async function fetchInventory(inventoryId: number): Promise<void> {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(apiUrl(`/inventories/${inventoryId}`), {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${props.token}`,
      },
    })

    if (!response.ok) {
      throw new Error('Nao foi possivel carregar o item selecionado.')
    }

    const payload = (await response.json()) as InventoryShowResponse
    selectedInventory.value = payload.inventory
  } catch (error) {
    errorMessage.value = error instanceof Error
      ? error.message
      : 'Erro inesperado ao carregar item de estoque.'
  } finally {
    isLoading.value = false
  }
}

async function saveInventory(formData: InventoryFormData): Promise<void> {
  isSubmitting.value = true
  successMessage.value = ''
  errorMessage.value = ''

  const isEditing = Boolean(selectedInventory.value)
  const endpoint = selectedInventory.value
    ? apiUrl(`/inventories/${selectedInventory.value.id}`)
    : apiUrl('/inventories')
  const method = selectedInventory.value ? 'PUT' : 'POST'

  try {
    const response = await fetch(endpoint, {
      method,
      headers: getAuthHeaders(props.token),
      body: JSON.stringify(formData),
    })

    const payload = (await response.json()) as InventoryMutationResponse | ApiError

    if (!response.ok) {
      throw new Error(getErrorMessage(payload as ApiError, 'Nao foi possivel salvar o item de estoque.'))
    }

    const successPayload = payload as InventoryMutationResponse

    if (isEditing && selectedInventory.value) {
      inventories.value = inventories.value.map((inventory) => {
        return inventory.id === selectedInventory.value?.id ? successPayload.inventory : inventory
      })
    } else {
      inventories.value = [successPayload.inventory, ...inventories.value]
    }

    successMessage.value = successPayload.message
    selectedInventory.value = null
    emit('navigate', '/estoque')
  } catch (error) {
    errorMessage.value = error instanceof Error
      ? error.message
      : 'Erro inesperado ao salvar item de estoque.'
  } finally {
    isSubmitting.value = false
  }
}

function editInventory(inventory: Inventory): void {
  successMessage.value = ''
  errorMessage.value = ''
  emit('navigate', `/estoque/${inventory.id}/editar`)
}

function openCreate(): void {
  selectedInventory.value = null
  successMessage.value = ''
  errorMessage.value = ''
  emit('navigate', '/estoque/novo')
}

function goToList(): void {
  selectedInventory.value = null
  successMessage.value = ''
  errorMessage.value = ''
  emit('navigate', '/estoque')
}

watch(
  () => [props.mode, props.inventoryId, props.token] as const,
  ([mode, inventoryId]) => {
    if (!props.token) {
      return
    }

    if (mode === 'list') {
      selectedInventory.value = null
      void fetchInventories()
      return
    }

    if (mode === 'create') {
      selectedInventory.value = null
      return
    }

    if (mode === 'edit' && inventoryId) {
      void fetchInventory(inventoryId)
    }
  },
  { immediate: true },
)

onMounted(() => {
  if (props.token && props.mode === 'list') {
    void fetchInventories()
  }
})
</script>

<template>
  <section class="inventory-view">
    <div class="hero-panel">
      <div>
        <p class="hero-label">Estoque</p>
        <h2>Entradas, saidas e saldo de produtos no mesmo padrao do sistema.</h2>
        <p class="hero-text">
          Controle itens, quantidades minimas e precos com a mesma arquitetura dos outros modulos.
        </p>
      </div>
      <div class="hero-badge">
        <span>Total em base</span>
        <strong>{{ inventories.length }}</strong>
      </div>
    </div>

    <div v-if="isFormMode" class="single-column">
      <section class="form-column">
        <div class="context-card">
          <span class="context-label">Cadastro</span>
          <p>{{ formTitle }}</p>
        </div>

        <InventoryFormView
          :inventory="selectedInventory"
          :is-submitting="isSubmitting"
          @submit="saveInventory"
          @back="goToList"
        />

        <p v-if="successMessage" class="form-feedback success">{{ successMessage }}</p>
        <p v-if="errorMessage" class="form-feedback error">{{ errorMessage }}</p>
      </section>
    </div>

    <div v-else class="single-column">
      <InventoryListView
        :inventories="inventories"
        :is-loading="isLoading"
        @create="openCreate"
        @edit="editInventory"
        @refresh="fetchInventories"
      />
    </div>
  </section>
</template>

<style scoped>
.inventory-view {
  display: grid;
  gap: 1.25rem;
  padding: 1.5rem 1.75rem 2rem;
}

.hero-panel {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.5rem;
  border-radius: 24px;
  background: linear-gradient(135deg, #3e2f0d 0%, #b88315 100%);
  color: #fff8ef;
}

.hero-label {
  margin: 0 0 0.45rem;
  color: #ffe0a1;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.hero-panel h2 {
  margin: 0;
  max-width: 20ch;
  font-size: 1.9rem;
  line-height: 1.05;
}

.hero-text {
  margin: 0.8rem 0 0;
  color: #fff0d1;
}

.hero-badge {
  min-width: 180px;
  padding: 1rem 1.1rem;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.14);
}

.hero-badge span {
  display: block;
  margin-bottom: 0.4rem;
  color: #ffefc7;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.hero-badge strong {
  font-size: 1.5rem;
}

.single-column {
  display: grid;
  width: 100%;
}

.form-column {
  display: grid;
  width: 100%;
  align-content: start;
  gap: 1rem;
}

.context-card {
  padding: 1rem 1.1rem;
  border-radius: 18px;
  background: #fff7e8;
  border: 1px solid #f5e0b8;
}

.context-label {
  display: inline-block;
  margin-bottom: 0.45rem;
  color: #8d6b1a;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.context-card p {
  margin: 0;
  color: #5b4a23;
}

@media (max-width: 720px) {
  .hero-panel {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
